<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RingleSoft\JasminClient\Contracts\JasminHttpContract;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;
use RingleSoft\JasminClient\Models\IncomingMessage;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;
use RingleSoft\JasminClient\Validators\HttpMessageValidator;

class HttpService implements JasminHttpContract
{
    private string $url;
    private string $username;
    private string $password;

    public function __construct(?string $username = null, ?string $password = null, ?string $url = null)
    {
        $this->url = $url ?? Config::get('jasmin_client.url');
        $this->username = $username ?? Config::get('jasmin_client.username');
        $this->password = $password ?? Config::get('jasmin_client.password');
    }


    private function makeHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }


    /**
     * Send a single message
     * @param string $content
     * @param string $to
     * @param string $from
     * @param string $coding
     * @param int $priority
     * @param string|null $sdt
     * @param string|null $validityPeriod
     * @param string $dlr
     * @param string $dlrUrl
     * @param int $dlrLevel
     * @param string $dlrMethod
     * @param string|null $tags
     * @param string|null $hexContent
     * @param bool|null $asBinary
     * @return JasminResponse
     * @throws JasminClientException
     * @throws ValidationException
     */
    public function sendMessage(
        string $content,
        string $to,
        string $from,
        string $coding,
        int $priority,
        ?string $sdt,
        ?string $validityPeriod,
        string $dlr,
        string $dlrUrl,
        int $dlrLevel,
        string $dlrMethod,
        ?string $tags,
        ?string $hexContent,
        ?bool $asBinary = false): JasminResponse
    {
        $url = $this->url . '/send';
        $data = [
            "content" => $content,
            'to' => $to,
            'from' => $from,
            'coding' => $coding,
            'priority' => $priority,
            'sdt' => $sdt,
            'validity-period' => $validityPeriod,
            'dlr' => $dlr,
            'dlr-url' => $dlrUrl,
            'dlr-level' =>  $dlrLevel,
            'dlr-method' => $dlrMethod,
            'tags' => $tags,
            'hex-content' => $hexContent,
            'username' => $this->username,
            'password' => $this->password,
        ];

        $data = array_filter($data);

        $validator = HttpMessageValidator::validate($data);

        if ($validator->fails()) {
            Log::error("JasminClient: Data validation failed for http message");
            throw ValidationException::withMessages($validator->errors()->all());
        }

        try {
            $response = Http::withHeaders($this->makeHeaders())->post($url, $data);
        } catch (ConnectionException $e) {
            Log::debug($e);
            throw JasminClientException::from($e);
        }
        return JasminResponse::from($response);
    }

    /**
     * Check the balance of the account
     * @return JasminResponse
     * @throws JasminClientException
     */
    public function checkBalance(): JasminResponse
    {
        $url = $this->url . '/balance';
        $data = [
            "username" => $this->username,
            "password" => $this->password
        ];
        try {
            $response = Http::withHeaders($this->makeHeaders())->get($url, $data);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminResponse::from($response);
    }

    /**
     * @param string|null $to
     * @param string|null $from
     * @param string|null $coding
     * @param string|null $content
     * @return JasminResponse
     * @throws JasminClientException
     */
    public function checkRoute(?string $to, ?string $from = null, ?string $coding = null, ?string $content = null): JasminResponse
    {
        $url = $this->url . '/rate';
        $data = [
            "to" => $to,
            "from" => $from,
            "coding" => $coding,
            "content" => $content,
            "username" => $this->username,
            "password" => $this->password
        ];
        try {
            $response = Http::withHeaders($this->makeHeaders())->get($url, array_filter($data));
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminResponse::from($response);
    }


    /**
     * @return JasminResponse
     * @throws JasminClientException
     */
    public function getMetrics(): JasminResponse
    {
        $url = $this->url . '/metrics';
        try {
            $response = Http::withHeaders($this->makeHeaders())->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminResponse::from($response);
    }


    /**
     * @param Request $request
     * @param callback(IncomingMessage $message): bool $callback
     * @return Response
     */
    public function receiveMessage(Request $request, callable $callback): Response
    {
        $rules = [
            'id' => ['required', 'string'],
            'from' => ['required'],
            'to' => ['required'],
            'origin-connector' => ['required'],
            'priority' => ['nullable'],
            'coding' => ['nullable'],
            'validity' => ['nullable'],
            'content' => ['nullable'],
            'binary' => ['nullable'],
        ];

        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            Log::info("Invalid request received from jasmin");
            return new Response("Invalid Request", 400);
        }
        $IncomingMessage = new IncomingMessage(
            id: $request->input('id'),
            from: $request->input('from'),
            to: $request->input('to'),
            originConnector: $request->input('origin-connector'),
            priority: $request->input('priority'),
            coding: $request->input('coding'),
            validity: $request->input('validity'),
            message: $request->input('content'),
            binary: $request->input('binary')
        );
        if ($callback($IncomingMessage)) {
            return new Response("ACK/Jasmin", 200);
        }
        return new Response("FAIL/Jasmin", 400);
    }

    /**
     * @param Request $request
     * @param callback(DeliveryCallback $dlr): bool $callback
     * @return Response
     */
    public function receiveDlrCallback(Request $request, callable $callback): Response
    {
        $validator = Validator::make($request->input(), DeliveryCallback::rules());
        if ($validator->fails()) {
            Log::info("JasminClient: Invalid request received from jasmin");
            Log::debug($validator->errors()->all());
            return new Response("Invalid Request", 400);
        }
        $dlr = new DeliveryCallback(
            id: $request->input('id'),
            messageStatus: $request->input('message_status'),
            level: $request->input('level'),
            connector: $request->input('connector'),
            smscId: $request->input('smsc-id'),
            submittedDate: $request->input('subdate'),
            doneDate: $request->input('donedate'),
            submittedCount: $request->input('sub'),
            deliveredCunt: $request->input('dlvrd'),
            error: $request->input('err'),
            text: $request->input('text')
        );
        if ($callback($dlr)) {
            return new Response("ACK/Jasmin", 200, ['Content-Type' => 'text/plain']);
        }
        return new Response("FAIL/Jasmin", 400, ['Content-Type' => 'text/plain']);
    }
}

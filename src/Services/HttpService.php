<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use RingleSoft\JasminClient\Contracts\JasminHttpContract;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;
use RingleSoft\JasminClient\Models\IncomingMessage;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;
use RingleSoft\JasminClient\Models\Responses\JasminHttpResponse;
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
            Log::info("Data validation failed: ");
            return new JasminHttpResponse("Data validation failed",
                "failed", $validator->errors()->all());
        }

        try {
            $response = Http::withHeaders($this->makeHeaders())->post($url, $data);
        } catch (ConnectionException $e) {
            Log::debug($e);
            throw JasminClientException::from($e);
        }
        return JasminHttpResponse::from($response);
    }

    public function checkBalance(): JasminResponse
    {
        $url = $this->url . '/balance';
        try {
            $response = Http::withHeaders($this->makeHeaders())->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminHttpResponse::from($response);
    }

    public function checkRoute(?string $to): JasminResponse
    {
        $url = $this->url . '/rate';
        try {
            $response = Http::withHeaders($this->makeHeaders())->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminHttpResponse::from($response);
    }


    public function getMetrics(): JasminResponse
    {
        $url = $this->url . '/metrics';
        try {
            $response = Http::withHeaders($this->makeHeaders())->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminHttpResponse::from($response);
    }


    public function receiveMessage(Request $request, callable $callback): JsonResponse
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
            return new JsonResponse("Invalid Request", 400);
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
            return new JsonResponse("ACK/Jasmin", 200);
        }
        return new JsonResponse("NACK/Jasmin", 400);
    }

    /**
     * @param Request $request
     * @param callable $callback(DeliveryCallback $deliveryCallback)
     * @return JsonResponse
     */
    public function receiveDlrCallback(Request $request, callable $callback): JsonResponse
    {

        $validator = Validator::make($request->input(), DeliveryCallback::rules());
        if ($validator->fails()) {
            Log::info("Invalid request received from jasmin");
            return new JsonResponse("Invalid Request", 400);
        }
        $dlr = new DeliveryCallback(
            id: $request->input('id'),
            smscId: $request->input('smsc-id'),
            messageStatus: $request->input('message-status'),
            level: $request->input('level'),
            connector: $request->input('connector'),
            submittedDate: $request->input('subdate'),
            doneDate: $request->input('donedate'),
            submittedCount: $request->input('sub'),
            deliveredCunt: $request->input('dlvrd'),
            error: $request->input('err'),
            text: $request->input('text')
        );
        if ($callback($dlr)) {
            return new JsonResponse("ACK/Jasmin", 200);
        }
        return new JsonResponse("NACK/Jasmin", 400);
    }
}

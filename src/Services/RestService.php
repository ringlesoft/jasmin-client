<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use RingleSoft\JasminClient\Contracts\JasminRestContract;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Callbacks\BatchCallback;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;
use RingleSoft\JasminClient\Models\Responses\JasminRestResponse;

class RestService implements JasminRestContract
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
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
        ];
    }

    /**
     * Send a single message
     * @param string $content
     * @param string $to
     * @param string $from
     * @param string $dlr
     * @param string $dlrUrl
     * @param string $dlrLevel
     * @param bool|null $asBinary
     * @return JasminResponse
     * @throws JasminClientException
     */
    public function sendMessage(
        string $content,
        string $to,
        string $from,
        string $dlr,
        string $dlrUrl,
        string $dlrLevel,
        ?bool  $asBinary = false
    ): JasminResponse
    {
        $headers = $this->makeHeaders();
        $url = $this->url . '/secure/send';
        $data = [
            "message" => $content,
            "to" => $to,
            "from" => $from,
            "dlr" => $dlr,
            "dlr-url" => $dlrUrl,
            "dlr-level" => $dlrLevel,
        ];

        try {
            $response = Http::withHeaders($headers)->post($url,
                $data);
            dump($response->body());
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }

        return JasminRestResponse::from($response);
    }

    public function sendMultipleMessages(array $messages, ?array $globals, ?string $callbackUrl, ?string $errbackUrl, ?bool $asBinary = false): JasminResponse
    {
        $headers = $this->makeHeaders();
        $url = $this->url . '/secure/sendbatch';
        $data = [
            "messages" => $messages,
            "globals" => $globals,
            "batch_config" => ($callbackUrl || $errbackUrl) ? [
                "callback_url" => $callbackUrl,
                "errback_url" => $errbackUrl
            ] : null
        ];
        try {
            $response = Http::withHeaders($headers)->post($url, $data);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminRestResponse::from($response);
    }


    /**
     * @throws JasminClientException
     */
    public function checkBalance(): JasminResponse
    {
        $headers = $this->makeHeaders();
        $url = $this->url . '/secure/balance';
        try {
            $response = Http::withHeaders($headers)->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminRestResponse::from($response);
    }

    /**
     * @throws JasminClientException
     */
    public function checkRoute(?string $to): JasminResponse
    {
        $headers = $this->makeHeaders();
        $url = $this->url . '/secure/rate' . ($to ? "?to=$to" : '');
        try {
            $response = Http::withHeaders($headers)->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminRestResponse::from($response);
    }

    public function ping(): JasminResponse
    {
        $headers = $this->makeHeaders();
        $url = $this->url . '/secure/ping';
        try {
            $response = Http::withHeaders($headers)->get($url);
            dd($response->body());
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminRestResponse::from($response);
    }

    /**
     * @param Request $request
     * @param callable $callback (BatchCallback $batchCallback)
     * @return JsonResponse
     */
    public function receiveBatchCallback(Request $request, callable $callback): JsonResponse
    {
        $validator = Validator::make($request->input(), BatchCallback::rules());
        if ($validator->fails()) {
            Log::info("Invalid request received from jasmin");
            return new JsonResponse("Invalid Request", 400);
        }
        $batchCallback = new BatchCallback(
            batchId: $request->input('batchId'),
            to: $request->input('to'),
            status: $request->input('status'),
            statusText: $request->input('statusText')
        );
        if ($callback($batchCallback)) {
            return new JsonResponse("ACK/Jasmin", 200);
        }
        return new JsonResponse("FAIL/Jasmin", 400);
    }
}

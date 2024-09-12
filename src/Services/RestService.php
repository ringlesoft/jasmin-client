<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RingleSoft\JasminClient\Contracts\JasminRestContract;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\JasminRestResponse;

class RestService implements JasminRestContract
{
    private string $url;
    private string $username;
    private string $password;
    public function __construct(?string $username, ?string $password, ?string $url)
    {
        $this->url = $url ?? Config::get('jasmin_client.url');
        $this->username = $username ?? Config::get('jasmin_client.username');
        $this->password = $password ?? Config::get('jasmin_client.password');
    }


    private function makeHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
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
     * @return JasminRestResponse
     * @throws JasminClientException
     */
    public function sendMessage(string $content, string $to, string $from, string $dlr, string $dlrUrl, string $dlrLevel, ?bool $asBinary = false): JasminRestResponse
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
            $response = Http::withHeaders($headers)->post($url, $data);
        } catch (ConnectionException $e) {
           throw JasminClientException::from($e);
        }

        return JasminRestResponse::from($response);
    }

    public function sendMultipleMessages(array $messages, ?array $globals, ?string $callbackUrl, ?string $errbackUrl, ?bool $asBinary = false): JasminRestResponse
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
    public function checkBalance(): JasminRestResponse
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
    public function checkRoute(?string $to): JasminRestResponse
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

    public function ping(): JasminRestResponse
    {
        $headers = $this->makeHeaders();
        $url = $this->url . '/secure/ping';
        try {
            $response = Http::withHeaders($headers)->get($url);
        } catch (ConnectionException $e) {
            throw JasminClientException::from($e);
        }
        return JasminRestResponse::from($response);
    }
}

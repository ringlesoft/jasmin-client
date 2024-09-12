<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Support\Facades\Http;
use RingleSoft\JasminClient\Contracts\JasminRestContract;
use RingleSoft\JasminClient\Models\JasminRestResponse;

class RestService implements JasminRestContract
{
    private string $url;
    private string $username;
    private string $password;
    public function __construct(?string $username, ?string $password, ?string $url)
    {
        $this->url = $url ?? config('jasmin_client.url');
        $this->username = $username ?? config('jasmin_client.username');
        $this->password = $password ?? config('jasmin_client.password');
    }


    public function sendMessage(string $content, string $to, string $from, string $dlr, string $dlrUrl, string $dlrLevel): JasminRestResponse
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
        ];

        $data = [
            "message" => $content,
            "to" => $to,
            "from" => $from,
            "dlr" => $dlr,
            "dlr-url" => $dlrUrl,
            "dlr-level" => $dlrLevel,
        ];

        $response = Http::post($this->url . '/api/v1/sms/send', $data, $headers);

        return JasminRestResponse::from($response);
    }

}

<?php

namespace RingleSoft\JasminClient\Models\Responses;

use Illuminate\Http\Client\Response;

class JasminResponse
{
    public Response $response;
    public ?int $status;
    public ?array $data;

    public function __construct(Response $response, ?int $status = null, ?array $data = null)
    {
        $this->response = $response;
        $this->status = $status ?? $response->status();
        $this->data = $data ?? $response->json() ?? null;
    }

    public function isSuccessful(): bool
    {
        return $this->response->ok();
    }

    public static function from(Response $response): static
    {
        return new static($response);
    }

    private function translateEror($error): ?string {
        return '';
    }
}

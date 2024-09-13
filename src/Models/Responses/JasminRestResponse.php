<?php

namespace RingleSoft\JasminClient\Models\Responses;

use Illuminate\Http\Client\Response;

class JasminRestResponse extends JasminResponse
{

    public static function from(Response $response): self
    {
        $self = new self($response->status(), $response->body(), $response->json());
        $self->body = $response->body();
        return $self;
    }
}

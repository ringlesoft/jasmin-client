<?php

namespace RingleSoft\JasminClient\Models\Responses;

use Illuminate\Http\Client\Response;

class JasminHttpResponse extends JasminResponse
{

    public static function from(Response $response): JasminHttpResponse
    {
        $self = new self($response->status(), $response->body(), $response->json());
        $self->message = $response->body();
        $self->data = $response->json();
        $self->body = $response->body();
        return $self;
    }
}

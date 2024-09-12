<?php

namespace RingleSoft\JasminClient\Models;

use Illuminate\Http\Client\Response;

class JasminHttpResponse extends JasminResponse
{

    public static function from(Response $response): JasminHttpResponse
    {
        $self = new self($response->status(), $response->body(), $response->json());
        $self->message = $response->body();
        $self->data = $response->json();
        return $self;
    }
}

<?php

namespace RingleSoft\JasminClient\Models;

use Illuminate\Http\Client\Response;

class JasminRestResponse extends JasminResponse
{
    public $body;

    public static function from(Response $response): JasminRestResponse
    {
        $self = new self($response->status(), $response->body(), $response->json());
        $self->body = $response->body();
        return $self;
    }
}

<?php

namespace RingleSoft\JasminClient\Models;

use Illuminate\Http\Client\Response;

class JasminRestResponse
{


    public static function from(Response $response): JasminRestResponse
    {
        return new self();
    }
}

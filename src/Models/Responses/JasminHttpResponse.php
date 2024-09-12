<?php

namespace RingleSoft\JasminClient\Models;

use Illuminate\Http\Client\Response;
use RingleSoft\JasminClient\Contracts\JasminHttpContract;

class JasminHttpResponse
{


    public static function from(Response $response): JasminHttpResponse
    {
        return new self();
    }
}

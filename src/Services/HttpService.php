<?php

namespace RingleSoft\JasminClient\Services;

use RingleSoft\JasminClient\Contracts\JasminHttpContract;
use RingleSoft\JasminClient\Models\JasminHttpResponse;

class HttpService implements JasminHttpContract
{
    public function sendMessage(string $content, string $to, string $from, string $dlr, string $dlrUrl, string $dlrLevel): JasminHttpResponse
    {
        return '';
    }
}

<?php

namespace RingleSoft\JasminClient\Contracts;

use RingleSoft\JasminClient\Models\Jasmin\SentMessage;

interface JasminSmppContract
{
    public function sendMessage(string $to, string $content, string $from): SentMessage;
}

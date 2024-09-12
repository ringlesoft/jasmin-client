<?php

namespace RingleSoft\JasminClient\Contracts;

use RingleSoft\JasminClient\Models\JasminRestResponse;

interface JasminRestContract
{


    // Send a single message
    public function sendMessage(string $content, string $to, string $from, string $dlr, string $dlrUrl, string $dlrLevel): JasminRestResponse;
    // Send multiple messages
    public function sendMultipleMessages(array $messages): JasminRestResponse;
    // Send binary messages
    public function sendBinaryMessage(string $to, string $from, string $coding, string $hexContent);
    // Balance check
    public function checkBalance(string $to, string $from);
    // Route check
    // Ping
}

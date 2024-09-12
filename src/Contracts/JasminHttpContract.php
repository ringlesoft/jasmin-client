<?php

namespace RingleSoft\JasminClient\Contracts;

interface JasminHttpContract
{

    // Send a single message
    public function sendMessage(string $content, string $to, string $from, string $coding, int $priority, string $sdt, string $validityPeriod, string $dlr, string $dlrUrl, string $dlrLevel, string $dlrMethod, ?string $tags, ?string $hexContent,  ?bool $asBinary = false): JasminHttpResponse;
    // Send multiple messages
    // Send binary messages
    // Balance check
    // Route check
    // Ping
}

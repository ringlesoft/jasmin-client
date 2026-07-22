<?php

namespace RingleSoft\JasminClient\Contracts;

interface SmppTransport
{
    /**
     * @return string|array<string>
     */
    public function send(string $to, string $content, string $from): string|array;
}

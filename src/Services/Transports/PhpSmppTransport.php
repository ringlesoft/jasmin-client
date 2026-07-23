<?php

namespace RingleSoft\JasminClient\Services\Transports;

use PhpSmpp\Service\Sender;
use RingleSoft\JasminClient\Contracts\SmppTransport;

class PhpSmppTransport implements SmppTransport
{
    private Sender $sender;

    /**
     * @param array<string> $hosts
     */
    public function __construct(array $hosts, string $username, string $password)
    {
        $this->sender = new Sender($hosts, $username, $password, null);
    }

    public function send(string $to, string $content, string $from): string|array
    {
        return $this->sender->send($to, $content, $from);
    }
}

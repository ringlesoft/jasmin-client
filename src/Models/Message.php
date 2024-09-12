<?php

namespace RingleSoft\JasminClient\Models;


use RingleSoft\JasminClient\Facades\JasminClient;

class Message
{

    private bool $isBinary = false;
    public string $to;
    public string $from;
    public string $message;
    public bool $dlr = true;
    public string $dlrUrl;
    public string $dlrLevel;

    public function __construct(?string $to, ?string $from, ?string $message, ?bool $dlr, ?string $dlrUrl, ?string $dlrLevel)
    {
        $this->to = $to;
        $this->from = $from;
        $this->message = $message;
        $this->dlr = $dlr;
        $this->dlrUrl = $dlrUrl;
        $this->dlrLevel = $dlrLevel;
    }

    public function asBinary(bool $binary): self
    {
        $this->isBinary = $binary;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function to(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function from(string $from): self
    {
        $this->from = $from;
        return $this;
    }


    public function send(): JasminRestResponse
    {
        return JasminClient::http()->sendMessage($this->message, $this->to, $this->from, $this->dlr, $this->dlrUrl, $this->dlrLevel);
    }

    public function toArray(): array
    {
        return array_filter([
            "to" => $this->to,
            "from" => $this->from,
            "message" => $this->message,
            "dlr" => $this->dlr,
            "dlr_url" => $this->dlrUrl,
            "dlr_level" => $this->dlrLevel,
            "is_binary" => $this->isBinary
        ]);
    }
}

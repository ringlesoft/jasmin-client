<?php

namespace RingleSoft\JasminClient\Models;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Facades\JasminClient;

class Message
{

    private bool $isBinary = false;
    /**
     * Destination address, only one address is supported per request
     * @var string|null
     */
    public ?string $to;
    public ?string $from;
    public ?string $content;
    public bool $dlr = true;
    public ?string $dlrUrl;

    /**
     * 1: SMS-C level, 2: Terminal level, 3: Both
     * @var int|null
     */
    public ?int $dlrLevel = 2;

    private ?int $coding = 0;
    private ?int $priority = 0;
    private ?string $sdt = 'utf-8';
    private ?string $validityPeriod = '300';
    private ?string $dlrMethod = 'GET';
    private ?int $tags = null;
    private ?string $hexContent = null;

    private ?string $via;

    public function __construct(?string $to = null, ?string $from = null, ?string $content = null, ?bool $dlr = null, ?string $dlrUrl = null, ?string $dlrLevel = null)
    {
        $this->to = $to;
        $this->from = $from;
        $this->content = $content;
        $this->dlr = ($dlr !== null) ? $dlr : $this->dlr;
        $this->dlrUrl = $dlrUrl ?? Config::get('jasmin_client.default_callback_url');
    }

    public function asBinary(bool $binary): self
    {
        $this->isBinary = $binary;
        return $this;
    }

    public function content(string $content): self
    {
        $this->content = $content;
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

    public function via(string $route): self
    {
        $this->via = $route;
        return $this;
    }


    public function send(): ?JasminResponse
    {

        try {
            if ($this->via === 'http') {
                return JasminClient::http()->sendMessage(
                    content: $this->content,
                    to: $this->to,
                    from: $this->from,
                    coding: $this->coding,
                    priority: $this->priority,
                    sdt: $this->sdt,
                    validityPeriod: $this->validityPeriod,
                    dlr: $this->dlr,
                    dlrUrl: $this->dlrUrl,
                    dlrLevel: $this->dlrLevel,
                    dlrMethod: $this->dlrMethod,
                    tags: $this->tags,
                    hexContent: $this->hexContent,
                );
            } else {
                return JasminClient::rest()->sendMessage(
                    $this->content,
                    $this->to,
                    $this->from,
                    $this->dlr,
                    $this->dlrUrl,
                    $this->dlrLevel
                );
            }
        } catch (JasminClientException $e) {
            Log::error($e->getMessage());
        }
        return null;
    }

    public function toArray(): array
    {
        return array_filter([
            "to" => $this->to,
            "from" => $this->from,
            "content" => $this->content,
            "dlr" => $this->dlr,
            "dlr_url" => $this->dlrUrl,
            "dlr_level" => $this->dlrLevel,
            "is_binary" => $this->isBinary
        ]);
    }
}

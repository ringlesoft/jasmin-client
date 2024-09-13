<?php

namespace RingleSoft\JasminClient\Models;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Facades\JasminClient;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;

class Message
{

    private bool $isBinary = false;
    /**
     * Destination address, only one address is supported per request
     * @var string|null
     */
    public ?string $to;

    /**
     * Originating address, In case rewriting of the sender’s address is supported or permitted by the SMS-C
     * used to transmit the message, this number is transmitted as the originating address
     * @var string|null
     */
    public ?string $from;

    /**
     * @var string|null
     */
    public ?string $content;
    public bool $dlr = true;
    public ?string $dlrUrl;

    /**
     * 1: SMS-C level, 2: Terminal level, 3: Both
     * @var int|null
     */
    public ?int $dlrLevel = 2;

    /**
     * Sets the Data Coding Scheme bits, default is 0, accepts values all allowed values in SMPP protocol
     * @var int|null
     */
    private ?int $coding = 0;
    private ?int $priority = 0;

    /**
     * Specifies the scheduled delivery time at which the message delivery should be first attempted,
     * default is value is None (message will take SMSC’s default).
     * Supports Absolute and Relative Times per SMPP v3.4 Issue 1.2
     * @var string|null
     */
    private ?string $sdt = null;

    /**
     * Message validity (minutes) to be passed to SMSC, default is value is None (message will take SMSC’s default)
     * @var int|null
     */
    private ?int $validityPeriod = null;

    /**
     * DLR is transmitted through http to a third party application using GET or POST method.
     * @var string|null
     */
    private ?string $dlrMethod = 'GET';

    /**
     * @var int|null
     */
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

    public function dlrCallback(string $dlrUrl): self
    {
        $this->dlrUrl = $dlrUrl;
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
                    to: $this->sanitizeNumber($this->to),
                    from: $this->from,
                    coding: $this->coding,
                    priority: $this->priority,
                    sdt: $this->sdt,
                    validityPeriod: $this->validityPeriod,
                    dlr: $this->dlr ? 'yes' : 'no',
                    dlrUrl: $this->dlrUrl,
                    dlrLevel: $this->dlrLevel,
                    dlrMethod: $this->dlrMethod,
                    tags: $this->tags,
                    hexContent: $this->hexContent,
                );
            }
            return JasminClient::rest()->sendMessage(
                content: $this->content,
                to: $this->to,
                from: $this->from,
                dlr: $this->dlr,
                dlrUrl: $this->dlrUrl,
                dlrLevel: $this->dlrLevel
            );
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


    private function sanitizeNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return $phone;
    }
}

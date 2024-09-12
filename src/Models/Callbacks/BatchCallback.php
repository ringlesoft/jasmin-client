<?php

namespace RingleSoft\JasminClient\Models\Callbacks;

use Illuminate\Support\Str;

class BatchCallback
{

    public string $batchId;
    public string $to;
    public int $status;
    public string $statusText;

    public function __construct(?string $batchId, ?string $to, ?int $status, ?string $statusText)
    {
        $this->batchId = $batchId;
        $this->to = $to;
        $this->status = $status;
        $this->statusText = $statusText;
    }

    public function isSuccessful(): bool
    {
        return (bool) $this->status;
    }

    public function getMessageId(): string
    {
        $openingChar = "“";
        $closingChar = "”";
        return Str::of($this->statusText)->after($openingChar)->before($closingChar)->toString();
    }
}

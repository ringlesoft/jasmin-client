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

    public static function rules(): array
    {
        return  [
            'batchId' => ['required', 'string'],
            'to' => ['required', 'string'],
            'status' => ['required', 'string'],
            'statusText' => ['required', 'string'],
        ];
    }

    public function isSuccessful(): bool
    {
        return (bool) $this->status;
    }

    public function getMessageId(): string
    {
        $text = $this->statusText;
        str_replace('“', '"', $text);
        str_replace('”', '"', $text);
        return Str::of($text)->after('"')->before('"')->toString();
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }
}

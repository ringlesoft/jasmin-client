<?php

namespace RingleSoft\JasminClient\Models;

class Batch
{
    private array $batchConfig;
    private array $globals;
    private array $messages;

    public function __construct(?array $globals, ?array $messages)
    {
        $this->globals = $globals ?? [];
        $this->messages = $messages ?? [];
        $this->batchConfig = [
            "callback_url" => "http://127.0.0.1:7877/successful_batch",
            "errback_url" => "http://127.0.0.1:7877/errored_batch"
        ];
    }

    public function messages(array $messages): Batch
    {
        $this->messages = $messages;
        return $this;
    }

    public function addMessage(Message $message): Batch
    {
        $this->messages[] = $message->toArray();
        return $this;
    }

    public function globals(array $globals): Batch
    {
        $this->globals = $globals;
        return $this;
    }

    public function callbackUrl(string $callbackUrl): Batch
    {
        $this->batchConfig["callback_url"] = $callbackUrl;
        return $this;
    }

    public function errbackUrl(string $errbackUrl): Batch
    {
        $this->batchConfig["errback_url"] = $errbackUrl;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            "globals" => array_filter($this->globals),
            "messages" => array_filter($this->messages),
            "batch_config" => array_filter($this->batchConfig)
        ]);
    }


    public function send()
    {

    }
}

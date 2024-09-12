<?php

namespace RingleSoft\JasminClient\Models;

class JasminResponse
{
    public ?string $status;
    public ?string $message;
    public ?array $data;

    public function __construct(?string $status, ?string $message, ?array $data)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
}

<?php

namespace RingleSoft\JasminClient\Events;

use Throwable;

final class SmppConnectionFailed
{
    public function __construct(public Throwable $exception)
    {
    }
}

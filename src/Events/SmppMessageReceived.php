<?php

namespace RingleSoft\JasminClient\Events;

use RingleSoft\JasminClient\Models\SmppIncomingMessage;

final class SmppMessageReceived
{
    public function __construct(public SmppIncomingMessage $message)
    {
    }
}

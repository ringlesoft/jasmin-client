<?php

namespace RingleSoft\JasminClient\Models\Jasmin;

use RingleSoft\JasminClient\Models\Responses\JasminResponse;

class SentBatch
{
    public string $batchId;
    public int $messageCount;

    public function __construct(string $batchId, int $messageCount)
    {
        $this->batchId = $batchId;
        $this->messageCount = $messageCount;
    }

    public static function from(JasminResponse $response): self
    {

    }


}

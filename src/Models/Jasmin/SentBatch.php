<?php

namespace RingleSoft\JasminClient\Models\Jasmin;

use RingleSoft\JasminClient\Models\Responses\JasminResponse;
use RuntimeException;

class SentBatch
{
    public string $batchId;
    public int $messageCount;

    public function __construct(string $batchId, int $messageCount)
    {
        $this->batchId = $batchId;
        $this->messageCount = $messageCount;
    }

    /**
     * @param JasminResponse $response
     * @return self
     */
    public static function fromResponse(JasminResponse $response): self
    {
        $data = $response->data['data'] ?? '';
        if ($data !== '') {
            $batchId = $data['batchId'] ?? '';
            $messageCount = $data['messageCount'] ?? 0;
            return new self($batchId, $messageCount);
        }
        throw new RuntimeException("Invalid response from jasmin");
    }

    public function toArray(): array
    {
        return [
            'batchId' => $this->batchId,
            'messageCount' => $this->messageCount
        ];
    }


}

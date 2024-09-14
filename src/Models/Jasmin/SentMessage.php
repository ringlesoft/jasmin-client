<?php

namespace RingleSoft\JasminClient\Models\Jasmin;

use Illuminate\Support\Str;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;
use RuntimeException;

class SentMessage
{
    public string $messageId;
    public string $status;

    public function __construct(?string $status = null, ?string $messageId = null)
    {
        $this->status = $status;
        $this->messageId = $messageId;
    }

    /**
     * @param JasminResponse $response
     * @return self
     * @throws RuntimeException
     */
    public static function fromResponse(JasminResponse $response): self
    {
        $data = $response->data['data'] ?? '';
        dump($data);
        if ($data !== '') {
            [$status, $messageId] = self::extractValues($data);
            dump($status);
            dump($messageId);
            return new self(status: $status, messageId: $messageId);
        }
        throw new RuntimeException("Invalid response from jasmin");
    }

    private static function extractValues(string $data): array
    {
        if(Str::contains($data, 'Success ')) {
            $status = Str::before($data, ' ');
            $messageId = Str::of($data)->after('Success "')->before('"')->toString();
            return [$status, $messageId];
        }
        return [null, null];
    }
}

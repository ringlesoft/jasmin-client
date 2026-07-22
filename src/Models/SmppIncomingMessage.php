<?php

namespace RingleSoft\JasminClient\Models;

use PhpSmpp\Pdu\DeliverSm;

final class SmppIncomingMessage
{
    public function __construct(
        public string $messageId,
        public string $from,
        public string $to,
        public string $content,
        public int $dataCoding,
        public int $priority,
    ) {
    }

    public static function fromPdu(DeliverSm $message): self
    {
        return new self(
            messageId: (string) ($message->msgId ?? ''),
            from: (string) ($message->source->value ?? ''),
            to: (string) ($message->destination->value ?? ''),
            content: (string) ($message->message ?? ''),
            dataCoding: (int) ($message->dataCoding ?? 0),
            priority: (int) ($message->priorityFlag ?? 0),
        );
    }
}

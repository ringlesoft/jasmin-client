<?php

namespace RingleSoft\JasminCLient\Models;

use PhpSmpp\Pdu\DeliverReceiptSm;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;

class DeliveryMessage
{
    public function __construct(
        public string $id,
        public int    $sub,
        public int    $dlvrd,
        public string $submitDate,
        public string $doneDate,
        public string $stat,
        public string $err,
        public string $text,
    ) {}

    public function getMessageState(): string
    {
        return match ($this->stat) {
            'DELIVRD' => 'DELIVERED',
            'EXPIRED' => 'EXPIRED',
            'DELETED' => 'DELETED',
            'UNDELIV' => 'UNDELIVERABLE',
            'ACCEPTD' => 'ACCEPTED',
            'REJECTD' => 'REJECTED',
            default => 'UNKNOWN',
        };
    }


    public static function fromCallback(DeliveryCallback $callback): self
    {
        return new self(
            id: $callback->getMessageId(),
            sub: $callback->submittedCount,
            dlvrd: $callback->deliveredCount,
            submitDate: $callback->submittedDate,
            doneDate: $callback->doneDate,
            stat: $callback->messageStatus,
            err: $callback->error,
            text: $callback->text
        );
    }

    public static function fromSmppDeliveyr(DeliverReceiptSm $delivery): self
    {
        return new self(
            id: $delivery->msgId,
            sub: $delivery->state,
            dlvrd: $delivery->state,
            submitDate: $delivery->submitDate,
            doneDate: $delivery->doneDate,
            stat: $delivery->state,
            err: $delivery->state,
            text: $delivery->state
        );
    }
}

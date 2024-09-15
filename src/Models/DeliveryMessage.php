<?php

namespace RingleSoft\JasminCLient\Models;

use PhpSmpp\Pdu\DeliverReceiptSm;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;

class DeliveryMessage
{
    public function __construct(
        public string $getMessageId,
        public int    $submittedCount,
        public int    $deliveredCount,
        public string $submittedDate,
        public string $doneDate,
        public string $messageStatus,
        public string $error,
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
            getMessageId: $callback->getMessageId(),
            submittedCount: $callback->submittedCount,
            deliveredCount: $callback->deliveredCount,
            submittedDate: $callback->submittedDate,
            doneDate: $callback->doneDate,
            messageStatus: $callback->messageStatus,
            error: $callback->error,
            text: $callback->text
        );
    }

    public static function fromSmppDeliveyr(DeliverReceiptSm $delivery): self
    {
        return new self(
            getMessageId: $delivery->msgId,
            submittedCount: $delivery->state,
            deliveredCount: $delivery->state,
            submittedDate: $delivery->submitDate,
            doneDate: $delivery->doneDate,
            messageStatus: $delivery->state,
            error: $delivery->state,
            text: $delivery->state
        );
    }
}

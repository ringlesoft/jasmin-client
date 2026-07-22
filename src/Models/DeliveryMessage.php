<?php

namespace RingleSoft\JasminClient\Models;

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
        return match ($this->messageStatus) {
            'DELIVRD' => 'DELIVERED',
            'EXPIRED' => 'EXPIRED',
            'DELETED' => 'DELETED',
            'UNDELIV' => 'UNDELIVERABLE',
            'ACCEPTD' => 'ACCEPTED',
            'REJECTD' => 'REJECTED',
            default => 'UNKNOWN',
        };
    }


    /**
     * @param DeliveryCallback $callback
     * @return self
     */
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

    /**
     * @param DeliverReceiptSm $delivery
     * @return self
     */
    public static function fromSmppDelivery(DeliverReceiptSm $delivery): self
    {
        $report = SmppDeliveryReport::fromPdu($delivery);

        return new self(
            getMessageId: $report->messageId,
            submittedCount: 0,
            deliveredCount: $report->status === 'DELIVRD' ? 1 : 0,
            submittedDate: $report->submittedAt?->format('Y-m-d H:i:s') ?? '',
            doneDate: $report->deliveredAt?->format('Y-m-d H:i:s') ?? '',
            messageStatus: $report->status,
            error: $report->errorCode === null ? '' : (string) $report->errorCode,
            text: $report->rawReceipt,
        );
    }
}

<?php

namespace RingleSoft\JasminClient\Models;

use DateTimeImmutable;
use DateTimeInterface;
use PhpSmpp\Pdu\DeliverReceiptSm;
use PhpSmpp\SMPP;

final class SmppDeliveryReport
{
    public function __construct(
        public string $messageId,
        public string $status,
        public ?DateTimeImmutable $submittedAt,
        public ?DateTimeImmutable $deliveredAt,
        public ?int $errorCode,
        public string $errorText,
        public string $rawReceipt,
    ) {
    }

    public static function fromPdu(DeliverReceiptSm $receipt): self
    {
        return new self(
            messageId: (string) ($receipt->msgId ?? ''),
            status: self::statusFor((int) ($receipt->state ?? 0)),
            submittedAt: self::immutableDate($receipt->submitDate ?? null),
            deliveredAt: self::immutableDate($receipt->doneDate ?? null),
            errorCode: isset($receipt->receiptErrorCode) ? (int) $receipt->receiptErrorCode : null,
            errorText: (string) ($receipt->receiptErrorText ?? ''),
            rawReceipt: (string) ($receipt->message ?? ''),
        );
    }

    private static function immutableDate(mixed $date): ?DateTimeImmutable
    {
        return $date instanceof DateTimeInterface ? DateTimeImmutable::createFromInterface($date) : null;
    }

    private static function statusFor(int $state): string
    {
        return match ($state) {
            SMPP::STATE_DELIVERED => 'DELIVRD',
            SMPP::STATE_EXPIRED => 'EXPIRED',
            SMPP::STATE_DELETED => 'DELETED',
            SMPP::STATE_UNDELIVERABLE => 'UNDELIV',
            SMPP::STATE_ACCEPTED => 'ACCEPTD',
            SMPP::STATE_REJECTED => 'REJECTD',
            default => 'UNKNOWN',
        };
    }
}

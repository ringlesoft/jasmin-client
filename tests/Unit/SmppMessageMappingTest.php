<?php

namespace RingleSoft\JasminClient\Tests\Unit;

use DateTimeImmutable;
use PhpSmpp\Pdu\DeliverReceiptSm;
use PhpSmpp\Pdu\DeliverSm;
use PhpSmpp\Pdu\Part\Address;
use PhpSmpp\SMPP;
use PHPUnit\Framework\Attributes\Test;
use RingleSoft\JasminClient\Models\DeliveryMessage;
use RingleSoft\JasminClient\Models\SmppDeliveryReport;
use RingleSoft\JasminClient\Models\SmppIncomingMessage;
use RingleSoft\JasminClient\Tests\TestCase;

class SmppMessageMappingTest extends TestCase
{
    #[Test]
    public function it_maps_an_inbound_smpp_message_to_an_immutable_dto(): void
    {
        $pdu = new DeliverSm(0, 0, 1, '');
        $pdu->msgId = 'mo-1';
        $pdu->source = new Address('255711000000');
        $pdu->destination = new Address('INFO');
        $pdu->message = 'Hello';
        $pdu->dataCoding = 8;
        $pdu->priorityFlag = 1;

        $message = SmppIncomingMessage::fromPdu($pdu);

        $this->assertSame('mo-1', $message->messageId);
        $this->assertSame('255711000000', $message->from);
        $this->assertSame('INFO', $message->to);
        $this->assertSame('Hello', $message->content);
    }

    #[Test]
    public function it_maps_an_smpp_delivery_receipt_without_using_state_as_message_metadata(): void
    {
        $pdu = new DeliverReceiptSm(0, 0, 1, '');
        $pdu->msgId = 'dlr-1';
        $pdu->state = SMPP::STATE_DELIVERED;
        $pdu->submitDate = new DateTimeImmutable('2026-01-01 12:00:00 UTC');
        $pdu->doneDate = new DateTimeImmutable('2026-01-01 12:01:00 UTC');
        $pdu->receiptErrorCode = 0;
        $pdu->receiptErrorText = 'Code:0';
        $pdu->message = 'id:dlr-1 stat:DELIVRD';

        $report = SmppDeliveryReport::fromPdu($pdu);
        $delivery = DeliveryMessage::fromSmppDelivery($pdu);

        $this->assertSame('DELIVRD', $report->status);
        $this->assertSame('dlr-1', $delivery->getMessageId);
        $this->assertSame(1, $delivery->deliveredCount);
        $this->assertSame('0', $delivery->error);
    }
}

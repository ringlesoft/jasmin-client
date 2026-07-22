<?php

namespace RingleSoft\JasminClient\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use RingleSoft\JasminClient\Events\SmppDeliveryReportReceived;
use RingleSoft\JasminClient\Events\SmppMessageReceived;
use RingleSoft\JasminClient\Models\SmppDeliveryReport;
use RingleSoft\JasminClient\Models\SmppIncomingMessage;
use RingleSoft\JasminClient\Tests\TestCase;

class SmppEventsTest extends TestCase
{
    #[Test]
    public function it_exposes_typed_event_payloads(): void
    {
        $message = new SmppIncomingMessage('mo-1', '255711000000', 'INFO', 'Hello', 0, 0);
        $report = new SmppDeliveryReport('dlr-1', 'DELIVRD', null, null, null, '', '');

        $this->assertSame($message, (new SmppMessageReceived($message))->message);
        $this->assertSame($report, (new SmppDeliveryReportReceived($report))->report);
    }
}

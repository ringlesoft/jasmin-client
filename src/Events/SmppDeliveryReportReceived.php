<?php

namespace RingleSoft\JasminClient\Events;

use RingleSoft\JasminClient\Models\SmppDeliveryReport;

final class SmppDeliveryReportReceived
{
    public function __construct(public SmppDeliveryReport $report)
    {
    }
}

<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Config;
use PhpSmpp\Pdu\DeliverReceiptSm;
use PhpSmpp\Pdu\DeliverSm;
use PhpSmpp\Service\Listener;
use RingleSoft\JasminClient\Events\SmppConnectionFailed;
use RingleSoft\JasminClient\Events\SmppDeliveryReportReceived;
use RingleSoft\JasminClient\Events\SmppMessageReceived;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\SmppDeliveryReport;
use RingleSoft\JasminClient\Models\SmppIncomingMessage;
use RingleSoft\JasminCLient\Utility\Logger;
use Throwable;

class SmppReceiver
{
    private Listener $listener;

    public function __construct(private readonly Dispatcher $events)
    {
        $config = Config::get('jasmin_client.smpp', []);
        $hosts = $config['hosts'] ?? [];
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;

        if ($hosts === [] || !$username || !$password) {
            throw new JasminClientException('SMPP receiver credentials and hosts must be configured.');
        }

        $this->listener = new Listener($hosts, $username, $password, null);
    }

    /**
     * @throws JasminClientException
     */
    public function listenOnce(): void
    {
        try {
            $this->listener->listenOnce(function (DeliverSm $pdu): void {
                if ($pdu instanceof DeliverReceiptSm) {
                    $this->events->dispatch(new SmppDeliveryReportReceived(SmppDeliveryReport::fromPdu($pdu)));
                    return;
                }

                $this->events->dispatch(new SmppMessageReceived(SmppIncomingMessage::fromPdu($pdu)));
            });
        } catch (Throwable $exception) {
            $this->events->dispatch(new SmppConnectionFailed($exception));
//            Logger::error('Unable to receive SMPP messages.'. $exception->getMessage());
            throw JasminClientException::from($exception, 'Unable to receive SMPP messages.');
        }
    }

    public function disconnect(): void
    {
        $this->listener->unbind();
    }
}

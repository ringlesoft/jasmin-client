<?php

namespace RingleSoft\JasminClient\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use PhpSmpp\Pdu\DeliverReceiptSm;
use PhpSmpp\Pdu\Pdu;
use PhpSmpp\Pdu\Sm;
use PhpSmpp\Pdu\Ussd;
use PhpSmpp\Service\Listener;
use PhpSmpp\Service\Sender;
use PhpSmpp\Service\Service;
use PhpSmpp\SMPP;
use RingleSoft\JasminClient\Contracts\JasminSmppContract;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use Throwable;

class SmppService implements JasminSmppContract
{

    public Service $senderService;
    public Service $listenerService;
    private string $url;
    private string $username;
    private string $password;
    public function __construct(?string $username = null, ?string $password = null, ?string $url = null)
    {
        $this->url = $url ?? Config::get('jasmin_client.smpp_url');
        $this->username = $username ?? Config::get('jasmin_client.username');
        $this->password = $password ?? Config::get('jasmin_client.password');
        $this->senderService =  new Sender([$this->url], $this->username, $this->password, null);
        $this->listenerService = new Listener([$this->url], $this->username, $this->password, null);
    }

    /**
     * @throws Throwable
     * @throws JasminClientException
     */
    public function sendMessage(string $to, string $content, string $from): string
    {
        try {
            return $this->senderService->send(79001001010, 'Hello world!', 'Sender');
        } catch (Exception $e) {
            throw JasminClientException::from($e);
        }
    }


    public function receiveMessage(): ?Sm
    {
        $this->listenerService->listen(function (Sm $sm) {
//            var_dump($sm->msgId);
            if ($sm instanceof DeliverReceiptSm) {
//                var_dump($sm->state);
//                var_dump($sm->state === SMPP::STATE_DELIVERED);
                REturn $sm;
            } else {
                echo 'not message';
                return null;
            }
        });
        return null;
    }

    public function receiveDeliveryReport(): ?Sm
    {
        $this->listenerService->listen(function (Sm $sm) {
//            var_dump($sm->msgId);
            if ($sm instanceof DeliverReceiptSm) {
//                var_dump($sm->state);
//                var_dump($sm->state === SMPP::STATE_DELIVERED);
                return $sm;
            } else {
                echo 'not message';
                return null;
            }
        });
        return null;
    }





    /**
     * @throws Throwable
     * @throws JasminClientException
     */
    public function sendUssd(string $to, string $content, string $from): string
    {
        try {
            return $this->senderService->sendUSSD($to, $content, $from, []);
        } catch (Exception $e) {
            throw JasminClientException::from($e);
        }
    }


    public function receiveUssd(): ?Ussd {
        $this->listenerService->listen(function (Pdu $pdu) {
//            var_dump($pdu->id);
//            var_dump($pdu->sequence);
            if ($pdu instanceof Ussd) {
//                var_dump($pdu->status);
//                var_dump($pdu->source->value);
//                var_dump($pdu->destination->value);
//                var_dump($pdu->message);
                // do some job with ussd
                return $pdu;
            }
        });
        return null;
    }


}

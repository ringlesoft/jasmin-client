<?php

namespace RingleSoft\JasminClient\Models;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Facades\JasminClient;
use RingleSoft\JasminClient\Models\Jasmin\SentMessage;

class Message
{

    private bool $isBinary = false;
    /**
     * Destination address, only one address is supported per request
     * @var string|null
     */
    public ?string $to;

    /**
     * Originating address, In case rewriting of the sender’s address is supported or permitted by the SMS-C
     * used to transmit the message, this number is transmitted as the originating address
     * @var string|null
     */
    public ?string $from;

    /**
     * @var string|null
     */
    public ?string $content;

    /**
     * @var bool
     */
    public bool $dlr = true;

    /**
     * @var string|mixed|null
     */
    public ?string $dlrUrl;

    /**
     * 1: SMS-C level, 2: Terminal level, 3: Both
     * @var int|null
     */
    public ?int $dlrLevel = 2;

    /**
     * Sets the Data Coding Scheme bits, default is 0, accepts values all allowed values in SMPP protocol
     * @var int|null
     */
    private ?int $coding = 0;

    /**
     * @var int|null
     */
    private ?int $priority = 0;

    /**
     * Specifies the scheduled delivery time at which the message delivery should be first attempted,
     * default is value is None (message will take SMSC’s default).
     * Supports Absolute and Relative Times per SMPP v3.4 Issue 1.2
     * @var string|null
     */
    private ?string $sdt = null;

    /**
     * Message validity (minutes) to be passed to SMSC, default is value is None (message will take SMSC’s default)
     * @var int|null
     */
    private ?int $validityPeriod = null;

    /**
     * DLR is transmitted through http to a third party application using GET or POST method.
     * @var string|null
     */
    private ?string $dlrMethod = 'GET';

    /**
     * @var int|null
     */
    private ?int $tags = null;

    /**
     * @var string|null
     */
    private ?string $hexContent = null;

    /**
     * @var string|null
     */
    private ?string $via;

    private ?string $routeUsername;
    private ?string $routePassword;
    private ?string $routeUrl;

    public function __construct(?string $to = null, ?string $from = null, ?string $content = null, ?bool $dlr = null, ?string $dlrUrl = null, ?string $dlrLevel = null, ?string $dlrMethod = null)
    {
        $this->to = $to;
        $this->from = $from;
        $this->content = $content;
        $this->dlr = ($dlr !== null) ? $dlr : $this->dlr;
        $this->dlrUrl = $dlrUrl ?? Config::get('jasmin_client.dlr_callback_url');
        $this->dlrMethod = $dlrMethod ?? Config::get('jasmin_client.dlr_method', 'POST');
        $this->dlrLevel = $dlrLevel ?? Config::get('jasmin_client.dlr_level', 2);
    }

    /**
     * Set if the message should be sent as binary
     * @param bool $binary
     * @return $this
     */
    public function asBinary(bool $binary): self
    {
        $this->isBinary = $binary;
        return $this;
    }

    /**
     * Set the message content
     * @param string $content
     * @return $this
     */
    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the destination address (phone number)
     * @param string $to
     * @return $this
     */
    public function to(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    /**
     * Set the originating address (SENDER ID)
     * @param string $from
     * @return $this
     */
    public function from(string $from): self
    {
        $this->from = $from;
        return $this;
    }

    /**
     * Set the DLR callback url
     * @param string $dlrUrl
     * @return $this
     */
    public function dlrCallbackUrl(string $dlrUrl): self
    {
        $this->dlrUrl = $dlrUrl;
        return $this;
    }

    /**
     * Set if the message should be tracked for delivery
     * @param $value
     * @return $this
     */
    public function trackDelivery($value = true): self
    {
        $this->dlr = $value ? 'yes' :'no';
        return $this;
    }

    public function via(string $route, ?string $username = null, ?string $password = null, ?string $url = null): self
    {
        $this->via = $route;
        return $this;
    }


    /**
     * @return SentMessage
     * @throws JasminClientException
     */
    public function send(): SentMessage
    {
        // TODO check if all required fields are set
        try {
            if ($this->via === 'http') {
                $response = JasminClient::http($this->routeUsername, $this->routePassword, $this->routeUrl)
                    ->sendMessage(
                    content: $this->content,
                    to: $this->sanitizeNumber($this->to),
                    from: $this->from,
                    coding: $this->coding,
                    priority: $this->priority,
                    sdt: $this->sdt,
                    validityPeriod: $this->validityPeriod,
                    dlr: $this->dlr ? 'yes' : 'no',
                    dlrUrl: $this->dlrUrl,
                    dlrLevel: $this->dlrLevel,
                    dlrMethod: $this->dlrMethod,
                    tags: $this->tags,
                    hexContent: $this->hexContent,
                );
            } else {
                $response = JasminClient::rest($this->routeUsername, $this->routePassword, $this->routeUrl)
                    ->sendMessage(
                    content: $this->content,
                    to: $this->sanitizeNumber($this->to),
                    from: $this->from,
                    dlr: $this->dlr ? 'yes' : 'no',
                    dlrUrl: $this->dlrUrl,
                    dlrLevel: $this->dlrLevel,
                    dlrMethod: $this->dlrMethod
                );
            }
            if($response->isSuccessful()) {
                return SentMessage::fromResponse($response);
            } else {
                throw new JasminClientException("Failed to send message");
            }

        } catch (JasminClientException $e) {
            Log::error("JasminClient: ". $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param string|null $username
     * @param string|null $password
     * @param string|null $url
     * @return $this
     */
    public function withCredentials(?string $username = null, ?string $password = null, ?string $url = null): self
    {
        $this->routeUsername = $username ?? $this->routeUsername;
        $this->routePassword = $password ?? $this->routePassword;
        $this->routeUrl = $url ?? $this->routeUrl;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            "to" => $this->to,
            "from" => $this->from,
            "content" => $this->content,
            "dlr" => $this->dlr ? 'yes' : 'no',
            "dlr-url" => $this->dlrUrl,
            "dlr-level" => $this->dlrLevel,
            "dlr-method" => $this->dlrMethod,
            "is_binary" => $this->isBinary
        ]);
    }


    private function sanitizeNumber(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        return $phone;
    }
}

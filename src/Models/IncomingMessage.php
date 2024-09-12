<?php

namespace RingleSoft\JasminClient\Models;

use DateTime;
use Illuminate\Support\Facades\Date;

class IncomingMessage
{
    /**
     * Internal Jasmin’s gateway message id
     * @var string
     */
    public string $id;
    /**
     * Originating address
     * @var string
     */
    public string $from;

    /**
     * Destination address, only one address is supported per request
     * @var string
     */
    public string $to;

    /**
     * Jasmin http connector id
     * @var string
     */
    public string $originConnector;

    /**
     * Default is 1 (lowest priority)
     * @var string
     */
    public string $priority;

    /**
     * Default is 0, accepts values all allowed values in SMPP protocol
     * @var int
     */
    public int $coding;

    /**
     * The validity period parameter indicates the Jasmin GW expiration time, after which the message should be discarded if not delivered to the destination
     * @var string
     */
    public string $validity;

    /**
     * Content of the message
     * @var string
     */
    public string $content;

    /*
     * Content of the message in binary format
     * @var string
     */
    public string $binary;

    public function __construct(string $id, string $from, string $to, string $originConnector, string $priority, bool $coding, string $validity, string $message, string $binary)
    {
        $this->id = $id;
        $this->from = $from;
        $this->to = $to;
        $this->originConnector = $originConnector;
        $this->priority = $priority;
        $this->coding = $coding;
        $this->validity = $validity;
        $this->content = $message;
        $this->binary = $binary ;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['from'],
            $data['to'],
            $data['origin-connector'],
            $data['priority'],
            $data['coding'],
            $data['validity'],
            $data['content'],
            $data['binary']
        );
    }
}

<?php

namespace RingleSoft\JasminClient\Contracts;

use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\JasminRestResponse;

interface JasminRestContract
{


    /**
     * Send a single message
     * @param string $content
     * @param string $to
     * @param string $from
     * @param string $dlr
     * @param string $dlrUrl
     * @param string $dlrLevel
     * @param bool|null $asBinary
     * @return JasminRestResponse
     * @throws JasminClientException
     */
    public function sendMessage(string $content, string $to, string $from, string $dlr, string $dlrUrl, string $dlrLevel, ?bool $asBinary = false): JasminRestResponse;

    /**
     * Send multiple messages
     * @param array $messages
     * @param array|null $globals
     * @param string|null $callbackUrl
     * @param string|null $errbackUrl
     * @param bool|null $asBinary
     * @return JasminRestResponse
     */
    public function sendMultipleMessages(array $messages, ?array $globals, ?string $callbackUrl, ?string $errbackUrl, ?bool $asBinary = false): JasminRestResponse;
    // Send binary messages

    /**
     * @return mixed
     * @throws JasminClientException
     */
    public function checkBalance(): JasminRestResponse;
    // Route check

    /**
     * @param string|null $to
     * @return JasminRestResponse
     */
    public function checkRoute(?string $to): JasminRestResponse;

    // Ping

    /**
     * @return JasminRestResponse
     */
    public function ping(): JasminRestResponse;


}

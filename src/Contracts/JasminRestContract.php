<?php

namespace RingleSoft\JasminClient\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;

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
     * @return JasminResponse
     * @throws JasminClientException
     */
    public function sendMessage(string $content, string $to, string $from, string $dlr, string $dlrUrl, string $dlrLevel, ?bool $asBinary = false): JasminResponse;

    /**
     * Send multiple messages
     * @param array $messages
     * @param array|null $globals
     * @param string|null $callbackUrl
     * @param string|null $errbackUrl
     * @param bool|null $asBinary
     * @return JasminResponse
     */
    public function sendBatch(array $messages, ?array $globals, ?array $batchConfig, ?bool $asBinary = false): JasminResponse;
    // Send binary messages

    /**
     * @return mixed
     * @throws JasminClientException
     */
    public function checkBalance(): JasminResponse;
    // Route check

    /**
     * @param string|null $to
     * @return JasminResponse
     */
    public function checkRoute(?string $to): JasminResponse;

    // Ping

    /**
     * @return JasminResponse
     */
    public function ping(): JasminResponse;

    /**
     * @param Request $request
     * @param callable $callback(BatchCallback $batchCallback)
     * @return JsonResponse
     */
    public function receiveBatchCallback(Request $request, callable $callback): JsonResponse;


}

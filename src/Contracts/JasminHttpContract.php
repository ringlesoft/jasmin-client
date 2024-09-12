<?php

namespace RingleSoft\JasminClient\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\IncomingMessage;
use RingleSoft\JasminClient\Models\JasminHttpResponse;

interface JasminHttpContract
{

    // Send a single message
    /**
     * @param string $content
     * @param string $to
     * @param string $from
     * @param string $coding
     * @param int $priority
     * @param string $sdt
     * @param string $validityPeriod
     * @param string $dlr
     * @param string $dlrUrl
     * @param string $dlrLevel
     * @param string $dlrMethod
     * @param string|null $tags
     * @param string|null $hexContent
     * @param bool|null $asBinary
     * @return JasminHttpResponse
     */
    public function sendMessage(string $content, string $to, string $from, string $coding, int $priority, string $sdt, string $validityPeriod, string $dlr, string $dlrUrl, string $dlrLevel, string $dlrMethod, ?string $tags, ?string $hexContent, ?bool $asBinary = false): JasminHttpResponse;


    /**
     * @return mixed
     * @throws JasminClientException
     */
    public function checkBalance(): JasminHttpResponse;
    // Route check

    /**
     * @param string|null $to
     * @return JasminHttpResponse
     */
    public function checkRoute(?string $to): JasminHttpResponse;


    public function getMetrics(): JasminHttpResponse;

    /**
     * @param Request $request
     * @param callable $callback (DeliveryCallback $deliveryCallback)
     * @return JsonResponse
     */
    public function receiveDlrCallback(Request $request, callable $callback): JsonResponse;

    // Receive MO message

    /**
     * @param Request $request
     * @param callable $callback (IncomingMessage $message)
     * @return JsonResponse
     */
    public function receiveMessage(Request $request, callable $callback): JsonResponse;
}

<?php

namespace RingleSoft\JasminClient\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;

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
     * @param int $dlrLevel
     * @param string $dlrMethod
     * @param string|null $tags
     * @param string|null $hexContent
     * @param bool|null $asBinary
     * @return JasminResponse
     */
    public function sendMessage(string $content, string $to, string $from, string $coding, int $priority, string $sdt, string $validityPeriod, string $dlr, string $dlrUrl, int $dlrLevel, string $dlrMethod, ?string $tags, ?string $hexContent, ?bool $asBinary = false): JasminResponse;


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


    public function getMetrics(): JasminResponse;

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

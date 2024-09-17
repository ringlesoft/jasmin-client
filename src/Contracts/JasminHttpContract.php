<?php

namespace RingleSoft\JasminClient\Contracts;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;
use RingleSoft\JasminClient\Models\IncomingMessage;
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
     * @param string|null $from
     * @param string|null $coding
     * @param string|null $content
     * @return JasminResponse
     */
    public function checkRoute(?string $to, ?string $from = null, ?string $coding = null, ?string $content = null): JasminResponse;


    public function getMetrics(): JasminResponse;

    /**
     * @param Request $request
     * @param callable(DeliveryCallback $dlr): bool $callback
     * @return Response
     */
    public function receiveDlrCallback(Request $request, callable $callback): Response;

    // Receive MO message

    /**
     * @param Request $request
     * @param callable(IncomingMessage $message): bool $callback
     * @return Response
     */
    public function receiveMessage(Request $request, callable $callback): Response;
}

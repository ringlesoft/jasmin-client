<?php

namespace RingleSoft\JasminClient;

use http\Client\Request;
use Illuminate\Http\JsonResponse;
use RingleSoft\JasminClient\Models\Batch;
use RingleSoft\JasminClient\Models\Callbacks\BatchCallback;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;
use RingleSoft\JasminClient\Models\IncomingMessage;
use RingleSoft\JasminClient\Models\Message;
use RingleSoft\JasminClient\Services\HttpService;
use RingleSoft\JasminClient\Services\RestService;
use RingleSoft\JasminClient\Services\SmppService;

class JasminClient
{

    /**
     * @return HttpService
     */
    public static function http(): HttpService
    {
        return new HttpService();
    }

    /**
     * @return RestService
     */
    public static function rest(): RestService
    {
        return new RestService();
    }

    /**
     * @return SmppService
     */
    public static function smpp(): SmppService
    {
        return new SmppService();
    }

    /**
     * @return Message
     */
    public static function message(): Message
    {
        return new Message();
    }

    /**
     * @return Batch
     */
    public static function batch(): Batch
    {
        return new Batch();
    }

    /**
     * Receive a batch callback from the jasmin gateway
     * @param Request $request
     * @param callable(BatchCallback $batch): bool $callback
     * @return JsonResponse
     */
    public static function receiveBatchCallback(Request $request, callable $callback): JsonResponse
    {
        return self::rest()->receiveBatchCallback($request, $callback);
    }

    /**
     * Receive a dlr callback from the jasmin gateway
     * @param \Illuminate\Http\Request $request
     * @param callable(DeliveryCallback $dlr): bool $callback
     * @return JsonResponse
     */
    public static function receiveDlrCallback(\Illuminate\Http\Request $request, callable $callback): JsonResponse
    {
        return self::http()->receiveDlrCallback($request, $callback);
    }

    /**
     * Receive a MO message callback from the jasmin gateway
     * @param Request $request
     * @param callable(IncomingMessage $message): bool $callback
     * @return JsonResponse
     */
    public static function receiveMessage(Request $request, callable $callback): JsonResponse
    {
        return self::http()->receiveMessage($request, $callback);
    }
}

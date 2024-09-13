<?php

namespace RingleSoft\JasminClient;

use http\Client\Request;
use Illuminate\Http\JsonResponse;
use RingleSoft\JasminClient\Services\HttpService;
use RingleSoft\JasminClient\Services\RestService;
use RingleSoft\JasminClient\Services\SmppService;

class JasminClient
{

    public static function http(): HttpService
    {
        return new HttpService();
    }

    public static function rest(): RestService
    {
        return new RestService();
    }

    public static function smpp(): SmppService
    {
        return new SmppService();
    }

    public static function receiveBatchCallback(Request $request, callable $callback): JsonResponse
    {
        return self::http()->receiveBatchCallback($request, $callback);
    }

    public static function receiveDlrCallback(Request $request, callable $callback): JsonResponse
    {
        return self::http()->receiveDlrCallback($request, $callback);
    }

    public static function receiveMessage(Request $request, callable $callback): JsonResponse
    {
        return self::http()->receiveMessage($request, $callback);
    }
}

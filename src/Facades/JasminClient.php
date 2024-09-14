<?php

namespace RingleSoft\JasminClient\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;
use RingleSoft\JasminClient\Models\Batch;
use RingleSoft\JasminClient\Models\Message;
use RingleSoft\JasminClient\Services\HttpService;
use RingleSoft\JasminClient\Services\RestService;
use RingleSoft\JasminClient\Services\SmppService;

/**
 * @see \RingleSoft\JasminClient\JasminClient
 * @method static HttpService http()
 * @method static RestService rest()
 * @method static SmppService smpp()
 * @method static Message message()
 * @method static Batch batch()
 * @method static JsonResponse receiveBatchCallback()
 * @method static JsonResponse receiveDlrCallback()
 * @method static JsonResponse receiveMessage()
 */
class JasminClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RingleSoft\JasminClient\JasminClient::class;
    }
}

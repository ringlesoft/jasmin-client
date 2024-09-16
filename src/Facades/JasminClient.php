<?php

namespace RingleSoft\JasminClient\Facades;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use RingleSoft\JasminClient\Models\Batch;
use RingleSoft\JasminClient\Models\Message;
use RingleSoft\JasminClient\Services\HttpService;
use RingleSoft\JasminClient\Services\RestService;
use RingleSoft\JasminClient\Services\SmppService;

/**
 * @see \RingleSoft\JasminClient\JasminClient
 * @method static HttpService http(?string $username = null, ?string $password = null, ?string $url = null)
 * @method static RestService rest(?string $username = null, ?string $password = null, ?string $url = null)
 * @method static SmppService smpp(?string $username = null, ?string $password = null, ?string $url = null)
 * @method static Message message()
 * @method static Batch batch()
 * @method static JsonResponse receiveBatchCallback(Request $request, callable $callback)
 * @method static JsonResponse receiveDlrCallback(Request $request, callable $callback)
 * @method static JsonResponse receiveMessage(Request $request, callable $callback)
 */
class JasminClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RingleSoft\JasminClient\JasminClient::class;
    }
}

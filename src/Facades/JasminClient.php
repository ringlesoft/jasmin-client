<?php

namespace RingleSoft\JasminClient\Facades;

use Illuminate\Support\Facades\Facade;
use RingleSoft\JasminClient\Services\HttpService;
use RingleSoft\JasminClient\Services\RestService;
use RingleSoft\JasminClient\Services\SmppService;

/**
 * @see \RingleSoft\JasminClient\JasminClient
 * @method static HttpService http()
 * @method static RestService rest()
 * @method static SmppService smpp()
 */
class JasminClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RingleSoft\JasminClient\JasminClient::class;
    }
}

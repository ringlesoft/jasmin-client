<?php

namespace RingleSoft\JasminClient;

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
}

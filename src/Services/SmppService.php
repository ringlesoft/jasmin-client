<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Support\Facades\Config;
use RingleSoft\JasminClient\Contracts\JasminSmppContract;

class SmppService implements JasminSmppContract
{

    private string $url;
    private string $username;
    private string $password;
    public function __construct(?string $username = null, ?string $password = null, ?string $url = null)
    {
        $this->url = $url ?? Config::get('jasmin_client.url');
        $this->username = $username ?? Config::get('jasmin_client.username');
        $this->password = $password ?? Config::get('jasmin_client.password');
    }

}

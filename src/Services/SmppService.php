<?php

namespace RingleSoft\JasminClient\Services;

use RingleSoft\JasminClient\Contracts\JasminHttpContract;

class SmppService implements JasminHttpContract
{

    private string $url;
    private string $username;
    private string $password;
    public function __construct(?string $username, ?string $password, ?string $url)
    {
        $this->url = $url ?? config('jasmin_client.url');
        $this->username = $username ?? config('jasmin_client.username');
        $this->password = $password ?? config('jasmin_client.password');
    }

}

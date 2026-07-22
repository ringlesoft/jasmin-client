<?php

namespace RingleSoft\JasminCLient\Utility;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

/**
 * @mixin Log
 */
class Logger
{
    private static function enabled(): bool
    {
        return true;
    }

    public function __invoke(...$args)
    {
        $method = 'info';
        if (self::enabled()) {
            Log::{$method}(...$args);
        }
    }

    public static function info(string|Stringable $message, array|null $context = []): void
    {
        if (self::enabled()) {
            Log::info($message, $context);
        }
    }


}

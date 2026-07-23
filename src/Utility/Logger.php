<?php

namespace RingleSoft\JasminClient\Utility;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Stringable;

/**
 * @mixin Log
 */
class Logger
{
    private static function enabled(): bool
    {
        return (bool) Config::get('jasmin_client.logging.enabled', true);
    }

    public static function info(string|Stringable $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    public static function error(string|Stringable $message, array $context = []): void
    {
        self::write('error', $message, $context);
    }

    public static function debug(string|Stringable $message, array $context = []): void
    {
        self::write('debug', $message, $context);
    }

    private static function write(string $level, string|Stringable $message, array $context): void
    {
        if (self::enabled()) {
            Log::{$level}($message, $context);
        }
    }
}

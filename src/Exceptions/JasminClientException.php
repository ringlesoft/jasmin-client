<?php

namespace RingleSoft\JasminClient\Exceptions;

use Exception;
use Throwable;

class JasminClientException extends Exception
{

    public static function from(Exception|Throwable $exception, ?String $message = null): JasminClientException
    {
        return new self($message ?? $exception->getMessage(), $exception->getCode() ?? 0, $exception);
    }

}

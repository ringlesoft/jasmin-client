<?php

namespace RingleSoft\JasminClient\Exceptions;

use Exception;
use Throwable;

class JasminClientException extends Exception
{

    public static function from(Exception|Throwable $exception): JasminClientException
    {
        return new self($exception->getMessage(), $exception->getCode() ?? 0, $exception);
    }

}

<?php

namespace RingleSoft\JasminClient;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use RingleSoft\JasminClient\Models\Batch;
use RingleSoft\JasminClient\Models\Callbacks\BatchCallback;
use RingleSoft\JasminClient\Models\Callbacks\DeliveryCallback;
use RingleSoft\JasminClient\Models\IncomingMessage;
use RingleSoft\JasminClient\Models\Message;
use RingleSoft\JasminClient\Services\HttpService;
use RingleSoft\JasminClient\Services\RestService;
use RingleSoft\JasminClient\Services\SmppService;

class JasminClient
{

    /**
     * @param string|null $username
     * @param string|null $password
     * @param string|null $url
     * @return HttpService
     */
    public static function http(?string $username = null, ?string $password = null, ?string $url = null): HttpService
    {
        return new HttpService($username, $password, $url);
    }

    /**
     * @param string|null $username
     * @param string|null $password
     * @param string|null $url
     * @return RestService
     */
    public static function rest(?string $username = null, ?string $password = null, ?string $url = null): RestService
    {
        return new RestService($username, $password, $url);
    }

    /**
     * @return SmppService
     */
    public static function smpp(): SmppService
    {
        return new SmppService();
    }

    /**
     * @return Message
     */
    public static function message(): Message
    {
        return new Message();
    }

    /**
     * @return Batch
     */
    public static function batch(): Batch
    {
        return new Batch();
    }

    /**
     * Receive a batch callback from the jasmin gateway
     * @param Request $request
     * @param callable(BatchCallback $batch): bool $callback
     * @return Response
     */
    public static function receiveBatchCallback(Request $request, callable $callback): Response
    {
        return self::rest()->receiveBatchCallback($request, $callback);
    }

    /**
     * Receive a dlr callback from the jasmin gateway
     * @param Request $request
     * @param callable(DeliveryCallback $dlr): bool $callback
     * @return Response
     */
    public static function receiveDlrCallback(Request $request, callable $callback): Response
    {
        return self::http()->receiveDlrCallback($request, $callback);
    }

    /**
     * Receive a MO message callback from the jasmin gateway
     * @param Request $request
     * @param callable(IncomingMessage $message): bool $callback
     * @return Response
     */
    public static function receiveMessage(Request $request, callable $callback): Response
    {
        return self::http()->receiveMessage($request, $callback);
    }
}

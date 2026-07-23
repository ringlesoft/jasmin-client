<?php

namespace RingleSoft\JasminClient\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Services\SmppReceiver;
use RingleSoft\JasminClient\Utility\Logger;

class ListenSmppCommand extends Command
{
    protected $signature = 'jasmin-client:smpp:listen
                            {--once : Process one receive cycle and exit}
                            {--max-cycles= : Stop after this many receive cycles}
                            {--backoff= : Initial reconnect delay in seconds}';
    protected $description = 'Listen to inbound SMPP messages and delivery reports.';

    public function handle(SmppReceiver $receiver): int
    {
        $stop = false;
        $cycles = 0;
        $failures = 0;
        $maxCycles = $this->option('max-cycles');
        $initialBackoff = max(1, (int) ($this->option('backoff') ?? Config::get('jasmin_client.smpp.receiver.reconnect_delay_seconds', 5)));
        $maxBackoff = max($initialBackoff, (int) Config::get('jasmin_client.smpp.receiver.max_reconnect_delay_seconds', 60));

        if (defined('SIGINT') && defined('SIGTERM')) {
            $this->trap([SIGINT, SIGTERM], static function () use (&$stop): void {
                $stop = true;
            });
        }

        try {
            while (!$stop && !$this->option('once') && ($maxCycles === null || $cycles < (int) $maxCycles)) {
                try {
                    $receiver->listenOnce();
                    $cycles++;
                    $failures = 0;
                } catch (JasminClientException $exception) {
                    Logger::error('Jasmin SMPP receiver failed.', ['exception' => $exception]);
                    $delay = min($maxBackoff, $initialBackoff * (2 ** min($failures, 10)));
                    $failures++;
                    sleep($delay);
                }
            }

            if ($this->option('once')) {
                $receiver->listenOnce();
            }

            return self::SUCCESS;
        } catch (JasminClientException $exception) {
            Logger::error('Jasmin SMPP receiver failed.', ['exception' => $exception]);

            return self::FAILURE;
        } finally {
            $receiver->disconnect();
        }
    }
}

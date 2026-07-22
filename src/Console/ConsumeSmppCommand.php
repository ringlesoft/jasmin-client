<?php

namespace RingleSoft\JasminClient\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Services\SmppReceiver;

class ConsumeSmppCommand extends Command
{
    protected $signature = 'jasmin-client:smpp:consume {--once : Process one receive cycle and exit}';
    protected $description = 'Consume inbound SMPP messages and delivery reports.';

    public function handle(SmppReceiver $receiver): int
    {
        do {
            try {
                $receiver->listenOnce();
            } catch (JasminClientException $exception) {
                Log::error('Jasmin SMPP receiver failed.', ['exception' => $exception]);

                if ($this->option('once')) {
                    return self::FAILURE;
                }

                sleep(5);
            }
        } while (!$this->option('once'));

        return self::SUCCESS;
    }
}

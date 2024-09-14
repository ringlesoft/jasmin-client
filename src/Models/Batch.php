<?php

namespace RingleSoft\JasminClient\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Facades\JasminClient;
use RingleSoft\JasminClient\Models\Jasmin\SentBatch;
use RingleSoft\JasminClient\Models\Responses\JasminResponse;
use RingleSoft\JasminClient\Models\Responses\JasminRestResponse;

class Batch
{
    private array $batchConfig;
    private array $globals;
    private array $messages;

    public function __construct(?array $globals = null, ?array $messages = null)
    {
        $this->messages = $messages ?? [];
        $this->globals = $globals ?? [
            'dlr' => 'yes',
            'dlr-url' => Config::get('jasmin_client.dlr_callback_url'),
            'dlr-level' => 2
        ];
        $this->batchConfig = [
            "callback_url" => Config::get('jasmin_client.batch_callback_url'),
            "errback_url" => Config::get('jasmin_client.batch_errback_url')
        ];
    }

    /**
     * Add messages to the batch
     * @param array $messages
     * @return $this
     */
    public function messages(array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Add a message to the batch
     * @param Message $message
     * @return $this
     */
    public function addMessage(Message $message): self
    {
        $this->messages[] = $message->toArray();
        return $this;
    }

    /**
     * Add globals to the batch
     * @param array $globals
     * @return $this
     */
    public function globals(array $globals): self
    {
        $this->globals = $globals;
        return $this;
    }

    /**
     * Set the batch callback url
     * @param string $callbackUrl
     * @return $this
     */
    public function callbackUrl(string $callbackUrl): self
    {
        $this->batchConfig["callback_url"] = $callbackUrl;
        return $this;
    }

    /**
     * Set the batch error callback url
     * @param string $errbackUrl
     * @return $this
     */
    public function errbackUrl(string $errbackUrl): self
    {
        $this->batchConfig["errback_url"] = $errbackUrl;
        return $this;
    }

    /**
     * Set whether delivery reports should be tracked
     * @param $value
     * @return $this
     */
    public function trackDelivery($value = true): self
    {
        $this->globals['dlr'] = $value ? 'yes' :'no';
        return $this;
    }

    /**
     * Set the delivery callback url
     * @param string $dlrUrl
     * @return $this
     */
    public function deliveryUrl(string $dlrUrl): self
    {
        $this->globals['dlr-url'] = $dlrUrl;
        return $this;
    }

    /**
     * Set the delivery level
     * @param int $level
     * @return $this
     */
    public function deliveryLevel(int $level): self
    {
        if($level > 0 && $level < 4){
        $this->globals['dlr-level'] = $level;
        }
        return $this;
    }

    /**
     * Set the global originating number (Sender ID)
     * @param string $from
     * @return $this
     */
    public function from(string $from): self
    {
        $this->globals['from'] = $from;
        return $this;
    }


    /**
     * Smartly combine similar messages to reduce the size of the batch
     * @return $this
     */
    public function combineMessages(): self
    {
        $defaultFrom = $this->globals['from'] ?? null;
        $defaultDlr = $this->globals['dlr'] ?? null;
        $defaultDlrUrl = $this->globals['dlr-url'] ?? null;
        $defaultDlrLevel= $this->globals['dlr-level'] ?? 2;
        $messages = collect($this->messages)->groupBy(static function($i) use($defaultFrom, $defaultDlr, $defaultDlrUrl, $defaultDlrLevel) {
            return MD5( ($i['from'] ?? $defaultFrom). ($i['dlr'] ?? $defaultDlr) . ($i['dlr_url'] ?? $defaultDlrUrl) . ($i['dlr-level'] ?? $defaultDlrLevel). $i['content']);
        });
        $newMessages = $messages->map(static function($group) use($defaultDlr, $defaultDlrLevel, $defaultDlrUrl) {
            $message = $group->first();
            $message['to'] = $group->pluck('to')->toArray();
            if(isset($message['dlr-url']) && $message['dlr-url'] === $defaultDlrUrl){
                unset($message['dlr-url']);
            }
            if(isset($message['dlr-level']) && $message['dlr-level'] === $defaultDlrLevel){
                unset($message['dlr-level']);
            }
            if(isset($message['dlr']) && $message['dlr'] === $defaultDlr){
                unset($message['dlr']);
            }
            return $message;

        })->toArray();
        $this->messages = array_values($newMessages);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            "globals" => array_filter($this->globals),
            "messages" => array_filter($this->messages),
            "batch_config" => array_filter($this->batchConfig)
        ]);
    }


    /**
     * Send the batch
     * @return JasminResponse|null
     */
    public function send(): SentBatch
    {
        $this->combineMessages();
        $data = $this->toArray();
        try {
            $response =  JasminClient::rest()->sendBatch(
                messages: $data['messages'],
                globals: $data['globals'],
                batchConfig: $data['batch_config']
            );
            if($response->isSuccessful()) {
                return SentBatch::fromResponse($response);
            } else {
                throw new JasminClientException("Failed to send batch to jasmin");
            }
        } catch (JasminClientException $e) {
            Log::error($e->getMessage());
            return new JasminRestResponse($e->getMessage(), $e->getMessage(), null);
        }
    }
}

<?php

namespace RingleSoft\JasminClient\Models;

use Illuminate\Support\Facades\Config;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Facades\JasminClient;
use RingleSoft\JasminClient\Models\Jasmin\SentBatch;
use RingleSoft\JasminClient\Utility\Logger;

class Batch
{
    private array $batchConfig;
    private array $globals;
    private array $messages;

    private ?string $routeUsername = null;
    private ?string $routePassword = null;
    private ?string $routeUrl = null;

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
     * @param string|null $username
     * @param string|null $password
     * @param string|null $url
     * @return $this
     */
    public function withCredentials(?string $username = null, ?string $password = null, ?string $url = null): self
    {
        $this->routeUsername = $username ?? $this->routeUsername;
        $this->routePassword = $password ?? $this->routePassword;
        $this->routeUrl = $url ?? $this->routeUrl;
        return $this;
    }


    /**
     * Smartly combine similar messages to reduce the size of the batch
     * Convert `[["to" => "123", "content" => "Hello"],["to" => "456", "content" => "Hello"]]` to combined `[["to" => ["123", "456"], "content" => "Hello"]]`
     * @return $this
     */
    public function combineMessages(): self
    {
        $globalDefaults = array_intersect_key($this->globals, array_flip([
            'from',
            'dlr',
            'dlr-url',
            'dlr-level',
            'dlr-method',
        ]));
        $groups = [];

        foreach ($this->messages as $message) {
            $groupAttributes = $message;
            unset($groupAttributes['to']);

            foreach ($globalDefaults as $field => $value) {
                if (!array_key_exists($field, $groupAttributes)) {
                    $groupAttributes[$field] = $value;
                }
            }

            ksort($groupAttributes);
            $groupKey = serialize($groupAttributes);

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = $message;
                $groups[$groupKey]['to'] = [];
            }

            foreach (is_array($message['to']) ? $message['to'] : [$message['to']] as $recipient) {
                $groups[$groupKey]['to'][] = $recipient;
            }
        }

        foreach ($groups as &$message) {
            foreach ($globalDefaults as $field => $value) {
                if (array_key_exists($field, $message) && $message[$field] === $value) {
                    unset($message[$field]);
                }
            }
        }
        unset($message);

        $this->messages = array_values($groups);
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
     * @return SentBatch
     * @throws JasminClientException
     */
    public function send(): SentBatch
    {
        $this->combineMessages();
        $data = $this->toArray();
        try {
            $response =  JasminClient::rest($this->routeUsername, $this->routePassword, $this->routeUrl)
                ->sendBatch(
                messages: $data['messages'],
                globals: $data['globals'],
                batchConfig: $data['batch_config']
            );
            if($response->isSuccessful()) {
                return SentBatch::fromResponse($response);
            }
            throw new JasminClientException("Failed to send batch to jasmin");
        } catch (JasminClientException $e) {
            Logger::error('JasminClient batch send failed.', ['exception' => $e]);
            throw $e;
        }
    }
}

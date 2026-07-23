<?php

namespace RingleSoft\JasminClient\Services;

use Illuminate\Support\Facades\Config;
use RingleSoft\JasminClient\Contracts\JasminSmppContract;
use RingleSoft\JasminClient\Contracts\SmppTransport;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\Models\Jasmin\SentMessage;
use RingleSoft\JasminClient\Services\Transports\PhpSmppTransport;
use Throwable;

class SmppService implements JasminSmppContract
{
    private SmppTransport $transport;

    /**
     * @param array<string>|string|null $hosts
     * @throws JasminClientException
     */
    public function __construct(
        ?string $username = null,
        ?string $password = null,
        array|string|null $hosts = null,
        ?SmppTransport $transport = null,
    ) {
        $config = Config::get('jasmin_client.smpp', []);
        $configuredHosts = $config['hosts'] ?? [];
        $resolvedHosts = $this->normalizeHosts($hosts ?? $configuredHosts);
        $resolvedUsername = $username ?? ($config['username'] ?? null);
        $resolvedPassword = $password ?? ($config['password'] ?? null);

        if ($resolvedUsername === null || $resolvedUsername === '' || $resolvedPassword === null || $resolvedPassword === '') {
            throw new JasminClientException('SMPP credentials must be configured.');
        }

        $this->transport = $transport ?? new PhpSmppTransport($resolvedHosts, $resolvedUsername, $resolvedPassword);
    }

    /**
     * @param string $to
     * @param string $content
     * @param string $from
     * @return SentMessage
     * @throws JasminClientException
     */
    public function sendMessage(string $to, string $content, string $from): SentMessage
    {
        if ($to === '' || $content === '' || $from === '') {
            throw new JasminClientException('SMPP recipient, content, and sender are required.');
        }

        try {
            $messageIds = (array) $this->transport->send($to, $content, $from);
            $messageIds = array_values(array_filter($messageIds, static fn (mixed $id): bool => is_string($id) && $id !== ''));

            if ($messageIds === []) {
                throw new JasminClientException('SMPP server did not return a message ID.');
            }
            return new SentMessage('Success', $messageIds[0], $messageIds);
        } catch (JasminClientException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw JasminClientException::from($exception, 'Unable to send SMPP message.');
        }
    }

    /**
     * @param array<string>|string $hosts
     * @return array<string>
     * @throws JasminClientException
     */
    private function normalizeHosts(array|string $hosts): array
    {
        $hosts = is_array($hosts) ? $hosts : explode(',', $hosts);
        $hosts = array_values(array_filter(array_map('trim', $hosts)));

        if ($hosts === [] || array_filter($hosts, static fn (string $host): bool => str_contains($host, '://') || !str_contains($host, ':'))) {
            throw new JasminClientException('SMPP hosts must use the host:port format, for example 127.0.0.1:2775.');
        }

        return $hosts;
    }
}

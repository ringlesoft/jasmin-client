<?php

namespace RingleSoft\JasminClient\Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use PHPUnit\Framework\Attributes\Test;
use RingleSoft\JasminClient\Contracts\SmppTransport;
use RingleSoft\JasminClient\Exceptions\JasminClientException;
use RingleSoft\JasminClient\JasminClient;
use RingleSoft\JasminClient\Models\Message;
use RingleSoft\JasminClient\Services\SmppService;
use RingleSoft\JasminClient\Tests\TestCase;
use RuntimeException;

class SmppServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $app = new Container();
        $app->instance('config', new class {
            public function get(string $key, mixed $default = null): mixed
            {
                return $default;
            }
        });
        Facade::setFacadeApplication($app);
    }

    #[Test]
    public function it_sends_messages_through_the_transport_and_preserves_all_message_ids(): void
    {
        $transport = new class implements SmppTransport {
            public array $arguments = [];

            public function send(string $to, string $content, string $from): string|array
            {
                $this->arguments = [$to, $content, $from];

                return ['smpp-1', 'smpp-2'];
            }
        };

        $message = (new SmppService('user', 'password', '127.0.0.1:2775', $transport))
            ->sendMessage('00255711000000', 'Hello', 'INFO');

        $this->assertSame(['00255711000000', 'Hello', 'INFO'], $transport->arguments);
        $this->assertSame('Success', $message->status);
        $this->assertSame('smpp-1', $message->messageId);
        $this->assertSame(['smpp-1', 'smpp-2'], $message->messageIds);
    }

    #[Test]
    public function it_converts_transport_failures_to_a_package_exception(): void
    {
        $transport = new class implements SmppTransport {
            public function send(string $to, string $content, string $from): string|array
            {
                throw new RuntimeException('Connection refused');
            }
        };

        $service = new SmppService('user', 'password', '127.0.0.1:2775', $transport);

        $this->expectException(JasminClientException::class);
        $this->expectExceptionMessage('Unable to send SMPP message.');

        $service->sendMessage('255711000000', 'Hello', 'INFO');
    }

    #[Test]
    public function it_sends_a_fluent_message_via_smpp(): void
    {
        $app = Facade::getFacadeApplication();
        $app->instance(JasminClient::class, new class {
            public function smpp(?string $username = null, ?string $password = null, array|string|null $hosts = null): SmppService
            {
                return new SmppService('user', 'password', '127.0.0.1:2775', new class implements SmppTransport {
                    public function send(string $to, string $content, string $from): string|array
                    {
                        return 'smpp-123';
                    }
                });
            }
        });

        $sent = (new Message('255711000000', 'INFO', 'Hello'))
            ->via('smpp')
            ->send();

        $this->assertSame('smpp-123', $sent->messageId);
    }
}

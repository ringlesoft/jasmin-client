<?php

namespace RingleSoft\JasminClient\Tests\Unit;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use RingleSoft\JasminClient\Models\Batch;
use RingleSoft\JasminClient\Tests\TestCase;

class BatchTest extends TestCase
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

    public function test_combine_messages_merges_recipients_and_is_idempotent(): void
    {
        $batch = new Batch(
            globals: [
                'from' => 'INFO',
                'dlr' => 'yes',
                'dlr-url' => 'https://example.com/dlr',
                'dlr-level' => 2,
            ],
            messages: [
                [
                    'to' => '255711000000',
                    'content' => 'Hello',
                    'dlr' => 'yes',
                    'dlr-url' => 'https://example.com/dlr',
                    'dlr-level' => 2,
                ],
                [
                    'to' => ['255711000001', '255711000002'],
                    'content' => 'Hello',
                ],
            ],
        );

        $batch->combineMessages()->combineMessages();

        $this->assertSame([
            [
                'to' => ['255711000000', '255711000001', '255711000002'],
                'content' => 'Hello',
            ],
        ], $batch->toArray()['messages']);
    }

    public function test_combine_messages_keeps_messages_with_different_delivery_settings_separate(): void
    {
        $batch = new Batch(
            globals: [
                'dlr' => 'yes',
                'dlr-url' => 'https://example.com/default-dlr',
                'dlr-level' => 2,
            ],
            messages: [
                [
                    'to' => '255711000000',
                    'content' => 'Hello',
                    'dlr-url' => 'https://example.com/first-dlr',
                    'dlr-method' => 'GET',
                ],
                [
                    'to' => '255711000001',
                    'content' => 'Hello',
                    'dlr-url' => 'https://example.com/second-dlr',
                    'dlr-method' => 'POST',
                ],
            ],
        );

        $batch->combineMessages();

        $this->assertSame([
            [
                'to' => ['255711000000'],
                'content' => 'Hello',
                'dlr-url' => 'https://example.com/first-dlr',
                'dlr-method' => 'GET',
            ],
            [
                'to' => ['255711000001'],
                'content' => 'Hello',
                'dlr-url' => 'https://example.com/second-dlr',
                'dlr-method' => 'POST',
            ],
        ], $batch->toArray()['messages']);
    }
}

<?php

namespace Tests\Client;

use Tarre\Swish\Client\Swish;
use Tarre\Swish\Exceptions\InvalidConfigurationOptionException;
use Tests\TestCase;

class ConfigTest extends TestCase
{

    public function testLoadConfigWithInvalidOptions()
    {
        try {
            new Swish([
                'invalid_option' => 123
            ]);
        } catch (InvalidConfigurationOptionException $exception) {
            $this->assertTrue(true);
        }

    }

    public function testLoadValidConfig()
    {
        $client = new Swish([
            'merchant_number' => 123
        ]);

        $this->assertSame($client->merchant_number, 123);
    }

    public function testLoadDefaultConfig()
    {
        $client = new Swish;

        $this->assertSame($client->merchant_number, '123456789');
        $this->assertSame($client->base_uri, 'https://cpc.getswish.net/swish-cpcapi/api/');
        $this->assertSame($client->cert, storage_path('swish' . DIRECTORY_SEPARATOR . 'cert.pem'));
        $this->assertSame($client->key,  storage_path('swish' . DIRECTORY_SEPARATOR . 'key.pem'));

    }

}

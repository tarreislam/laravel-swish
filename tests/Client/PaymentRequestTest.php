<?php

namespace Tests\Client;

use Tarre\Swish\Client\Swish;
use Tests\TestCase;

class PaymentRequestTest extends TestCase
{
    protected function setupClient(): Swish
    {
        return new Swish([
            'base_uri' => 'https://mss.cpc.getswish.net/swish-cpcapi/api/v1/',
            'cert' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'Swish_Merchant_TestCertificate_1234679304.pem',
            'key' => [dirname(__DIR__) . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . 'Swish_Merchant_TestCertificate_1234679304.key', 'swish'], // 2nd param is password for key
            'merchant_number' => '1234679304'
        ]);
    }

    public function testPaymentRequest()
    {
        $client = $this->setupClient();

        $paymentResponse = $client->paymentRequest([
            'currency' => 'SEK',
            'amount' => 1
        ]);


        dd($paymentResponse->toArray());

    }
}

<?php

namespace Tests\Client;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Tarre\Swish\Client\Responses\PaymentResponse;
use Tarre\Swish\Client\Responses\PaymentStatusResponse;
use Tarre\Swish\Client\Swish;
use Tests\TestCase;

class PaymentRequestTest extends TestCase
{
    protected function setupClient($swapCerts = false): Swish
    {
        /*
         * Setup client with dev backend and test certs
         */
        if (!$swapCerts) {
            $cert = 'Swish_Merchant_TestCertificate_1234679304.pem';
            $key = 'Swish_Merchant_TestCertificate_1234679304.key';
        } else {
            $cert = 'Swish_Merchant_TestSigningCertificate_1234679304.pem';
            $key = 'Swish_Merchant_TestSigningCertificate_1234679304.key';
        }
        return new Swish([
            'base_uri' => 'https://mss.cpc.getswish.net/swish-cpcapi/api/',
            'cert' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . $cert,
            'key' => [dirname(__DIR__) . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . $key, 'swish'], // 2nd param is password for key
            'merchant_number' => '1234679304'
        ]);
    }

    public function testPaymentRequest()
    {
        $client = $this->setupClient();

        try {
            $paymentResponse = $client->paymentRequest([
                'amount' => 1.0,
                'payerAlias' => $this->fakeNumber()
            ]);
        } catch (GuzzleException $e) {
            $this->assertFalse(true, $e->getMessage());
        }

        $this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
        $this->assertNotNull($paymentResponse->id);
        $this->assertNull($paymentResponse->paymentRequestToken);
        $this->assertNotNull($paymentResponse->location);
    }

    public function testSimplePaymentRequest()
    {
        $client = $this->setupClient();

        $paymentResponse = $client->simplePaymentRequest($this->fakeNumber(), 150.0, 'hello');

        $this->assertInstanceOf(PaymentResponse::class, $paymentResponse);
        $this->assertNull($paymentResponse->paymentRequestToken); // sinve we provided a number
        $this->assertNotNull($paymentResponse->location);
    }

    public function testGetPaymentRequest()
    {
        $client = $this->setupClient();

        $paymentResponse = $client->paymentRequest([
            'amount' => 1.0,
        ]);

        $paymentStatusResponse = $client->paymentStatusRequest($paymentResponse->id);

        $this->assertInstanceOf(PaymentStatusResponse::class, $paymentStatusResponse);
        $this->assertInstanceOf(Carbon::class, $paymentStatusResponse->dateCreated);
        $this->assertSame('CREATED', $paymentStatusResponse->status);
    }


    public function testRefundRequest()
    {

        $client = $this->setupClient(false);

        $paymentResponse = $client->paymentRequest([
            'amount' => 1.0,
            'payerAlias' => $number = $this->fakeNumber(),
        ]);

        $paymentStatusResponse = $client->refundRequest([
            'originalPaymentReference' => $paymentResponse->id,
            'payerAlias' => $number,
            'amount' => 1.0
        ]);
    }
}

<?php

namespace Tests\Client;

use Carbon\Carbon;
use Tarre\Swish\Client\Responses\RefundStatusResponse;
use Tarre\Swish\Client\Swish;
use Tarre\Swish\Exceptions\ValidationFailedException;
use Tests\TestCase;

class RefundRequestTest extends TestCase
{
    protected function setupClient(): Swish
    {
        /*
         * Setup client with dev backend and test certs
         */
        return new Swish([
            'base_uri' => 'https://mss.cpc.getswish.net/swish-cpcapi/api/',
            'cert' => dirname(__DIR__) . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . "Swish_Merchant_TestCertificate_1234679304.pem",
            'key' => [dirname(__DIR__) . DIRECTORY_SEPARATOR . '_data' . DIRECTORY_SEPARATOR . "Swish_Merchant_TestCertificate_1234679304.key", 'swish'], // 2nd param is password for key
            'merchant_number' => '1234679304'
        ]);
    }

    public function testRefundRequestWithFailure()
    {
        $client = $this->setupClient();

        try {
            $refundResponse = $client->refundRequest([
            ]);
        } catch (ValidationFailedException $exception) {
            $this->assertTrue(true);
        }

    }

    public function testRefundRequestWithSuccess()
    {
        $client = $this->setupClient();

        $paymentResponse = $client->paymentRequest([
            'amount' => 1.0,
            'payerAlias' => $number = $this->fakeNumber(),
        ]);

        // wait for request to get auto paid
        do {
            $psr = $client->paymentStatusRequest($paymentResponse->id);
            sleep(1);
        } while ($psr->status != 'PAID');

        $refundResponse = $client->refundRequest([
            'originalPaymentReference' => $psr->paymentReference,
            'amount' => 1.0,
            'message' => 'here you go!',
        ]);

        $this->assertNotNull($refundResponse->id);
        $this->assertNotNull($refundResponse->location);
    }

    public function testSimpleRefundRequest()
    {
        $client = $this->setupClient();

        $paymentResponse = $client->paymentRequest([
            'amount' => 1.0,
            'payerAlias' => $number = $this->fakeNumber(),
        ]);

        // wait for request to get auto paid
        do {
            $psr = $client->paymentStatusRequest($paymentResponse->id);
            sleep(1);
        } while ($psr->status != 'PAID');

        $refundResponse = $client->simpleRefundRequest($psr->paymentReference, 1.0);

        $this->assertNotNull($refundResponse->id);
        $this->assertNotNull($refundResponse->location);
    }

    public function testGetPaymentRequest()
    {
        $client = $this->setupClient();

        $paymentResponse = $client->paymentRequest([
            'amount' => 1.0,
            'payerAlias' => $number = $this->fakeNumber(),
        ]);

        // wait for request to get auto paid
        do {
            $psr = $client->paymentStatusRequest($paymentResponse->id);
            sleep(1);
        } while ($psr->status != 'PAID');

        $refundResponse = $client->simpleRefundRequest($psr->paymentReference, 1.0);

        $refundStatusResponse = $client->refundStatusRequest($refundResponse->id);

        $this->assertInstanceOf(RefundStatusResponse::class, $refundStatusResponse);
        $this->assertInstanceOf(Carbon::class, $refundStatusResponse->dateCreated);
        $this->assertSame('CREATED', $refundStatusResponse->status);
        $this->assertSame('Refund', $refundStatusResponse->message);
    }
}

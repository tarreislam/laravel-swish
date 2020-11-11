<?php

namespace Tests\Client;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Event;
use Tarre\Swish\Client\Responses\PaymentResponse;
use Tarre\Swish\Client\Responses\PaymentStatusResponse;
use Tarre\Swish\Client\Swish;
use Tarre\Swish\Events\PaymentRequest\Created;
use Tarre\Swish\Events\PaymentRequest\Failed;
use Tarre\Swish\Exceptions\ValidationFailedException;
use Tests\TestCase;

class PaymentRequestTest extends TestCase
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

    public function testPaymentRequestWithFailure()
    {
        $client = $this->setupClient();

        Event::fake();

        Event::assertNotDispatched(Failed::class);
        Event::assertNotDispatched(Created::class);

        try {
            $client->paymentRequest([]);
        } catch (ValidationFailedException $exception) {
            $this->assertTrue(true);
            Event::assertDispatched(Failed::class);
            Event::assertNotDispatched(Created::class);
        }
    }

    public function testPaymentRequestWithSuccess()
    {
        $client = $this->setupClient();

        Event::fake();

        Event::assertNotDispatched(Failed::class);
        Event::assertNotDispatched(Created::class);


        try {
            $paymentResponse = $client->paymentRequest([
                'amount' => 1.0,
                'payerAlias' => $this->fakeNumber()
            ]);
        } catch (GuzzleException $e) {
            $this->assertFalse(true, $e->getMessage());
            Event::assertDispatched(Created::class);
            Event::assertNotDispatched(Failed::class);
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


}

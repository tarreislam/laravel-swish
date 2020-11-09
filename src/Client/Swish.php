<?php

namespace Tarre\Swish\Client;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Tarre\Swish\Client\Helpers\ResourceBase;
use Tarre\Swish\Client\Requests\PaymentRequest;
use Tarre\Swish\Client\Responses\PaymentResponse;
use Tarre\Swish\Exceptions\InvalidConfigurationOptionException;

class Swish
{
    public $base_uri = 'https://cpc.getswish.net/swish-cpcapi/api/v1/';
    public $cert;
    public $key;
    public $merchant_number;
    public $callback_base_url;

    public function __construct(array $config = null)
    {
        /*
         * Load initial config
         */
        $this->loadConfig(config('swish'));

        /*
         * Override with custom settings
         */
        if (is_array($config)) {
            $this->loadConfig($config);
        }
    }

    /**
     * @param array $config
     * @throws InvalidConfigurationOptionException
     */
    public function loadConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            } else {
                throw new InvalidConfigurationOptionException(sprintf('Unknown option "%s"', $key));
            }
        }
    }

    /**
     * @param array|PaymentRequest $requestData
     * @return PaymentResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentRequest($requestData): PaymentResponse
    {
        if (!$requestData instanceof PaymentRequest) {
            $mergedOptions = array_merge([
                'callbackUrl' => $this->callback_base_url,
                'payeeAlias' => $this->merchant_number,
            ], $requestData);

            $requestData = new PaymentRequest($mergedOptions);
        }

        $response = $this->makeRequest('POST', 'paymentrequests', $requestData);

        return new PaymentResponse(
            $response->getHeader('Location'),
            $response->getHeader('PaymentRequestToken')
        );
    }

    /**
     * @param string $to
     * @param float $amount
     * @param mixed $paymentRef
     * @param null $message
     * @param string $currency SEK
     * @return PaymentResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function simplePaymentRequest($to, $amount, $paymentRef, $message = null, $currency = 'SEK')
    {
        return $this->paymentRequest([
            'payeePaymentReference' => $paymentRef,
            'payerAlias' => $to,
            'amount' => $amount,
            'currency' => $currency,
            'message' => $message ?? $paymentRef
        ]);
    }

    /**
     * @param $method
     * @param $uri
     * @param ResourceBase $resourceBase
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeRequest($method, $uri, $resourceBase)
    {

        $gClient = new Client([
            'base_uri' => $this->base_uri,
            RequestOptions::CERT => $this->cert,
            RequestOptions::SSL_KEY => $this->key,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $requestData = $resourceBase->toArray();

        $response = $gClient->request($method, $uri, [
            RequestOptions::JSON => $requestData
        ]);

        return $response;
    }

}
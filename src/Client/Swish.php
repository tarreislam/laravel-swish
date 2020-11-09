<?php

namespace Tarre\Swish\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Tarre\Swish\Client\Helpers\ResourceBase;
use Tarre\Swish\Client\Requests\PaymentRequest;
use Tarre\Swish\Client\Responses\PaymentResponse;
use Tarre\Swish\Client\Responses\PaymentStatusResponse;
use Tarre\Swish\Exceptions\InvalidConfigurationOptionException;

class Swish
{
    public $base_uri = 'https://cpc.getswish.net/swish-cpcapi/api/v1/';
    public $cert;
    public $key;
    public $currency;
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
     * @throws GuzzleException
     */
    public function paymentRequest($requestData): PaymentResponse
    {
        if (!$requestData instanceof PaymentRequest) {

            $mergedOptions = array_merge([
                'callbackUrl' => $this->callback_base_url,
                'payeeAlias' => $this->merchant_number,
                'currency' => $this->currency
            ], $requestData);

            $requestData = new PaymentRequest($mergedOptions);
        }

        $response = $this->makeRequest('POST', 'paymentrequests', $requestData);

        $location = $response->getHeader('Location')[0];
        $paymentRequestToken = data_get($response->getHeader('PaymentRequestToken'), 0);
        $id = preg_replace('/.*\/(.*)/', '$1', $location);

        return new PaymentResponse(
            [
                'id' => $id,
                'location' => $location,
                'paymentRequestToken' => $paymentRequestToken
            ]
        );
    }

    /**
     * @param string $to
     * @param float $amount
     * @param string|null $paymentRef
     * @param string|null $message
     * @param string|null $currency SEK
     * @return PaymentResponse
     * @throws GuzzleException
     */
    public function simplePaymentRequest(string $to, float $amount, string $paymentRef, string $message = null, $currency = null): PaymentResponse
    {
        $request = [
            'payeePaymentReference' => $paymentRef,
            'payerAlias' => $to,
            'amount' => $amount,
            'message' => $message ?? $paymentRef
        ];

        if (!is_null($currency)) {
            $request['currency'] = $currency;
        }

        return $this->paymentRequest($request);
    }

    /**
     * @param string $paymentRequestToken
     * @return PaymentStatusResponse
     * @throws GuzzleException
     */
    public function paymentStatusRequest(string $paymentRequestToken): PaymentStatusResponse
    {
        $response = $this->makeRequest('GET', "paymentrequests/$paymentRequestToken");

        $json = json_decode($response->getBody()->getContents(), true);

        return new PaymentStatusResponse($json);
    }

    /**
     * @param $method
     * @param $uri
     * @param ResourceBase $resourceBase
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function makeRequest($method, $uri, $resourceBase = null)
    {

        $gClient = new Client([
            'base_uri' => $this->base_uri,
            RequestOptions::CERT => $this->cert,
            RequestOptions::SSL_KEY => $this->key,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json'
            ]
        ]);

        if ($resourceBase instanceof ResourceBase) {
            $requestData = $resourceBase->toArray();
        } else {
            $requestData = [];
        }

        $response = $gClient->request($method, $uri, [
            RequestOptions::JSON => $requestData
        ]);

        return $response;
    }

}
<?php

namespace Tarre\Swish\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Tarre\Swish\Client\Helpers\ResourceBase;
use Tarre\Swish\Client\Requests\PaymentRequest;
use Tarre\Swish\Client\Requests\RefundRequest;
use Tarre\Swish\Client\Responses\PaymentResponse;
use Tarre\Swish\Client\Responses\PaymentStatusResponse;
use Tarre\Swish\Client\Responses\RefundResponse;
use Tarre\Swish\Exceptions\InvalidConfigurationOptionException;

class Swish
{
    public $base_uri = 'https://cpc.getswish.net/swish-cpcapi/api/';
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
     * Creates a V2 payment request
     *
     * @param array|PaymentRequest $requestData
     * @return PaymentResponse
     * @throws GuzzleException
     * @throws \Tarre\Swish\Exceptions\ValidationFailedException
     */
    public function paymentRequest($requestData): PaymentResponse
    {
        if (!$requestData instanceof PaymentRequest) {

            $mergedOptions = array_merge([
                'id' => null,
                'callbackUrl' => $this->callback_base_url,
                'payeeAlias' => $this->merchant_number,
                'currency' => $this->currency
            ], $requestData);

            $requestData = new PaymentRequest($mergedOptions);
        }

        $requestData->validate();

        $response = $this->makeRequest('PUT', "v2/paymentrequests/{$requestData->id}", $requestData);

        $commonData = $this->extractCommonData($response);

        return new PaymentResponse(
            $commonData
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

        if (is_null($paymentRef)) {
            unset($request['payeePaymentReference']);
        }

        return $this->paymentRequest($request);
    }

    /**
     * @param string $id
     * @return PaymentStatusResponse
     * @throws GuzzleException
     */
    public function paymentStatusRequest(string $id): PaymentStatusResponse
    {
        $response = $this->makeRequest('GET', "v1/paymentrequests/$id");

        $json = json_decode($response->getBody()->getContents(), true);

        return new PaymentStatusResponse($json);
    }

    /**
     * Creates a V2 refund request
     *
     * @param array|RefundRequest $requestData
     * @return RefundResponse
     * @throws GuzzleException
     * @throws \Tarre\Swish\Exceptions\ValidationFailedException
     */
    public function refundRequest($requestData): RefundResponse
    {
        if (!$requestData instanceof RefundRequest) {

            $mergedOptions = array_merge([
                'callbackUrl' => $this->callback_base_url,
                'payerAlias' => $this->merchant_number,
                'currency' => $this->currency,
                'id' => null,
            ], $requestData);

            $requestData = new RefundRequest($mergedOptions);
        }

        $requestData->validate();

        $response = $this->makeRequest('PUT', "v2/refunds/{$requestData->id}", $requestData);

        $commonData = $this->extractCommonData($response);

        unset($commonData['paymentRequestToken']);

        return new RefundResponse(
            $commonData
        );
    }

    /**
     * @param string $originalPaymentReference
     * @param float $amount
     * @param string $message
     * @return RefundResponse
     * @throws GuzzleException
     */
    public function simpleRefundRequest(string $originalPaymentReference, float $amount, string $message = 'Refund'): RefundResponse
    {
        return $this->refundRequest([
            'originalPaymentReference' => $originalPaymentReference,
            'amount' => $amount,
            'message' => $message,
        ]);
    }

    /**
     * @param $method
     * @param $uri
     * @param ResourceBase $resourceBase
     * @param string $contentType
     * @return ResponseInterface
     * @throws GuzzleException
     */
    protected function makeRequest($method, $uri, $resourceBase = null, $contentType = 'application/json')
    {
        $gClient = new Client([
            'base_uri' => $this->base_uri,
            RequestOptions::CERT => $this->cert,
            RequestOptions::SSL_KEY => $this->key,
            RequestOptions::HEADERS => [
                'Content-Type' => $contentType
            ]
        ]);

        if ($resourceBase instanceof ResourceBase) {
            $requestData = $resourceBase->toArray();
        } else {
            $requestData = [];
        }

        $requestOptions = [];

        if (!is_null($resourceBase)) {
            $requestOptions[RequestOptions::JSON] = $requestData;
        }

        $response = $gClient->request($method, $uri, $requestOptions);

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function extractCommonData(ResponseInterface $response)
    {
        $location = $response->getHeader('Location')[0];
        $paymentRequestToken = data_get($response->getHeader('PaymentRequestToken'), 0);
        $id = preg_replace('/.*\/(.*)/', '$1', $location);

        return [
            'location' => $location,
            'id' => $id,
            'paymentRequestToken' => $paymentRequestToken
        ];
    }

}
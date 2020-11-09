<?php

namespace Tarre\Swish\Client\Responses;

use Illuminate\Contracts\Support\Arrayable;

class PaymentResponse implements Arrayable
{
    public $location;
    public $paymentRequestToken;

    public function __construct($location, $paymentRequestToken)
    {
        $this->location = $location;
        $this->paymentRequestToken = $paymentRequestToken;
    }

    public function toArray()
    {
        return [
            'location' => $this->location,
            'paymentRequestToken' => $this->paymentRequestToken
        ];
    }
}
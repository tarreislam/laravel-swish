<?php

namespace Tarre\Swish\Client\Requests;

use Tarre\Swish\Client\Helpers\ResourceBase;

class PaymentRequest extends ResourceBase
{
    public $payeePaymentReference;
    public $callbackUrl;
    public $payerAlias;
    public $payeeAlias;
    public $amount;
    public $currency;
    public $message;

}
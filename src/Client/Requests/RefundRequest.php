<?php

namespace Tarre\Swish\Client\Requests;

use Tarre\Swish\Client\Helpers\ResourceBase;

class RefundRequest extends ResourceBase
{
    public $originalPaymentReference;
    public $callbackUrl;
    public $payerAlias;
    public $amount;
    public $currency;
    public $messageResponse;
    public $payerPaymentReference;

}
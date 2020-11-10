<?php

namespace Tarre\Swish\Client\Requests;

use Tarre\Swish\Client\Helpers\Helper;
use Tarre\Swish\Client\Helpers\ResourceBase;

class PaymentRequest extends ResourceBase
{
    public $id;
    public $payeePaymentReference;
    public $callbackUrl;
    public $payerAlias;
    public $payeeAlias;
    public $payerSSN;
    public $payerAgeLimit;
    public $amount;
    public $currency;
    public $message;

    public function transforms(): array
    {
        return [
            'id' => function ($id) {
                return !$id ? Helper::SwishOrderedUUID4() : $id;
            },
        ];
    }

}
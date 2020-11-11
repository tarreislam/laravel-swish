<?php

namespace Tarre\Swish\Client\Requests;

use Tarre\Swish\Client\Helpers\Helper;
use Tarre\Swish\Client\Helpers\ResourceBase;

class RefundRequest extends ResourceBase
{
    public $id;
    public $originalPaymentReference;
    public $callbackUrl;
    public $payerAlias;
    public $amount;
    public $currency;
    public $message;
    public $payerPaymentReference;

    public function requiredFields(): array
    {
        return [
            'originalPaymentReference',
            'amount',
            'message',
            'callbackUrl',
            'payerAlias',
            'currency',
            'id'
        ];
    }

    public function transforms(): array
    {
        return [
            'id' => function ($id) {
                return !$id ? Helper::SwishOrderedUUID4() : $id;
            },
        ];
    }
}
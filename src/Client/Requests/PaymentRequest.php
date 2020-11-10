<?php

namespace Tarre\Swish\Client\Requests;

use Illuminate\Support\Str;
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

    public function __construct(array $options = [])
    {
        /*
         * Transform
         */
        if(!isset($options['id'])){
            $options['id'] = (string) strtoupper(str_replace('-', '', Str::orderedUuid()));
        }
        parent::__construct($options);
    }

}
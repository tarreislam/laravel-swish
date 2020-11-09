<?php

namespace Tarre\Swish\Client\Responses;

use Carbon\Carbon;
use Tarre\Swish\Client\Helpers\ResourceBase;

/**
 * @property mixed id
 * @property mixed payeePaymentReference
 * @property mixed paymentReference
 * @property mixed callbackUrl
 * @property mixed payerAlias
 * @property mixed payeeAlias
 * @property mixed amount
 * @property mixed currency
 * @property mixed message
 * @property mixed status
 * @property Carbon dateCreated
 * @property null|Carbon datePaid
 * @property mixed errorCode
 * @property mixed errorMessage
 */
class PaymentStatusResponse extends ResourceBase
{
    public $id;
    public $payeePaymentReference;
    public $paymentReference;
    public $callbackUrl;
    public $payerAlias;
    public $payeeAlias;
    public $amount;
    public $currency;
    public $message;
    public $status;
    public $dateCreated;
    public $datePaid;
    public $errorCode;
    public $errorMessage;

    public function __construct(array $options = [])
    {
        /*
         * transform
         */
        $options['dateCreated'] = Carbon::parse($options['dateCreated']);
        $options['datePaid'] =  isset($options['datePaid']) ? Carbon::parse($options['datePaid']) : null;
        parent::__construct($options);
    }
}
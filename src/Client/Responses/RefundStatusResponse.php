<?php

namespace Tarre\Swish\Client\Responses;

use Carbon\Carbon;
use Tarre\Swish\Client\Helpers\ResourceBase;

/**
 * @property mixed id
 * @property mixed payeePaymentReference
 * @property mixed originalPaymentReference
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
 * @property mixed additionalInformation
 * @property mixed errorMessage
 */
class RefundStatusResponse extends ResourceBase
{
    public $id;
    public $payeePaymentReference;
    public $originalPaymentReference;
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
    public $additionalInformation;
    public $errorMessage;

    public function transforms(): array
    {
        return [
            'dateCreated' => function ($dateCreated) {
                return Carbon::parse($dateCreated);
            },
            'datePaid' => function ($datePaid) {
                return is_null($datePaid) ? null : Carbon::parse($datePaid);
            }
        ];
    }
}
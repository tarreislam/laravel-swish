<?php

namespace Tarre\Swish\Client\Responses;

use Tarre\Swish\Client\Helpers\ResourceBase;

/**
 * @property string location
 * @property string id
 */
class RefundResponse extends ResourceBase
{
    public $id; // an ID for retrieveing the status of the payment request
    public $paymentRequestToken; // Returned when creating an m-commerce payment request. The token to use when opening the Swish app

}
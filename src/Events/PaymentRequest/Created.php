<?php


namespace Tarre\Swish\Events\PaymentRequest;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Tarre\Swish\Client\Requests\PaymentRequest;
use Tarre\Swish\Client\Responses\PaymentResponse;

/**
 * @property PaymentRequest request
 * @property PaymentResponse paymentResponse
 */
class Created implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $request;
    public $paymentResponse;

    public function __construct(PaymentRequest $request, PaymentResponse $paymentResponse)
    {
        $this->request = $request;
        $this->paymentResponse = $paymentResponse;
    }

    public function broadCastAs()
    {
        return 'paymentRequest.created';
    }

    public function broadcastOn()
    {
        return new Channel('swish');
    }

    public function broadcastWhen()
    {
        return config('swish.broadcast_events', true);
    }
}
<?php


namespace Tarre\Swish\Events\RefundRequest;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Tarre\Swish\Client\Requests\RefundRequest;
use Tarre\Swish\Client\Responses\RefundResponse;

/**
 * @property RefundRequest request
 * @property RefundResponse RefundResponse
 */
class Created implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $request;
    public $RefundResponse;

    public function __construct(RefundRequest $request, RefundResponse $RefundResponse)
    {
        $this->request = $request;
        $this->RefundResponse = $RefundResponse;
    }

    public function broadCastAs()
    {
        return 'refundRequest.created';
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
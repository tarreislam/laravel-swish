<?php


namespace Tarre\Swish\Events\RefundRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Psr\Http\Message\ResponseInterface;
use Tarre\Swish\Client\Requests\RefundRequest;
use Tarre\Swish\Exceptions\ValidationFailedException;

/**
 * @property RefundRequest request
 * @property ResponseInterface|ValidationFailedException|mixed $error
 */
class Failed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $request;
    public $error;

    public function __construct(RefundRequest $request, $error)
    {
        $this->request = $request;
        $this->error = $error;
    }

    public function broadCastAs()
    {
        return 'refundRequest.failed';
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
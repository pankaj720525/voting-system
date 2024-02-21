<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class Votes implements ShouldBroadcast
{
    public $polls;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($polls)
    {
        $this->polls = $polls;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('poll-votes');
    }
}

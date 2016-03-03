<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Heartbeat extends Event
{
    use SerializesModels;
    
    public $user;
    public $timestamp;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $timestamp = null, $data = [])
    {
        $this->user = $user;
        $this->timestamp = $timestamp;
        $this->data = $data;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}

<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WorkingUpdated extends Event
{
    use SerializesModels;
    
    public $uuid;
    public $workingFile;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($workingFile, $uuid)
    {
        $this->workingFile = $workingFile;
        $this->uuid = $uuid;
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

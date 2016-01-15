<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventSessionUpdated extends Event
{
    use SerializesModels;
    
    public $EventSession;
    public $request;
    public $uuid;
    public $workingFile;
    public $masterfile;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($eventsession, $request, $uuid, $workingFile = null, $masterFile = null)
    {
        $this->EventSession = $eventsession;
        $this->request = $request;
        $this->uuid = $uuid;
        $this->workingFile = $workingFile;
        $this->masterFile = $masterFile;
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

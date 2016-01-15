<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MasterUpdated extends Event
{
    use SerializesModels;

    public $uuid;
    public $newFilePath;
    public $oldFilePath;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldFilePath, $newFilePath, $uuid = null)
    {
        $this->oldFilePath = $oldFilePath;
        $this->newFilePath = $newFilePath;
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

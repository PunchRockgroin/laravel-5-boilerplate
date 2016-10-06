<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class IssueAlert extends Event
{
    use SerializesModels;

	public $message; 
	public $target;
	public $attachments;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message = '', $target = '', $attachments = [])
    {
		//Using Makins/Slack
        $this->message = $message;
		$this->target  = $target;
		$this->attachments = $attachments;
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

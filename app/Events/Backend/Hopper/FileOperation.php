<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FileOperation extends Event
{
    use SerializesModels;
	
	public $filename;
	public $type;
    public $entity;
    public $event;
	public $icon;
	public $class;
	public $links;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($filename, $type, $entity, $event, $icon = 'file', $class = 'blue', $links = [])
    {
        //
		$this->filename = $filename;
		$this->type = $type;
		$this->entity = $entity;
		$this->event = $event;
		$this->icon = $icon;
		$this->class = $class;
		$this->links = $links;
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

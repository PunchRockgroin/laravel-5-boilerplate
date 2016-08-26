<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VisitUpdated extends Event
{
    use SerializesModels;
    
    public $id;
	public $visit;
    public $event;
	public $icon;
	public $class;
    public $notes;
    public $filename;
    public $tasks;
    public $user;
	public $request;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $visit, $event, $icon = 'plus', $class = 'green', $notes = '', $filename = '', $tasks = [], $user = 'Hopper', $request = null)
    {
        
        
        $this->id = $id;
        $this->visit = $visit;
        $this->event = $event;
		$this->icon = $icon;
		$this->class = $class;
		$this->notes = $notes;
		$this->filename = $filename;
		$this->tasks = $tasks;
        if(\Auth::check()){
            $this->user = \Auth::user()->name;
        }
		$this->request = $request;
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

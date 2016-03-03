<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class FileEntityUpdated extends Event
{
    use SerializesModels;

    public $id;
    public $event;
    public $request;
    public $filename;
    public $notes;
    public $tasks;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $event, $notes = '', $filename = null, $tasks = [], $user = 'Hopper', $request = null)
    {
        //
//        $this->request = $request;
        $this->id = $id;
        $this->event = $event;
        $this->request = $request;
        $this->filename = $filename;
        $this->notes = $notes;
        $this->tasks = $tasks;
        $this->user = $user;
        if(\Auth::check()){
            $this->user = \Auth::user()->name;
        }

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

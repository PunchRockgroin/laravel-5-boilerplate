<?php

namespace App\Events\Backend\Hopper;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Illuminate\Http\Request;
use App\Services\Hopper\HopperFile;

class FileUploaded extends Event
{
    use SerializesModels;

    public $request;
    public $hopperFile;
    public $newFileName;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request,$newFileName)
    {
        //
//        $this->request = $request;
        $this->newFileName = $newFileName;
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

<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\EventSessionUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Exception;
use Session;

use App\Jobs\Hopper\HopperUpdateMasterDriveTable;

use App\Services\Drive;
use App\Services\Hopper;

class EventSessionUpdatedHandler 
{
    
    protected $drive;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->drive = new Drive;
    }

    /**
     * Handle the event.
     *
     * @param  EventSessionUpdated  $event
     * @return void
     */
    public function handle(EventSessionUpdated $event)
    {
        \Log::info('Event Session ID Updated: '.$event->EventSession['id']);            
//        \Log::info('Master File: '.$event->masterFile);            
//        \Log::info('Working File: '.$event->workingFile);            
    }
}

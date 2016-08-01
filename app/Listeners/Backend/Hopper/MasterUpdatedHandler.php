<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\MasterUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\Hopper\HopperFile;

class MasterUpdatedHandler
{
    protected $hopperFile;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->hopperFile = new HopperFile();
    }

    /**
     * Handle the event.
     *
     * @param  MasterUpdated  $event
     * @return void
     */
    public function handle(MasterUpdated $event)
    {
        //
        
        \Log::info('Master File: ' . $event->oldFilePath . ' to ' . $event->newFilePath );  
        
//        $this->hopperFile->copyfile($event->oldFilePath, $event->newFilePath);
        
        
    }
}

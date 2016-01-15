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
        $this->driver_storage_path = \Storage::disk('hopper')->getDriver()->getAdapter()->getPathPrefix();
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
        
        \Log::info('Master File: ' . basename( $event->newFilePath ) );  
        
        $fd = fopen($this->driver_storage_path.$event->oldFilePath, "rb");
        \Storage::disk('hopper')
                    ->put($event->newFilePath, $fd);
        fclose($fd);
        
    }
}

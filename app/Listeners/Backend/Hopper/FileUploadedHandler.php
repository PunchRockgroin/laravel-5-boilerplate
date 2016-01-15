<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\FileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\HopperFile;

class FileUploadedHandler implements ShouldQueue
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
     * @param  FileUploaded  $event
     * @return void
     */
    public function handle(FileUploaded $event)
    {
        //
        \Log::info('File Uploaded: '.$event->newFileName);
         
        try{
            $this->hopperFile->_moveHopperTemporaryToHopperWorking($event->newFileName);
        }catch(Exception $e){
               \Log::error($exception);
            \Debugbar::addException($e);
        }
//        $this->hopperfile->_moveHopperTemporaryToHopperWorking($newFileName);
        
    }
}

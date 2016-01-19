<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\FileEntityUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Hopper\HopperFile;

class FileEntityUpdatedHandler implements ShouldQueue {

    protected $hopperFile;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
        $this->hopperFile = new HopperFile();
    }

    /**
     * Handle the event.
     *
     * @param  FileUploaded  $event
     * @return void
     */
    public function handle(FileEntityUpdated $event) {
        //
//        \Log::info('File Uploaded: '.$event->filename);
//        if(!empty($event->tasks)){
//          foreach($event->tasks as $key => $task){
//            try{
//              $taskStatus = 'complete';
//              $event->tasks[$key]['status'] = $taskStatus;
//            }catch(Exception $e){
//              \Log::error($e);
//              $event->tasks[$key]['status'] = $e->getMessage(); 
//            }
//          }
//        }
        
        $user = 'Hopper';
        if(!empty(\Auth::user()->email)){
            $user = \Auth::user()->email;
        }

        try {
            $History = [
                'event' => $event->event,
                'user' => $user,
                'filename' => $event->filename,
                'notes' => $event->notes,
                'tasks' => $event->tasks,
                'timestamp' => \Carbon\Carbon::now(),
            ];

            $FileEntity = \App\Models\Hopper\FileEntity::find($event->id);
            $OldHistory = $FileEntity->history;
            $OldHistory[] = $History;

            $FileEntity->update(['history' => $OldHistory]);
//            $this->hopperFile->_moveHopperTemporaryToHopperWorking($event->newFileName);
        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }
//        $this->hopperfile->_moveHopperTemporaryToHopperWorking($newFileName);
    }

}

<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\FileEntityUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Hopper\HopperFile;

class FileEntityUpdatedHandler implements ShouldQueue {

    protected $hopperfile;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
        $this->hopperfile = new HopperFile();
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
                

        try {
            $History = [
                'event' => $event->event,
                'notes' => $event->notes,
                'filename' => $event->filename,
                'tasks' => $event->tasks,
                'user' => $event->user,
                'timestamp' => \Carbon\Carbon::now(),
            ];

            $FileEntity = \App\Models\Hopper\FileEntity::find($event->id);
            if(count($FileEntity)){
                $OldHistory = $FileEntity->history;
                $OldHistory[] = $History;
                $updateData = ['history' => $OldHistory];
                
                if(!empty($event->tasks)){
                    $this->performTasks($event->tasks, $updateData);
                }
                
                $FileEntity->update($updateData);
            }
            
//            $this->hopperfile->_moveHopperTemporaryToHopperWorking($event->newFileName);
        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }
//        $this->hopperfile->_moveHopperTemporaryToHopperWorking($newFileName);
    }
    
    
    private function performTasks($tasks, &$updateData){
        
        foreach($tasks as $task => $taskdata){
            switch ($task) {
                case 'update_path':
                    $updateData['path'] = $taskdata;
                    break;
                default:
                    break;
            }  
        }
    }

}

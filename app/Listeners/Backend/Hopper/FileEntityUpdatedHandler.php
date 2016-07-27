<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\FileEntityUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Hopper\HopperFile;

use Vinkla\Pusher\Facades\Pusher;

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
		
        try {
//            $History = [
//                'event' => $event->event,
//                'notes' => $event->notes,
//                'filename' => $event->filename,
//                'tasks' => $event->tasks,
//                'user' => $event->user,
//                'timestamp' => \Carbon\Carbon::now(),
//            ];
//
//            $FileEntity = \App\Models\Hopper\FileEntity::find($event->id);
//            if(count($FileEntity)){
//                $OldHistory = $FileEntity->history;
//                $OldHistory[] = $History;
//                $updateData = ['history' => $OldHistory];
//				
//				$this->performTasks($event->tasks, $updateData);
//                $FileEntity->update($updateData);
//				
//            }
			Pusher::trigger('hopper-channel', 'file-entity-'.$event->id, ['message' => $event->notes]);
        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }
    }
    
    
    private function performTasks($tasks, &$updateData){
		if(empty($tasks) && ! is_array($tasks)){
		   return;
		}
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

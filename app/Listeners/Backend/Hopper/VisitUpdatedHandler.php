<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\EventSessionUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Exception;
use Session;


use App\Services\Hopper\Contracts\HopperFileContract;

class VisitUpdatedHandler 
{
    
    protected $hopperfile;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(HopperFileContract $hopperfile) {
        //
        $this->hopperfile = $hopperfile;
    }

    /**
     * Handle the event.
     *
     * @param  VisitUpdated  $event
     * @return void
     */
    public function handle(VisitUpdated $event) {
        //

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
                'user' => $event->user,
                'filename' => $event->filename,
                'notes' => $event->notes,
                'tasks' => $event->tasks,
                'timestamp' => \Carbon\Carbon::now(),
            ];
            
            $Visit = \App\Models\Hopper\Visit::find($event->id);
            if(count($Visit)){
                $OldHistory = $Visit->history;
                $OldHistory[] = $History;
                $Visit->update(['history' => $OldHistory]);
            }
        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }

    }
}

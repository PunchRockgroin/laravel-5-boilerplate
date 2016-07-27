<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\VisitUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Exception;
use Session;


use App\Services\Hopper\Contracts\HopperFileContract;
use Vinkla\Pusher\PusherManager;

class VisitUpdatedHandler 
{
    
    protected $hopperfile;
    protected $pusher;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(HopperFileContract $hopperfile, PusherManager $pusher) {
        //
        $this->hopperfile = $hopperfile;
		$this->pusher = $pusher;
    }

    /**
     * Handle the event.
     *
     * @param  VisitUpdated  $event
     * @return void
     */
    public function handle(VisitUpdated $event) {

        try {
//            $History = [
//                'event' => $event->event,
//                'user' => $event->user,
//                'filename' => $event->filename,
//                'notes' => $event->notes,
//                'tasks' => $event->tasks,
//                'timestamp' => \Carbon\Carbon::now(),
//            ];
//            
//            $Visit = \App\Models\Hopper\Visit::find($event->id);
//            if(count($Visit)){
//                $OldHistory = $Visit->history;
//                $OldHistory[] = $History;
//                $Visit->update(['history' => $OldHistory]);
//            }
		$payload = [
			'id' => $event->id
		];
		
		$this->pusher->trigger('hopper_channel', 'visit_status', ['message' => 'update', 'payload' => $payload ]);

        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }

    }
}

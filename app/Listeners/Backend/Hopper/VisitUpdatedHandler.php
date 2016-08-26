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
			
			
			history()->log(
				'Visit',
				$event->event.' <strong>$1</strong> for <strong>$2</strong>',
				$event->visit->id,
				$event->icon,
				'bg-'.$event->class,
				[
					'link' => ['admin.visit.invoice', $event->visit->id, [$event->visit->id]],
					'link2' => ['admin.eventsession.edit', $event->visit->session_id, [$event->visit->session_id]],
				]
			);
		
		//Pusher
		$payload = [
			'event' => $event->event,
			'id' => $event->id,
			'userID' => 0,
			'user' => 'Hopper',
			'icon' => $event->icon,
			
		];
		
		if(\Auth::check()){
            $payload['userID'] = \Auth::user()->id;
            $payload['user'] = \Auth::user()->name;
        }

		
		
		$this->pusher->trigger('hopper_channel', 'visit_status', ['message' => 'update', 'payload' => $payload ]);

        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }

    }
}

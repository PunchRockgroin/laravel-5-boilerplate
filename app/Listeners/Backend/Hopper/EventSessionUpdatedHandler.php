<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\EventSessionUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Exception;
use Session;


use App\Services\Hopper\Contracts\HopperFileContract;

class EventSessionUpdatedHandler 
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
     * @param  EventSessionUpdated  $event
     * @return void
     */
    public function handle(EventSessionUpdated $event) {

		try {
			history()->log(
				'Event Session',
				$event->event.' <strong>$1</strong>',
				$event->eventsession->id,
				'plus',
				'bg-green',
				[
					'link' => ['admin.eventsession.edit', $event->eventsession->session_id, [$event->eventsession->id]]
				]
			);
        } catch (Exception $e) {
            \Log::error($e);
        }

    }
}

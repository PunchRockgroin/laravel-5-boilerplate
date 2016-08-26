<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\FileOperation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Vinkla\Pusher\PusherManager;

class FileOperationHandler
{
	protected $pusher;
	
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PusherManager $pusher)
    {
        
		$this->pusher = $pusher;
    }

    /**
     * Handle the event.
     *
     * @param  FileOperation  $event
     * @return void
     */
    public function handle(FileOperation $event)
    {
		//.' <strong>$1</strong>'
//      [
//			'link' => ['admin.eventsession.edit', $event->eventsession->session_id, [$event->eventsession->id]]
//		]
		try {
			$history = history()->log(
				$event->type,
				$event->event,
				$event->entity,
				$event->icon,
				'bg-'.$event->class,
				$event->links
			);
		//Pusher
		$payload = [
			'filename' => $event->filename,
			'event' => preg_replace('/\s+/S', " ", strip_tags(history()->buildItem($history), '<a><strong><i>')),
			//'id' => $event->entity->id,
			'userID' => 0,
			'user' => 'Hopper',
			'icon' => $event->icon,
			
		];
		
		if(\Auth::check()){
            $payload['userID'] = \Auth::user()->id;
            $payload['user'] = \Auth::user()->name;
        }

		
		
		$this->pusher->trigger('hopper_channel', 'file_status', ['message' => 'success', 'payload' => $payload ]);
			
        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }
    }
}

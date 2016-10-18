<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\Heartbeat;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Cache;

use Vinkla\Pusher\PusherManager;

class HeartbeatHandler
{

    protected $pusher;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(PusherManager $pusher)
    {
        //
        $this->pusher = $pusher;
    }

    /**
     * Handle the event.
     *
     * @param  Heartbeat  $event
     * @return void
     */
    public function handle(Heartbeat $event)
    {
		if(! config('hopper.heartbeat', false) ){
			return;
		}
		//Use Pusher for Heartbeat
		if(config('hopper.heartbeat_handler', false) === 'pusher'){
			$this->pusher->trigger('private-hopper_channel', 'heartbeat', ['message' => 'ok']);
		}
    }
}

<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\Heartbeat;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\Cache;

class HeartbeatHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Heartbeat  $event
     * @return void
     */
    public function handle(Heartbeat $event)
    {
       
        try {
             Cache::forever('heartbeat-'.md5($event->user->email), json_encode([
                    'route' => request()->route()->getName(),
                    'parameters' => request()->segments(),
                    'timestamp' => \Carbon\Carbon::now()->toIso8601String(),
                ]));
        } catch (Exception $e) {
            \Log::error($e);
//            \Debugbar::addException($e);
        }
    }
}

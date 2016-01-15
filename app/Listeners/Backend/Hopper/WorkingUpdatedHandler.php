<?php

namespace App\Listeners\Backend\Hopper;

use App\Events\Backend\Hopper\WorkingUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WorkingUpdatedHandler
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
     * @param  WorkingUpdated  $event
     * @return void
     */
    public function handle(WorkingUpdated $event)
    {
        //
    }
}

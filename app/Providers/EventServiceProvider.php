<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        /**
         * Frontend Events
         */

        /**
         * Authentication Events
         */
        \App\Events\Frontend\Auth\UserLoggedIn::class  => [
            \App\Listeners\Frontend\Auth\UserLoggedInListener::class,
        ],
        \App\Events\Frontend\Auth\UserLoggedOut::class => [
            \App\Listeners\Frontend\Auth\UserLoggedOutListener::class,
        ],
        \App\Events\Frontend\Auth\UserRegistered::class => [
            \App\Listeners\Frontend\Auth\UserRegisteredListener::class,
        ],
        
        /**
        * Backend Events
        */
       \App\Events\Backend\Hopper\EventSessionUpdated::class => [
            \App\Listeners\Backend\Hopper\EventSessionUpdatedHandler::class,
       ],
       \App\Events\Backend\Hopper\FileUploaded::class => [
            \App\Listeners\Backend\Hopper\FileUploadedHandler::class,
       ],
       \App\Events\Backend\Hopper\MasterUpdated::class => [
            \App\Listeners\Backend\Hopper\MasterUpdatedHandler::class,
       ],
       \App\Events\Backend\Hopper\WorkingUpdated::class => [
               \App\Listeners\Backend\Hopper\WorkingUpdatedHandler::class,
       ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}

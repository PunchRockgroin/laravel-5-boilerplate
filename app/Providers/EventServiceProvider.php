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
      * Backend Events
      */
     \App\Events\Backend\Hopper\EventSessionUpdated::class => [
          \App\Listeners\Backend\Hopper\EventSessionUpdatedHandler::class,
     ],
     \App\Events\Backend\Hopper\FileEntityUpdated::class => [
          \App\Listeners\Backend\Hopper\FileEntityUpdatedHandler::class,
     ],
     \App\Events\Backend\Hopper\VisitUpdated::class => [
          \App\Listeners\Backend\Hopper\VisitUpdatedHandler::class,
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
     \App\Events\Backend\Hopper\Heartbeat::class => [
             \App\Listeners\Backend\Hopper\HeartbeatHandler::class,
     ],
     \App\Events\Backend\Hopper\FileOperation::class => [
             \App\Listeners\Backend\Hopper\FileOperationHandler::class,
     ],
	 \App\Events\Backend\Hopper\IssueAlert::class => [
             \App\Listeners\Backend\Hopper\IssueAlertHandler::class,
     ],
    ];

	/**
     * Class event subscribers
     * @var array
     */
    protected $subscribe = [
		/**
		 * Frontend Subscribers
		 */

		/**
		 * Auth Subscribers
		 */
		\App\Listeners\Frontend\Auth\UserEventListener::class,

		/**
		 * Backend Subscribers
		 */

		/**
		 * Access Subscribers
		 */
        \App\Listeners\Backend\Access\User\UserEventListener::class,
		\App\Listeners\Backend\Access\Role\RoleEventListener::class,
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

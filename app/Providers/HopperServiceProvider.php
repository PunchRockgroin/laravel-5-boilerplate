<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Hopper\Contracts\HopperContract;

use App\Services\Hopper\Hopper;
use App\Services\Hopper\HopperFile;
use App\Services\Hopper\HopperUser;

class HopperServiceProvider extends ServiceProvider
{
    
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['backend.layouts.master'], function(){
            
            $heartbeat_detector_enable = false;
            $heartbeat_detector_routes = ['admin.dashboard'];
			
			$pusher_presence_routes = ['admin.dashboard', 'admin.visit.edit'];
                        
            javascript()->put([
                'hopper' => [
                    'userid' => auth()->user()->id,
                    'username' => auth()->user()->name,
                    'email' => auth()->user()->email,
					'routes' => [
						'user_status' => route('admin.dashboard.user.status'),
						'user_update' => route('admin.dashboard.user.update'),
						'heartbeat_status' => route('backend.heartbeat.status'),
						'heartbeat_data' => route('admin.dashboard.data'),
						'heartbeat_user' => '/admin/dashboard/heartbeat',
						'visit_assignments' => route('admin.visit.assignments'),
						'visit_unassigned' => route('admin.visit.unassigned'),
						'visit_assign' => route('admin.visit.assign', ''),
						'notify_client' => route('backend.hopper.admin.notify-client'),
					],
					'user_status_uri' => route('admin.dashboard.user.status'),
                    'heartbeat_status' => route('backend.heartbeat.status'),
                    'heartbeat_data' => route('admin.dashboard.data'),
                    'heartbeat_user' => '/admin/dashboard/heartbeat',
                    'heartbeat_detector_enable' => (in_array(request()->route()->getName(), $heartbeat_detector_routes) ? true : false),
                    'pusher_presence_enable' => (in_array(request()->route()->getName(), $pusher_presence_routes) ? true : false),
                ],
            ]);
        });
        //Views we track
        view()->composer([
            'backend.fileentity.edit',
            'backend.visit.edit',
        ], function(){
            javascript()->put([
                'heartbeat' => [
                    'user' => auth()->user()->email,
                    'url' => route('backend.heartbeat.index'),
                    'route' => request()->route()->getName(),
                    'parameters' => request()->segments()
                ],
            ]);
        
        });
                
        //Announce the creation of a new Event Session
        \App\Models\Hopper\EventSession::created(function ($eventsession) {
            //event(new \App\Events\Backend\Hopper\EventSessionUpdated($eventsession->id, 'created', 'I was born'));
        });
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
       $this->registerHopper();
       $this->registerFacade();
       $this->registerBindings();
    }
    
     /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerHopper()
    {
        $this->app->bind('hopper', function ($app) {
            return new Hopper($app);
        });
        $this->app->bind('hopper.file', function ($app) {
            return new HopperFile($app);
        });
        $this->app->bind('hopper.user', function ($app) {
            return new HopperUser($app);
        });
    }
    
    /**
     * Register the vault facade without the user having to add it to the app.php file.
     *
     * @return void
     */
    public function registerFacade()
    {
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Hopper', \App\Services\Hopper\Facades\Hopper::class);
        });
    }
    
    
    /**
     * Register service provider bindings
     */
    public function registerBindings()
    {
        $this->app->bind(
            \App\Services\Hopper\Contracts\HopperContract::class,
            \App\Services\Hopper\Hopper::class
        );
        
        $this->app->bind(
            \App\Services\Hopper\Contracts\HopperFileContract::class,
            \App\Services\Hopper\HopperFile::class
        );
		
        $this->app->bind(
            \App\Services\Hopper\Contracts\HopperUserContract::class,
            \App\Services\Hopper\HopperUser::class
        );

        
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return 
        [
            'App\Services\Hopper\Hopper',
            'App\Services\Hopper\HopperFile',
            'App\Services\Hopper\HopperUser',
        ];
    }
}

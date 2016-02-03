<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Hopper\Contracts\HopperContract;
use App\Services\Hopper\Hopper;
use App\Services\Hopper\HopperFile;

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
        //
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
        ];
    }
}

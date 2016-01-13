<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Hopper\Hopper;

class HopperServiceProvider extends ServiceProvider
{
    protected $defer = true;
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
       $this->app->bind('App\Services\Hopper\Contracts\HopperContract', function(){

            return new Hopper();

        });
        
       $this->app->bind('hopper', function(){

            return new Hopper();

        }); 
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['App\Services\Hopper\Contracts\HopperContract'];
    }
}

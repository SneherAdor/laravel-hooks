<?php

namespace Millat\LaravelHooks;

use Illuminate\Support\ServiceProvider;
use Millat\LaravelHooks\Hooks;

class LaravelHooksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //
    }

    /**
     * Register all directives.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('hooks', function ($app) {
            return new Hooks();
        });
    }
}

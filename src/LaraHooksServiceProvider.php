<?php

namespace Millat\LaraHooks;

use Illuminate\Support\ServiceProvider;
use Millat\LaraHooks\Hooks;

class LaraHooksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerDirectives();
    }

    /**
     * Register all directives.
     *
     * @return void
     */
    public function registerDirectives()
    {
        $this->app->singleton('hooks', function ($app) {
            return new Hooks();
        });
    }
}

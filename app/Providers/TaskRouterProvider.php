<?php

namespace App\Providers;

use App\TwilioAppSettings;
use Illuminate\Support\ServiceProvider;

class TaskRouterProvider extends ServiceProvider
{
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
        $this->app->singleton(TwilioAppSettings::class, function ($app) {
            return new TwilioAppSettings();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [TwilioAppSettings::class];
    }
}

<?php

namespace App\Providers;

use App\TwilioAppSettings;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;
use Twilio\Rest\Taskrouter;

class TwilioProvider extends ServiceProvider
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
        $this->app->singleton(Client::class, function ($app) {
            $accountSid = config('services.twilio')['accountSid']
            or die("TWILIO_ACCOUNT_SID is not set in the environment");
            $authToken = config('services.twilio')['authToken']
            or die("TWILIO_AUTH_TOKEN is not set in the environment");
            return new Client($accountSid, $authToken);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }
}

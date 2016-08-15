<?php

namespace App\Providers;

use App\TaskRouter\WorkspaceFacade;
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
        $this->app->singleton(WorkspaceFacade::class, function ($app) {
            $workspaceSid = config('services.twilio')['workspaceSid']
                or die("WORKSPACE_SID needs to be set in the environment");
            $twilioClient = $app[Client::class];
            return WorkspaceFacade::createBySid($twilioClient->taskrouter, $workspaceSid);
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

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Factory;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function () {
            $factory = (new Factory)
                ->withServiceAccount(config('services.fcm.credentials'))
                ->withProjectId(config('services.fcm.project_id'));

            return $factory->createMessaging();
        });
    } 

    public function boot(): void
    {
        //
    }
}

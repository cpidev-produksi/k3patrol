<?php

namespace App\Providers;

use App\Listeners\SendFindingNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    protected $subscribe = [
        SendFindingNotification::class,
    ];

    public function boot(): void
    {
        //
    }
}

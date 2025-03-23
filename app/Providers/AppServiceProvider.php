<?php

namespace App\Providers;

use App\Listeners\ElectionEventSubscriber;
use App\Services\SMS\Contracts\SMSFactoryContract;
use App\Services\SMS\SMSManagerService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SMSFactoryContract::class, fn($app) => new SMSManagerService($app));

        Event::subscribe(ElectionEventSubscriber::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

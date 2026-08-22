<?php

namespace App\Providers;

use App\Events\CeilingPriceUpdated;
use App\Listeners\SendCeilingPriceAlertSms;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Event::listen(CeilingPriceUpdated::class, SendCeilingPriceAlertSms::class);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

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
        RateLimiter::for('pay-order', function ($request){
            return [
                Limit::perMinute(10)->by(optional($request->user())->id ?: $request->ip())
            ];
        });

        RateLimiter::for('cancel-order', function ($request){
            return [
                Limit::perMinute(5)->by(optional($request->user())->id ?: $request->ip())
            ];
        });


        RateLimiter::for('payment-webhook', function ($request) {
            return [
                Limit::perMinute(30)->by($request->ip()),
            ];
        });



    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MidtransService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MidtransService::class, function ($app) {
            return new MidtransService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

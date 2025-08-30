<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymongoService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(PaymongoService::class, function ($app) {
            return new PaymongoService();
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

<?php

namespace App\Providers;

use App\Service\AuthService;
use App\Service\ConfigService;
use App\Service\ShowroomService;
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
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }

    /**
     * Register services.
     * @return void
     */
    protected function registerService(): void
    {
        $this->app->singleton(AuthService::class);
        $this->app->singleton(ConfigService::class);
        $this->app->singleton(ShowroomService::class);
    }
}

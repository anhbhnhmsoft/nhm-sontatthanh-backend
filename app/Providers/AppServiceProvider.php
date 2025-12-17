<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Service\AuthService;
use App\Service\BrandService;
use App\Service\ConfigService;
use App\Service\NewsService;
use App\Service\ProductService;
use App\Service\ShowroomService;
use App\Service\VideoLiveService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerService();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerObserve();
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
        $this->app->singleton(VideoLiveService::class);
        $this->app->singleton(ProductService::class);
        $this->app->singleton(BrandService::class);
        $this->app->singleton(NewsService::class);
    }

    protected function registerObserve(): void
    {
        User::observe(UserObserver::class);
    }
}

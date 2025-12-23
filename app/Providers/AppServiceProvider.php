<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Camera;
use App\Models\Line;
use App\Models\Product;
use App\Models\Showroom;
use App\Models\User;
use App\Observers\BannerObserve;
use App\Observers\BrandObserve;
use App\Observers\CameraObserve;
use App\Observers\LineObserve;
use App\Observers\ProductObserve;
use App\Observers\ShowroomObserve;
use App\Observers\UserObserver;
use App\Service\AuthService;
use App\Service\BrandService;
use App\Service\CameraService;
use App\Service\ConfigService;
use App\Service\NewsService;
use App\Service\ProductService;
use App\Service\ShowroomService;
use App\Service\VideoLiveService;
use Illuminate\Support\Facades\URL;
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
        $this->app->singleton(CameraService::class);
        $this->app->singleton(VideoLiveService::class);
    }

    protected function registerObserve(): void
    {
        User::observe(UserObserver::class);
        Banner::observe(BannerObserve::class);
        Line::observe(LineObserve::class);
        Product::observe(ProductObserve::class);
        Showroom::observe(ShowroomObserve::class);
        Camera::observe(CameraObserve::class);
        Brand::observe(BrandObserve::class);
    }
}

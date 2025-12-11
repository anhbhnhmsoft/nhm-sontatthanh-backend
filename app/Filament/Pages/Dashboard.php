<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BrandChart;
use App\Filament\Widgets\InventoryChart;
use App\Filament\Widgets\RecentNews;
use App\Filament\Widgets\RecentProducts;
use App\Filament\Widgets\RecentUsers;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as FilamentDashboard;

class Dashboard extends FilamentDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public  function getWidgets(): array
    {
        return [
            BrandChart::class,
            InventoryChart::class,
            RecentUsers::class,
            RecentProducts::class,
            RecentNews::class,
            StatsOverview::class,
        ];
    }
}

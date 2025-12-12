<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Tổng số Cửa hàng', \App\Models\Showroom::count())
                ->description('Độ bao phủ của hệ thống')
                ->descriptionIcon('heroicon-m-building-storefront'),
            Stat::make('Tổng số Sản phẩm', \App\Models\Product::count())
                ->description('Quy mô danh mục sản phẩm')
                ->descriptionIcon('heroicon-m-shopping-bag'),
            Stat::make('Tổng số Camera', \App\Models\Camera::count())
                ->description('Tổng tài sản thiết bị')
                ->descriptionIcon('heroicon-m-video-camera'),
            Stat::make('Tổng số Người dùng', \App\Models\User::count())
                ->description('Tổng số nhân viên/người dùng')
                ->descriptionIcon('heroicon-m-users'),
            Stat::make('Tin tức mới', \App\Models\News::where('created_at', '>=', now()->subDays(7))->count())
                ->description('Mức độ hoạt động của nội dung')
                ->descriptionIcon('heroicon-m-newspaper'),
            Stat::make('Camera đang Bị ngắt', \App\Models\Camera::where('bind_status', false)->count())
                ->description('Thiết bị cần chú ý/bảo trì')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            Stat::make('Số lượng sản phẩm tồn kho', \App\Models\Product::where('quantity', '>', 0)->count())
                ->description('Số lượng sản phẩm tồn kho')
                ->descriptionIcon('heroicon-m-shopping-bag'),
            Stat::make('Số lượng sản phẩm hết hàng', \App\Models\Product::where('quantity', 0)->count())
                ->description('Số lượng sản phẩm hết hàng')
                ->descriptionIcon('heroicon-m-shopping-bag'),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class InventoryChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Biểu đồ Tồn kho';

    protected function getData(): array
    {
        $products = \App\Models\Product::orderBy('quantity', 'asc')->take(5)->get();

        return [
            'datasets' => [
                [
                    'label' => 'Số lượng tồn kho',
                    'data' => $products->pluck('quantity')->toArray(),
                    'backgroundColor' => '#f87171',
                ],
            ],
            'labels' => $products->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

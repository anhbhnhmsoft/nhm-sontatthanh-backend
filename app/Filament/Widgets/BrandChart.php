<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BrandChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Phân bổ Sản phẩm theo Thương hiệu';

    protected function getData(): array
    {
        $data = \App\Models\Product::select('brand_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('brand_id')
            ->with('brand')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Phân bổ sản phẩm',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => ['#fca5a5', '#fdba74', '#fcd34d', '#86efac', '#93c5fd'],
                ],
            ],
            'labels' => $data->map(fn($item) => $item->brand->name ?? 'Unknown')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

<?php

namespace App\Filament\Clusters\Category;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class CategoryCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $navigationLabel = 'Danh mục';

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Danh mục';
    }
}

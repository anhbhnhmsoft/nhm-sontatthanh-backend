<?php

namespace App\Filament\Clusters\Commerce;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class CommerceCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $navigationLabel = 'Thương mại';

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Thương mại';
    }
}

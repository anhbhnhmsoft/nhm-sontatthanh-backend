<?php

namespace App\Filament\Clusters\Media;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class MediaCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Truyền thông';
    }


}

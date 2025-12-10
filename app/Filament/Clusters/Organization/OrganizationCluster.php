<?php

namespace App\Filament\Clusters\Organization;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class OrganizationCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $navigationLabel = 'Tổ chức';

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Tổ chức';
    }
}

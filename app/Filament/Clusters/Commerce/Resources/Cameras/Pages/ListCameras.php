<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Pages;

use App\Filament\Clusters\Commerce\Resources\Cameras\CameraResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCameras extends ListRecords
{
    protected static string $resource = CameraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

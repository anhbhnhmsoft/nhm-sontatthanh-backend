<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Pages;

use App\Filament\Clusters\Commerce\Resources\Cameras\CameraResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCamera extends CreateRecord
{
    protected static string $resource = CameraResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = true;
        return $data;
    }
}

<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Pages;

use App\Filament\Clusters\Commerce\Resources\Cameras\CameraResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCamera extends EditRecord
{
    protected static string $resource = CameraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Clusters\Commerce\Resources\Showrooms\Pages;

use App\Filament\Clusters\Commerce\Resources\Showrooms\ShowroomResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditShowroom extends EditRecord
{
    protected static string $resource = ShowroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

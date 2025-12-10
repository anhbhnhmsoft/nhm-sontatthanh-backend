<?php

namespace App\Filament\Clusters\Category\Resources\Lines\Pages;

use App\Filament\Clusters\Category\Resources\Lines\LineResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLine extends EditRecord
{
    protected static string $resource = LineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

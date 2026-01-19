<?php

namespace App\Filament\Clusters\Category\Resources\CategoryNews\Pages;

use App\Filament\Clusters\Category\Resources\CategoryNews\CategoryNewsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryNews extends EditRecord
{
    protected static string $resource = CategoryNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn($record) => $record->name == 'Nổi bật'),
        ];
    }
}

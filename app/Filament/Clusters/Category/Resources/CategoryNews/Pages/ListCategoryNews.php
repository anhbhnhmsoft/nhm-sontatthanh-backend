<?php

namespace App\Filament\Clusters\Category\Resources\CategoryNews\Pages;

use App\Filament\Clusters\Category\Resources\CategoryNews\CategoryNewsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryNews extends ListRecords
{
    protected static string $resource = CategoryNewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

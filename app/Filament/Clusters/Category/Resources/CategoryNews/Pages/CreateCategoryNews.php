<?php

namespace App\Filament\Clusters\Category\Resources\CategoryNews\Pages;

use App\Filament\Clusters\Category\Resources\CategoryNews\CategoryNewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryNews extends CreateRecord
{
    protected static string $resource = CategoryNewsResource::class;
}

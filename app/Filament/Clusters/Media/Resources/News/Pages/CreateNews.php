<?php

namespace App\Filament\Clusters\Media\Resources\News\Pages;

use App\Filament\Clusters\Media\Resources\News\NewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
}

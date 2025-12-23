<?php

namespace App\Filament\Clusters\Media\Resources\Notifications\Pages;

use App\Filament\Clusters\Media\Resources\Notifications\NotificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

<?php

namespace App\Filament\Clusters\Media\Resources\Notifications;

use App\Filament\Clusters\Media\MediaCluster;
use App\Filament\Clusters\Media\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Clusters\Media\Resources\Notifications\Tables\NotificationsTable;
use App\Models\Notification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = MediaCluster::class;


    public static function getModelLabel(): string
    {
        return 'Thông báo';
    }

    public static function table(Table $table): Table
    {
        return NotificationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
        ];
    }
}

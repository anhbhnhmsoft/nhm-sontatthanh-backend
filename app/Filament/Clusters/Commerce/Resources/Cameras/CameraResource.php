<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras;

use App\Filament\Clusters\Commerce\CommerceCluster;
use App\Filament\Clusters\Commerce\Resources\Cameras\Pages\CreateCamera;
use App\Filament\Clusters\Commerce\Resources\Cameras\Pages\EditCamera;
use App\Filament\Clusters\Commerce\Resources\Cameras\Pages\ListCameras;
use App\Filament\Clusters\Commerce\Resources\Cameras\Schemas\CameraForm;
use App\Filament\Clusters\Commerce\Resources\Cameras\Tables\CamerasTable;
use App\Models\Camera;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CameraResource extends Resource
{
    protected static ?string $model = Camera::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = CommerceCluster::class;

    protected static ?string $recordTitleAttribute = 'Camera';

    public static function form(Schema $schema): Schema
    {
        return CameraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CamerasTable::configure($table);
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
            'index' => ListCameras::route('/'),
            'create' => CreateCamera::route('/create'),
            'edit' => EditCamera::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

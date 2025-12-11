<?php

namespace App\Filament\Clusters\Commerce\Resources\Showrooms;

use App\Filament\Clusters\Commerce\CommerceCluster;
use App\Filament\Clusters\Commerce\Resources\Showrooms\Pages\CreateShowroom;
use App\Filament\Clusters\Commerce\Resources\Showrooms\Pages\EditShowroom;
use App\Filament\Clusters\Commerce\Resources\Showrooms\Pages\ListShowrooms;
use App\Filament\Clusters\Commerce\Resources\Showrooms\Schemas\ShowroomForm;
use App\Filament\Clusters\Commerce\Resources\Showrooms\Tables\ShowroomsTable;
use App\Models\Showroom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShowroomResource extends Resource
{
    protected static ?string $model = Showroom::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = CommerceCluster::class;

    protected static ?string $recordTitleAttribute = 'Showroom';

    public static function form(Schema $schema): Schema
    {
        return ShowroomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShowroomsTable::configure($table);
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
            'index' => ListShowrooms::route('/'),
            'create' => CreateShowroom::route('/create'),
            'edit' => EditShowroom::route('/{record}/edit'),
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

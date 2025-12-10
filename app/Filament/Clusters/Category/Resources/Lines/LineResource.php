<?php

namespace App\Filament\Clusters\Category\Resources\Lines;

use App\Filament\Clusters\Category\CategoryCluster;
use App\Filament\Clusters\Category\Resources\Lines\Pages\CreateLine;
use App\Filament\Clusters\Category\Resources\Lines\Pages\EditLine;
use App\Filament\Clusters\Category\Resources\Lines\Pages\ListLines;
use App\Filament\Clusters\Category\Resources\Lines\Schemas\LineForm;
use App\Filament\Clusters\Category\Resources\Lines\Tables\LinesTable;
use App\Models\Line;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LineResource extends Resource
{
    protected static ?string $model = Line::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = CategoryCluster::class;

    protected static ?string $recordTitleAttribute = 'Line';

    public static function getModelLabel(): string
    {
        return 'Dòng sản phẩm';
    }

    public static function form(Schema $schema): Schema
    {
        return LineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LinesTable::configure($table);
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
            'index' => ListLines::route('/'),
            'create' => CreateLine::route('/create'),
            'edit' => EditLine::route('/{record}/edit'),
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

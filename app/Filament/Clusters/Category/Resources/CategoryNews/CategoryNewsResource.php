<?php

namespace App\Filament\Clusters\Category\Resources\CategoryNews;

use App\Filament\Clusters\Category\CategoryCluster;
use App\Filament\Clusters\Category\Resources\CategoryNews\Pages\CreateCategoryNews;
use App\Filament\Clusters\Category\Resources\CategoryNews\Pages\EditCategoryNews;
use App\Filament\Clusters\Category\Resources\CategoryNews\Pages\ListCategoryNews;
use App\Filament\Clusters\Category\Resources\CategoryNews\Schemas\CategoryNewsForm;
use App\Filament\Clusters\Category\Resources\CategoryNews\Tables\CategoryNewsTable;
use App\Models\CategoryNews;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryNewsResource extends Resource
{
    protected static ?string $model = CategoryNews::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = CategoryCluster::class;

    protected static ?string $recordTitleAttribute = 'CategoryNews';

    public static function getModelLabel(): string
    {
        return 'Danh mục tin tức';
    }
    public static function form(Schema $schema): Schema
    {
        return CategoryNewsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryNewsTable::configure($table);
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
            'index' => ListCategoryNews::route('/'),
            'create' => CreateCategoryNews::route('/create'),
            'edit' => EditCategoryNews::route('/{record}/edit'),
        ];
    }
}

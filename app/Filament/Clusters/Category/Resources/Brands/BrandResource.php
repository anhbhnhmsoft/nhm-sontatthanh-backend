<?php

namespace App\Filament\Clusters\Category\Resources\Brands;

use App\Filament\Clusters\Category\CategoryCluster;
use App\Filament\Clusters\Category\Resources\Brands\Pages\CreateBrand;
use App\Filament\Clusters\Category\Resources\Brands\Pages\EditBrand;
use App\Filament\Clusters\Category\Resources\Brands\Pages\ListBrands;
use App\Filament\Clusters\Category\Resources\Brands\Schemas\BrandForm;
use App\Filament\Clusters\Category\Resources\Brands\Tables\BrandsTable;
use App\Models\Brand;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = CategoryCluster::class;

    protected static ?string $recordTitleAttribute = 'Brand';

    public static function getModelLabel(): string
    {
        return 'Thương hiệu';
    }

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
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
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
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

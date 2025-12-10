<?php

namespace App\Filament\Clusters\Commerce\Resources\Products;

use App\Filament\Clusters\Commerce\CommerceCluster;
use App\Filament\Clusters\Commerce\Resources\Products\Pages\CreateProduct;
use App\Filament\Clusters\Commerce\Resources\Products\Pages\EditProduct;
use App\Filament\Clusters\Commerce\Resources\Products\Pages\ListProducts;
use App\Filament\Clusters\Commerce\Resources\Products\Schemas\ProductForm;
use App\Filament\Clusters\Commerce\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = CommerceCluster::class;

    protected static ?string $recordTitleAttribute = 'Product';

    public static function getModelLabel(): string
    {
        return 'sản phẩm';
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
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

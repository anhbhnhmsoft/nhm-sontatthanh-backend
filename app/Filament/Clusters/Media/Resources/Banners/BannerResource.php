<?php

namespace App\Filament\Clusters\Media\Resources\Banners;

use App\Filament\Clusters\Media\MediaCluster;
use App\Filament\Clusters\Media\Resources\Banners\Pages\CreateBanner;
use App\Filament\Clusters\Media\Resources\Banners\Pages\EditBanner;
use App\Filament\Clusters\Media\Resources\Banners\Pages\ListBanners;
use App\Filament\Clusters\Media\Resources\Banners\Schemas\BannerForm;
use App\Filament\Clusters\Media\Resources\Banners\Tables\BannersTable;
use App\Models\Banner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = MediaCluster::class;

    protected static ?string $recordTitleAttribute = 'Banner';

    public static function form(Schema $schema): Schema
    {
        return BannerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BannersTable::configure($table);
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
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
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

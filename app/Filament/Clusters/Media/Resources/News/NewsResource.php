<?php

namespace App\Filament\Clusters\Media\Resources\News;

use App\Filament\Clusters\Media\MediaCluster;
use App\Filament\Clusters\Media\Resources\News\Pages\CreateNews;
use App\Filament\Clusters\Media\Resources\News\Pages\EditNews;
use App\Filament\Clusters\Media\Resources\News\Pages\ListNews;
use App\Filament\Clusters\Media\Resources\News\Schemas\NewsForm;
use App\Filament\Clusters\Media\Resources\News\Tables\NewsTable;
use App\Models\News;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = MediaCluster::class;

    protected static ?string $recordTitleAttribute = 'News';

    public static function getModelLabel(): string
    {
        return 'Tin tức';
    }

    public static function form(Schema $schema): Schema
    {
        return NewsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::configure($table);
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
            'index' => ListNews::route('/'),
            'create' => CreateNews::route('/create'),
            'edit' => EditNews::route('/{record}/edit'),
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

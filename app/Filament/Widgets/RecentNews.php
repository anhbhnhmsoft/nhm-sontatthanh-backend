<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecentNews extends TableWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Tin tức mới nhất';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => \App\Models\News::query()->orderBy('published_at', 'desc')->limit(5))
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image')->disk('public')->label('Hình ảnh'),
                \Filament\Tables\Columns\TextColumn::make('title')->label('Tiêu đề')->searchable()->limit(50),
                \Filament\Tables\Columns\TextColumn::make('published_at')->label('Ngày đăng')->dateTime(),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}

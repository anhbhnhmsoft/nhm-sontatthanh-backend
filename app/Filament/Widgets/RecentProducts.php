<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentProducts extends TableWidget
{
    protected static ?int $sort = 6;

    protected static ?string $heading = 'Sản phẩm mới nhất';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => \App\Models\Product::query()->orderBy('created_at', 'desc')->limit(5))
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->label('Tên sản phẩm')->searchable()->limit(50),
                \Filament\Tables\Columns\TextColumn::make('quantity')->label('Số lượng'),
                \Filament\Tables\Columns\TextColumn::make('price')->label('Giá')->money('VND'),
                \Filament\Tables\Columns\TextColumn::make('created_at')->label('Ngày tạo')->dateTime(),
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

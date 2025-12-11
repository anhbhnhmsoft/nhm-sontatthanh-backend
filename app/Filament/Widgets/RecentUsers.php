<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentUsers extends TableWidget
{
    protected static ?int $sort = 5;

    protected static ?string $heading = 'Người dùng mới đăng ký';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => \App\Models\User::query()->orderBy('joined_at', 'desc')->limit(5))
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('avatar')->label('Ảnh đại diện')->disk('public')->circular(),
                \Filament\Tables\Columns\TextColumn::make('name')->label('Tên')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')->label('Email'),
                \Filament\Tables\Columns\TextColumn::make('joined_at')->label('Ngày đăng ký')->dateTime(),
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

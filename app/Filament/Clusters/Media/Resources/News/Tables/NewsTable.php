<?php

namespace App\Filament\Clusters\Media\Resources\News\Tables;

use App\Enums\NewsType;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Hình ảnh')
                    ->disk('public'),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Loại tin tức')
                    ->formatStateUsing(fn($state) => NewsType::getLabel($state))
                    ->searchable(),
                TextColumn::make('source')
                    ->label('Nguồn')
                    ->searchable(),
                TextColumn::make('view_count')
                    ->label('Lượt xem')
                    ->searchable(),
                TextColumn::make('published_at')
                    ->label('Ngày đăng')
                    ->searchable()
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->searchable()
                    ->dateTime(),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->searchable()
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Xem')
                        ->icon('heroicon-o-eye'),

                    EditAction::make()
                        ->label('Sửa')
                        ->icon('heroicon-o-pencil-square'),

                    DeleteAction::make()
                        ->label('Xóa')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa')
                        ->modalDescription('Bạn có chắc chắn muốn xóa dòng sản phẩm này?')
                        ->modalSubmitActionLabel('Xác nhận xóa')
                        ->visible(fn($record) => ! $record->trashed()),

                    RestoreBulkAction::make()
                        ->label('Khôi phục')
                        ->icon('heroicon-o-arrow-path')
                        ->visible(fn($record) => $record->trashed()),
                ]),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('is_active')
                    ->options([
                        true => 'Kích hoạt',
                        false => 'Tắt',
                    ])
                    ->label('Trạng thái'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa')
                        ->modalDescription('Bạn có chắc chắn muốn xóa dòng sản phẩm này?')
                        ->modalSubmitActionLabel('Xác nhận xóa'),

                    RestoreBulkAction::make()
                        ->label('Khôi phục')
                        ->visible(fn($livewire) => $livewire->tableFilters['trashed']['value'] ?? null === 'only'),

                    ForceDeleteBulkAction::make()
                        ->label('Xóa vĩnh viễn')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Xóa vĩnh viễn')
                        ->modalDescription('Bạn có chắc chắn muốn xóa dòng sản phẩm này vĩnh viễn?')
                        ->modalSubmitActionLabel('Xác nhận xóa vĩnh viễn'),
                ]),
            ]);
    }
}

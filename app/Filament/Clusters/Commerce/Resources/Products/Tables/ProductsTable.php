<?php

namespace App\Filament\Clusters\Commerce\Resources\Products\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable(),
                ImageColumn::make('images')
                    ->label('Hình ảnh')
                    ->disk('public'),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(100)
                    ->html()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Giá')
                    ->numeric()
                    ->money('VND'),
                TextColumn::make('sale_price')
                    ->label('Giá khuyến mãi')
                    ->numeric()
                    ->money('VND'),
                TextColumn::make('brand.name')
                    ->label('Thương hiệu')
                    ->searchable(),
                TextColumn::make('line.name')
                    ->label('Dòng sản phẩm')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Số lượng')
                    ->numeric(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Trạng thái'),
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

                    RestoreAction::make()
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

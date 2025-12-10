<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CamerasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('showroom.name')
                    ->label('Showroom')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Tên camera')
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Hình ảnh')
                    ->disk('public'),
                TextColumn::make('users')
                    ->label('Người dùng')
                    ->getStateUsing(function ($record) {
                        if (!$record->users || !is_array($record->users)) {
                            return [];
                        }

                        return collect($record->users)->pluck('name')->all();
                    }),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->searchable(),
                TextColumn::make('channel_id')
                    ->label('Kênh')
                    ->searchable(),
                TextColumn::make('device_id')
                    ->label('Thiết bị')
                    ->searchable(),
                TextColumn::make('device_model')
                    ->label('Model')
                    ->searchable(),
                IconColumn::make('bind_status')
                    ->label('Trạng thái kết nối')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                IconColumn::make('enable')
                    ->label('Trạng thái hoạt động')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                ToggleColumn::make('is_active')
                    ->label('Trạng thái khóa'),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
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
                SelectFilter::make('showroom_id')
                    ->label('Showroom')
                    ->searchable()
                    ->options(function () {
                        return \App\Models\Showroom::all()->pluck('name', 'id');
                    }),
                SelectFilter::make('enable')
                    ->label('Trạng thái hoạt động')
                    ->options([
                        true => 'Kích hoạt',
                        false => 'Tắt',
                    ]),
                SelectFilter::make('is_active')
                    ->label('Trạng thái khóa')
                    ->options([
                        true => 'Kích hoạt',
                        false => 'Tắt',
                    ]),
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

<?php

namespace App\Filament\Clusters\Organization\Resources\Users\Tables;

use App\Enums\UserRole;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Vai trò')
                    ->formatStateUsing(fn($state) => UserRole::getLabel($state))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Phòng ban')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Trạng thái')

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

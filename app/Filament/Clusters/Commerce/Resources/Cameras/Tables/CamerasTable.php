<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Tables;

use App\Models\Camera;
use App\Service\VideoLiveService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\RecordActionsPosition;
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
                    ->label('Hình ảnh cover')
                    ->disk('public'),
                TextColumn::make('users')
                    ->label('Người dùng')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (!$record->users) {
                            return [];
                        }
                        return  array_slice(collect($record->users)->pluck('name')->all(), 0, 3);
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

                IconColumn::make('bind_status')
                    ->label('Trạng thái kết nối')
                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'danger'),
                IconColumn::make('enable')
                    ->label('Trạng thái hoạt động')
                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'danger'),
                ToggleColumn::make('is_active')
                    ->label('Cho phép truy cập'),
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

                    Action::make('check_connection')
                        ->label('Kiểm tra kết nối')
                        ->icon('heroicon-o-signal')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Kiểm tra kết nối camera')
                        ->modalDescription('Hệ thống sẽ bind thiết bị vào tài khoản developer và kiểm tra trạng thái kết nối. Quá trình này có thể mất vài giây.')
                        ->modalSubmitActionLabel('Xác nhận')
                        ->action(function (Camera $record) {
                            /** @var VideoLiveService $videoLiveService */
                            $videoLiveService = app(VideoLiveService::class);
                            if (!$record->bind_status) {
                                $bindRes = $videoLiveService->bindDevice($record->device_id, $record->security_code);
                                if ($bindRes->isError()) {
                                    Notification::make()
                                        ->title('Lỗi')
                                        ->body('Không thể kiểm tra kết nối: ' . $bindRes->getMessage())
                                        ->danger()
                                        ->send();
                                    return;
                                }
                            }

                            $channelRes = $videoLiveService->getDeviceChannelInfo($record->device_id);
                            if ($channelRes->isError()) {
                                Notification::make()
                                    ->title('Lỗi')
                                    ->body('Không thể kiểm tra kết nối: ' . $channelRes->getMessage())
                                    ->danger()
                                    ->send();
                                return;
                            }


                            $res = $videoLiveService->startLive($record->device_id);

                            if ($res->isError()) {
                                Notification::make()
                                    ->title('Cảnh báo')
                                    ->body('Không thể bắt đầu stream: ' . $res->getMessage())
                                    ->warning()
                                    ->send();
                                return;
                            }
                            Notification::make()
                                ->title('Kiểm tra hoàn tất')
                                ->body('Hệ thống đã kiểm tra kết nối camera. Vui lòng refresh lại trang.')
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => ! $record->trashed()),
                    Action::make('view-stream')
                        ->label('Xem camera trực tiếp')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->mountUsing(function (Action $action, $record) {
                            $videoLiveService = app(VideoLiveService::class);
                            $res = $videoLiveService->viewLive($record->device_id);

                            if ($res->isError()) {
                                Notification::make()
                                    ->title('Không tìm thấy luồng phát')
                                    ->body('Không có kênh nào đang hoạt động hoặc có tín hiệu.')
                                    ->warning()
                                    ->send();
                            }
                            $action->arguments([
                                'stream' => $res->getData(),
                            ]);
                        })
                        ->modalContent(fn(array $arguments) => view('filament.pages.video-player', [
                            'stream' => $arguments['stream'] ?? [],
                        ]))
                        ->modalWidth('7xl') // Increase width for multiple streams
                        ->modalHeading(fn($record) => "Live Stream: {$record->name}")
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Đóng'),
                    Action::make('unbind')
                        ->label('Gỡ kết nối')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->action(function ($record) {
                            $videoLiveService = app(VideoLiveService::class);
                            $res = $videoLiveService->unbindDevice($record->device_id);
                            if ($res->isError()) {
                                Notification::make()
                                    ->title('Lỗi kết nối')
                                    ->body($res->getMessage())
                                    ->danger()
                                    ->send();
                                return;
                            }
                            Notification::make()
                                ->title('Gỡ kết nối')
                                ->body('Hệ thống đã gỡ kết nối camera. Vui lòng refresh lại trang.')
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => ! $record->trashed()),


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
            ], position: RecordActionsPosition::BeforeColumns)
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

<?php

namespace App\Filament\Clusters\Media\Resources\Notifications\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

class NotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Người nhận')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('description')
                    ->label('Nội dung')
                    ->limit(50),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->formatStateUsing(fn($state) => \App\Enums\UserNotificationType::label((int)$state))
                    ->badge(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('push_notification')
                    ->label('Gửi thông báo')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('title')
                            ->label('Tiêu đề')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('description')
                            ->label('Nội dung')
                            ->required(),
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Loại thông báo')
                            ->options(\App\Enums\UserNotificationType::getOptions())
                            ->required(),
                        \Filament\Forms\Components\Toggle::make('send_to_all')
                            ->label('Gửi cho tất cả người dùng')
                            ->reactive() // Giúp giao diện phản ứng ngay khi bấm
                            ->default(false),
                        \Filament\Forms\Components\Select::make('user_ids')
                            ->label('Người nhận cụ thể')
                            ->multiple()
                            ->options(\App\Models\User::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->hidden(fn($get) => $get('send_to_all'))
                            ->required(fn($get) => !$get('send_to_all')),
                    ])
                    ->action(function (array $data) {
                        $payload = new \App\Http\DTO\NotificationPayload(
                            title: $data['title'],
                            description: $data['description'],
                            type: \App\Enums\UserNotificationType::tryFrom($data['type']) ?? \App\Enums\UserNotificationType::WELCOME,
                            data: []
                        );

                        $userIds = $data['send_to_all']
                            ? \App\Models\User::pluck('id')->toArray()
                            : $data['user_ids'];

                        \App\Jobs\SendNotificationJob::dispatch(
                            $userIds,
                            $payload
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Đã đẩy lệnh gửi thông báo thành công')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}

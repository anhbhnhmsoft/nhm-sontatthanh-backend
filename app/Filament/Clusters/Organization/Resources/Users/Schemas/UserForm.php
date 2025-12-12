<?php

namespace App\Filament\Clusters\Organization\Resources\Users\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Tên')
                                    ->validationMessages([
                                        'required' => 'Vui lòng nhập tên',
                                        'max_length' => 'Tên không được vượt quá 255 ký tự',
                                    ]),
                                FileUpload::make('avatar')
                                    ->image()
                                    ->disk('public')
                                    ->directory('users')
                                    ->label('Ảnh đại diện'),
                                TextInput::make('email')
                                    ->required()
                                    ->maxLength(255)
                                    ->email()
                                    ->unique()
                                    ->label('Email')
                                    ->validationMessages([
                                        'required' => 'Vui lòng nhập email',
                                        'max_length' => 'Email không được vượt quá 255 ký tự',
                                        'unique' => 'Email đã tồn tại',
                                    ]),
                                TextInput::make('phone')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique()
                                    ->label('Số điện thoại')
                                    ->validationMessages([
                                        'required' => 'Vui lòng nhập số điện thoại',
                                        'max_length' => 'Số điện thoại không được vượt quá 255 ký tự',
                                        'unique' => 'Số điện thoại đã tồn tại',
                                    ]),

                            ]),
                        Section::make()
                            ->schema([
                                TextInput::make('address')
                                    ->maxLength(255)
                                    ->label('Địa chỉ')
                                    ->validationMessages([
                                        'max_length' => 'Địa chỉ không được vượt quá 255 ký tự',
                                    ]),
                                Select::make('department_id')
                                    ->label('Phòng ban')
                                    ->relationship('department', 'name')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Vui lòng chọn phòng ban',
                                    ]),

                                Select::make('role')
                                    ->label('Vai trò')
                                    ->options(UserRole::toOptions())
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Vui lòng chọn vai trò',
                                    ]),
                                Grid::make()
                                    ->schema([
                                        TextInput::make('password')
                                            ->label('Mật khẩu')
                                            ->password()
                                            ->required(fn($livewire) => $livewire instanceof CreateRecord)
                                            ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                                            ->dehydrated(fn($state) => filled($state))
                                            ->revealable()
                                            ->maxLength(255)
                                            ->validationMessages([
                                                'required' => 'Vui lòng nhập mật khẩu',
                                                'max'      => 'Mật khẩu không được vượt quá 255 ký tự',
                                            ]),
                                        Toggle::make('is_active')
                                            ->label('Trạng thái')
                                            ->default(true),
                                    ]),
                                Select::make('managedSales')
                                    ->label('Danh sách CTV đang quản lý')
                                    ->multiple()
                                    ->relationship(
                                        name: 'managedSales',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query, $livewire) => $query
                                            ->where('role', UserRole::CTV->value)

                                            ->where(function (Builder $q) use ($livewire) {
                                                $q->whereNull('sale_id')
                                                    ->orWhere('sale_id', $livewire->record->id);
                                            })

                                            ->whereNot('id', $livewire->record->id)
                                    )
                                    ->hidden(fn($livewire) => $livewire->record?->role !== UserRole::SALE->value)
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),

                            ])
                    ])->columnSpanFull(),
            ]);
    }
}

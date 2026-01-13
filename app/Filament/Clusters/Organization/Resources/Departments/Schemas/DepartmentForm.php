<?php

namespace App\Filament\Clusters\Organization\Resources\Departments\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Section::make()
                            ->schema([TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->label('Tên phòng ban')
                                ->validationMessages([
                                    'required' => 'Vui lòng nhập tên phòng ban',
                                    'max' => 'Tên phòng ban không được vượt quá 255 ký tự',
                                ]),
                                Select::make('users')
                                    ->relationship(
                                        name: 'users',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn($query) => $query->where('role', UserRole::SALE->value)
                                    )
                                    ->multiple()
                                    ->label('Nhân sự'),
                                Select::make('showroom_id')
                                    ->relationship(
                                        name: 'showroom',
                                        titleAttribute: 'name',
                                    )
                                    ->label('Showroom thuộc về'),
                            ]),

                        Repeater::make('hotlines')
                            ->label('Số điện thoại hotline')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        TextInput::make('label')
                                            ->label('Tên hotline')
                                            ->required()
                                            ->maxLength(255)
                                            ->validationMessages([
                                                'required' => 'Vui lòng nhập tên hotline',
                                                'max' => 'Tên hotline không được vượt quá 255 ký tự',
                                            ]),
                                        TextInput::make('name_user')
                                            ->label('Tên nhân sự liên hệ')
                                            ->required()
                                            ->maxLength(255)
                                            ->validationMessages([
                                                'required' => 'Vui lòng nhập tên nhân sự liên hệ',
                                                'max' => 'Tên nhân sự liên hệ không được vượt quá 255 ký tự',
                                            ]),
                                        Select::make('gender')
                                            ->label("Giới tính")
                                            ->options([
                                                'male' => 'Nam',
                                                'female' => 'Nữ',
                                            ])
                                            ->validationMessages([
                                                'required' => 'Vui lòng nhập giới tính',
                                            ]),
                                        TextInput::make('phone')
                                            ->label('Số điện thoại')
                                            ->required()
                                            ->maxLength(255)
                                            ->tel()
                                            ->validationMessages([
                                                'max' => 'Số điện thoại không được vượt quá 255 ký tự',
                                                'regex' => 'Số điện thoại không hợp lệ',
                                            ]),
                                    ])
                            ])
                            ->minItems(1)
                            ->required()
                            ->validationMessages([
                                'required' => 'Vui lòng nhập ít nhất 1 hotline',
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

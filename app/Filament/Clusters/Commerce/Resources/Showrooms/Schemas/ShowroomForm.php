<?php

namespace App\Filament\Clusters\Commerce\Resources\Showrooms\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShowroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên showroom')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Vui lòng nhập tên showroom',
                                        'maxLength' => 'Tên showroom không được vượt quá 255 ký tự',
                                    ]),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->required()
                                    ->maxLength(255)
                                    ->email()
                                    ->validationMessages([
                                        'required' => 'Vui lòng nhập email',
                                        'maxLength' => 'Email không được vượt quá 255 ký tự',
                                        'email' => 'Email không hợp lệ',
                                    ]),
                            ]),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('showrooms')
                            ->required()
                            ->image(),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Vui lòng nhập mô tả',
                                'maxLength' => 'Mô tả không được vượt quá 255 ký tự',
                            ]),
                        TextInput::make('weblink')
                            ->label('Link website')
                            ->required()
                            ->url()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Vui lòng nhập link website',
                                'maxLength' => 'Link website không được vượt quá 255 ký tự',
                                'url' => 'Link website không hợp lệ',
                            ]),
                    ]),
                Section::make()
                    ->schema([
                        Grid::make()
                            ->columns(3)
                            ->schema([
                                Select::make('province')
                                    ->label('Tỉnh thành')
                                    ->relationship('province', 'name')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn($set) => $set('district', null)),
                                Select::make('district')
                                    ->label('Quận huyện')
                                    ->relationship('district', 'name')
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn($set) => $set('ward', null)),
                                Select::make('ward')
                                    ->label('Phường xã')
                                    ->relationship('ward', 'name')
                                    ->required()
                                    ->searchable()
                                    ->live(),
                            ]),
                        TextInput::make('address')
                            ->label('Địa chỉ')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('latitude')
                            ->label('Vĩ độ (Lat)')
                            ->numeric()
                            ->visible(false)
                            ->required(),
                        TextInput::make('longitude')
                            ->label('Kinh độ (Lng)')
                            ->numeric()
                            ->visible(false)
                            ->required(),

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
                                                'maxLength' => 'Tên hotline không được vượt quá 255 ký tự',
                                            ]),
                                        TextInput::make('phone')
                                            ->label('Số điện thoại')
                                            ->required()
                                            ->maxLength(255)
                                            ->tel()
                                            ->validationMessages([
                                                'required' => 'Vui lòng nhập số điện thoại',
                                                'maxLength' => 'Số điện thoại không được vượt quá 255 ký tự',
                                                'regex' => 'Số điện thoại không hợp lệ',
                                            ]),
                                    ])
                            ])
                            ->minItems(1)
                            ->required(),

                    ]),
            ]);
    }
}

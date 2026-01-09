<?php

namespace App\Filament\Clusters\Commerce\Resources\Showrooms\Schemas;

use App\Enums\DirectFile;
use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
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
                                        'max' => 'Tên showroom không được vượt quá 255 ký tự',
                                    ]),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->maxLength(255)
                                    ->email()
                                    ->validationMessages([
                                        'max' => 'Email không được vượt quá 255 ký tự',
                                        'email' => 'Email không hợp lệ',
                                    ]),
                            ]),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->directory(DirectFile::SHOWROOMS->value)
                            ->required()
                            ->image(),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => 'Mô tả không được vượt quá 255 ký tự',
                            ]),
                        TextInput::make('weblink')
                            ->label('Link website')
                            ->url()
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => 'Link website không được vượt quá 255 ký tự',
                                'url' => 'Link website không hợp lệ',
                            ]),
                    ]),
                Section::make()
                    ->schema([
                        Grid::make()
                            ->columns(3)
                            ->schema([
                                Select::make('province_code')
                                    ->label('Tỉnh thành')
                                    ->options(fn() => Province::all()->pluck('name', 'code'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn($set) => $set('district_code', null)),
                                Select::make('district_code')
                                    ->label('Quận huyện')
                                    ->options(fn($get) => District::where('province_code', $get('province_code'))->pluck('name', 'code'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn($set) => $set('ward_code', null)),
                                Select::make('ward_code')
                                    ->label('Phường xã')
                                    ->options(fn($get) => Ward::where('district_code', $get('district_code'))->pluck('name', 'code'))
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
                        Select::make('department_code')
                            ->label('Phòng ban')
                            ->relationship(
                                name: 'departments',
                                titleAttribute: 'name',
                            )
                            ->preload()
                            ->multiple(),
                    ]),
            ]);
    }
}

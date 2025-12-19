<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Schemas;

use App\Enums\DirectFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CameraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên camera')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Tên camera không được để trống',
                                        'maxLength' => 'Tên camera không được vượt quá 255 ký tự',
                                    ]),
                                TextInput::make('description')
                                    ->label('Mô tả')
                                    ->maxLength(255),
                                Select::make('showroom_id')
                                    ->label('Showroom')
                                    ->relationship('showroom', 'name')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Showroom không được để trống',
                                    ]),
                                TextInput::make('security_code')
                                    ->label('Mã bảo mật')
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'maxLength' => 'Mã bảo mật không được vượt quá 255 ký tự',
                                    ]),
                                TextInput::make('device_id')
                                    ->label('Số serial thiết bị')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Thiết bị không được để trống',
                                        'maxLength' => 'Thiết bị không được vượt quá 255 ký tự',
                                    ]),
                                TextInput::make('channel_id')
                                    ->label('Số kênh ~ số mắt của thiết bị'),
                                TextInput::make('device_model')
                                    ->label('Model')
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'maxLength' => 'Model không được vượt quá 255 ký tự',
                                    ]),
                                FileUpload::make('image')
                                    ->disk('public')
                                    ->directory(DirectFile::CAMERAS->value)
                                    ->image()
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Hinh anh khong duoc de trong'
                                    ])
                                    ->columnSpan('full')
                                    ->label('Hình ảnh')
                            ])
                    ])
                    ->columnSpan('full'),
            ]);
    }
}

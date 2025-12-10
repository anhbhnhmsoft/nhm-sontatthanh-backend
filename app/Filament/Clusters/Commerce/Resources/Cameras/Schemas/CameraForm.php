<?php

namespace App\Filament\Clusters\Commerce\Resources\Cameras\Schemas;

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
                                TextInput::make('device_id')
                                    ->label('Thiết bị')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Thiết bị không được để trống',
                                        'maxLength' => 'Thiết bị không được vượt quá 255 ký tự',
                                    ]),
                                Select::make('channel_id')
                                    ->label('Kênh')
                                    ->required()
                                    ->options([
                                        0 => 'Kênh 0',
                                        1 => 'Kênh 1',
                                    ])
                                    ->validationMessages([
                                        'required' => 'Kênh không được để trống',
                                    ]),
                                TextInput::make('device_model')
                                    ->label('Model')
                                    ->required()
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Model không được để trống',
                                        'maxLength' => 'Model không được vượt quá 255 ký tự',
                                    ]),
                            ])
                    ])
                    ->columnSpan('full'),
            ]);
    }
}

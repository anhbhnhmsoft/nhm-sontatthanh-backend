<?php

namespace App\Filament\Clusters\Media\Resources\Banners\Schemas;

use App\Enums\DirectFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên banner')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Vui lòng nhập tên banner',
                                'max' => 'Tên banner không được vượt quá 255 ký tự',
                            ]),
                        TextInput::make('source')
                            ->label('Nguồn')
                            ->maxLength(255)
                            ->validationMessages([
                                'max' => 'Đường dẫn không được vượt quá 255 ký tự'
                            ]),
                        FileUpload::make('image')
                            ->label('Hình ảnh')
                            ->required()
                            ->disk('public')
                            ->image()
                            ->directory(DirectFile::BANNERS->value)
                            ->maxSize(10240)
                            ->validationMessages([
                                'required' => 'Vui lòng chọn hình ảnh',
                            ]),
                        TextInput::make('position')
                            ->label('Vị trí')
                            ->required()
                            ->default(1)
                            ->numeric(),
                    ])
                    ->columnSpan('full')
            ]);
    }
}

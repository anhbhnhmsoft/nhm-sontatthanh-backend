<?php

namespace App\Filament\Clusters\Category\Resources\Brands\Schemas;

use App\Enums\DirectFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên thương hiệu')
                            ->required()
                            ->maxLength(255)
                            ->unique()
                            ->validationMessages([
                                'required' => 'Vui lòng nhập tên thương hiệu',
                                'max_length' => 'Tên thương hiệu không được vượt quá 255 ký tự',
                                'unique' => 'Tên thương hiệu đã tồn tại',
                            ]),
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory(DirectFile::BRANDS->value)
                            ->required()
                            ->validationMessages([
                                'required' => 'Vui lòng chọn logo',
                            ]),
                        Toggle::make('is_active')
                            ->label('Trạng thái')
                            ->default(true)
                            ->required()
                            ->disabled(fn($livewire) => $livewire instanceof CreateRecord)
                            ->validationMessages([
                                'required' => 'Vui lòng chọn trạng thái',
                            ]),
                        TextInput::make('source')
                            ->label('Nguồn')
                            ->maxLength(255)
                            ->validationMessages([
                                'max_length' => 'Nguồn không được vượt quá 255 ký tự',
                            ]),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->maxLength(255)
                            ->validationMessages([
                                'max_length' => 'Mô tả không được vượt quá 255 ký tự',
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Clusters\Category\Resources\Lines\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên dòng sản phẩm')
                            ->required()
                            ->maxLength(255)
                            ->unique()
                            ->validationMessages([
                                'required' => 'Vui lòng nhập tên dòng sản phẩm',
                                'max_length' => 'Tên dòng sản phẩm không được vượt quá 255 ký tự',
                                'unique' => 'Tên dòng sản phẩm đã tồn tại',
                            ]),
                        Textarea::make('description')
                            ->label('Mô tả')
                            ->maxLength(255)
                            ->validationMessages([
                                'max_length' => 'Mô tả không được vượt quá 255 ký tự',
                            ]),
                        Toggle::make('is_active')
                            ->label('Trạng thái')
                            ->default(true)
                            ->required()
                            ->disabled(fn($livewire) => $livewire instanceof CreateRecord)
                            ->validationMessages([
                                'required' => 'Vui lòng chọn trạng thái',
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

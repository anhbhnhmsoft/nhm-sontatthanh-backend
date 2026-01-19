<?php

namespace App\Filament\Clusters\Category\Resources\CategoryNews\Schemas;

use Filament\Schemas\Schema;

class CategoryNewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Tên danh mục')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Vui lòng nhập tên danh mục',
                                'max_length' => 'Tên danh mục không được vượt quá 255 ký tự',
                                'unique' => 'Tên danh mục đã tồn tại',
                            ]),
                        \Filament\Forms\Components\Textarea::make('description')
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

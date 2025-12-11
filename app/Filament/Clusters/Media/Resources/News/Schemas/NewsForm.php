<?php

namespace App\Filament\Clusters\Media\Resources\News\Schemas;

use App\Enums\NewsType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tiêu đề')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Tiêu đề là bắt buộc',
                                    ])
                                    ->columnSpanFull(),
                                FileUpload::make('image')
                                    ->label('Hình ảnh')
                                    ->required()
                                    ->disk('public')
                                    ->image()
                                    ->maxSize(10240)
                                    ->validationMessages([
                                        'required' => 'Hình ảnh là bắt buộc',
                                        'max_size' => 'Hình ảnh không được vượt quá 10MB',
                                    ]),
                                TextInput::make('description')
                                    ->label('Mô tả')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Mô tả là bắt buộc',
                                    ]),
                                Select::make('type')
                                    ->label('Loại tin tức')
                                    ->required()
                                    ->options(NewsType::toOptions())
                                    ->validationMessages([
                                        'required' => 'Loại tin tức là bắt buộc',
                                    ]),
                                TextInput::make("source")
                                    ->label('Nguồn')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Nguồn là bắt buộc',
                                    ]),
                                DateTimePicker::make('published_at')
                                    ->label('Ngày đăng')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Ngày đăng là bắt buộc',
                                    ]),
                                TextInput::make('view_count')
                                    ->label('Số lượt xem')
                                    ->numeric()
                                    ->default(0),

                                RichEditor::make('content')
                                    ->label('Nội dung')
                                    ->required()
                                    ->columnSpanFull()
                                    ->validationMessages([
                                        'required' => 'Nội dung là bắt buộc',
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

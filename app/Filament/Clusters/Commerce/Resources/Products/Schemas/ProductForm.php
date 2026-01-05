<?php

namespace App\Filament\Clusters\Commerce\Resources\Products\Schemas;

use App\Enums\DirectFile;
use Dom\Text;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Tên')
                                    ->maxLength(255)
                                    ->validationMessages([
                                        'required' => 'Tên sản phẩm không được để trống',
                                        'max' => 'Tên sản phẩm không được vượt quá 255 ký tự',
                                    ]),

                                RichEditor::make('description')
                                    ->label('Miêu tả')
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Mô tả sản phẩm không được để trống',
                                    ]),
                                TextInput::make('price')
                                    ->label('Giá gốc')
                                    ->required()
                                    ->numeric()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $price = $get('price');
                                        $discount = $get('discount_percent');
                                        if ($price && is_numeric($price)) {
                                            if ($discount && is_numeric($discount)) {
                                                $set('sell_price', $price + ($price * $discount / 100));
                                                $set('price_discount', $price * $discount / 100);
                                            } else {
                                                $set('sell_price', $price);
                                                $set('price_discount', 0);
                                            }
                                        }
                                    })
                                    ->validationMessages([
                                        'required' => 'Giá sản phẩm không được để trống',
                                        'numeric' => 'Giá sản phẩm phải là số',
                                    ]),
                                TextInput::make('discount_percent')
                                    ->label('Mức chiết khấu (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $price = $get('price');
                                        $discount = $get('discount_percent');
                                        if ($price && is_numeric($price)) {
                                            if ($discount && is_numeric($discount)) {
                                                $set('sell_price', $price + ($price * $discount / 100));
                                                $set('price_discount', $price * $discount / 100);
                                            } else {
                                                $set('sell_price', $price);
                                                $set('price_discount', 0);
                                            }
                                        }
                                    }),
                                TextInput::make('sale_price')
                                    ->label('Giá khuyến mãi')
                                    ->numeric()
                                    ->default(0)
                                    ->dehydrated()
                                    ->validationMessages([
                                        'numeric' => 'Giá khuyến mãi phải là số',
                                    ]),
                                TextInput::make('price_discount')
                                    ->label('Chiết khấu sale nhận được')
                                    ->numeric()
                                    ->default(0)
                                    ->dehydrated()
                                    ->validationMessages([
                                        'numeric' => 'Chiết khấu phải là số',
                                    ]),
                                TextInput::make('sell_price')
                                    ->label('Giá bán')
                                    ->numeric()
                                    ->default(0)
                                    ->dehydrated()
                                    ->validationMessages([
                                        'numeric' => 'Giá bán phải là số',
                                    ]),
                                Select::make('brand')
                                    ->label('Thương hiệu')
                                    ->required()
                                    ->relationship('brand', 'name')
                                    ->validationMessages([
                                        'required' => 'Thương hiệu sản phẩm không được để trống',
                                    ]),
                                Select::make('line')
                                    ->label('Dòng sản phẩm')
                                    ->required()
                                    ->relationship('line', 'name')
                                    ->validationMessages([
                                        'required' => 'Dòng sản phẩm không được để trống',
                                    ]),
                                TextInput::make('quantity')
                                    ->label('Số lượng')
                                    ->required()
                                    ->numeric()
                                    ->validationMessages([
                                        'required' => 'Số lượng sản phẩm không được để trống',
                                        'numeric' => 'Số lượng sản phẩm phải là số',
                                    ]),
                                Toggle::make('is_active')
                                    ->label('Trạng thái')
                                    ->default(true),
                                FileUpload::make('images')
                                    ->label('Tải lên nhiều ảnh')
                                    ->multiple()
                                    ->image()
                                    ->reorderable()
                                    ->disk('public')
                                    ->directory(DirectFile::PRODUCTS->value)
                                    ->minFiles(1)
                                    ->maxFiles(10)
                                    ->columnSpanFull()
                                    ->required()
                                    ->panelLayout('grid')
                                    ->validationMessages([
                                        'min' => 'Hình ảnh sản phẩm phải có ít nhất 1 ảnh',
                                        'max' => 'Hình ảnh sản phẩm không được vượt quá 10 ảnh',
                                        'required' => 'Hình ảnh sản phẩm không được để trống',
                                    ]),
                                Repeater::make('features')
                                    ->label('Tính năng')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Tiêu đề  ')
                                            ->required()
                                            ->columnSpan(1)
                                        ->validationMessages([
                                            'required' => 'Tiêu đề tính năng không được để trống',
                                        ]),
                                        TextInput::make('description')
                                            ->label('Mô tả chi tiết')
                                            ->required()
                                            ->columnSpan(2)
                                        ->validationMessages([
                                            'required' => 'Mô tả chi tiết tính năng không được để trống',
                                        ]),
                                    ])
                                    ->columns(3)
                                    ->reorderable()
                                    ->cloneable(),
                            ]),
                        Section::make()
                            ->schema([
                                Repeater::make('colors')
                                    ->label('Màu sắc sản phẩm')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Tên màu')
                                            ->required()
                                        ->validationMessages([
                                            'required' => 'Tên màu không được để trống',
                                        ]),
                                        ColorPicker::make('code')
                                            ->label('Mã màu (Hex/Tên)')
                                            ->placeholder('#FF0000 hoặc Red')
                                            ->required()
                                        ->validationMessages([
                                            'required' => 'Mã màu không được để trống',
                                        ]),
                                    ])
                                    ->grid(2)
                                    ->reorderable()
                                    ->cloneable()
                                    ->collapsible()
                                    ->defaultItems(1),
                                Repeater::make('specifications')
                                    ->label('Thông số kỹ thuật')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Tên thông số')
                                            ->required()
                                        ->validationMessages([
                                            'required' => 'Tên thông số không được để trống',
                                        ]),
                                        TextInput::make('value')
                                            ->label('Giá trị')
                                            ->required()
                                            ->columnSpan(2)
                                        ->validationMessages([
                                            'required' => 'Giá trị thông số không được để trống',
                                        ]),
                                    ])
                                    ->grid(2)
                                    ->reorderable()
                                    ->cloneable()
                                    ->collapsible()
                                    ->defaultItems(1),

                            ])
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

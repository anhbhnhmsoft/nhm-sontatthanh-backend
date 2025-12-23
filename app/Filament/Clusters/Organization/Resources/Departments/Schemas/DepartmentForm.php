<?php

namespace App\Filament\Clusters\Organization\Resources\Departments\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Tên phòng ban')
                            ->validationMessages([
                                'required' => 'Vui lòng nhập tên phòng ban',
                                'max_length' => 'Tên phòng ban không được vượt quá 255 ký tự',
                            ]),
                        Select::make('users')
                            ->relationship(
                                name: 'users',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn($query) => $query->where('role', UserRole::SALE->value)
                            )
                            ->multiple()
                            ->label('Người dùng'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

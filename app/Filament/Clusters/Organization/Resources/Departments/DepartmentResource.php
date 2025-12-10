<?php

namespace App\Filament\Clusters\Organization\Resources\Departments;

use App\Filament\Clusters\Organization\OrganizationCluster;
use App\Filament\Clusters\Organization\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Clusters\Organization\Resources\Departments\Pages\EditDepartment;
use App\Filament\Clusters\Organization\Resources\Departments\Pages\ListDepartments;
use App\Filament\Clusters\Organization\Resources\Departments\Schemas\DepartmentForm;
use App\Filament\Clusters\Organization\Resources\Departments\Tables\DepartmentsTable;
use App\Models\Department;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $cluster = OrganizationCluster::class;

    protected static ?string $recordTitleAttribute = 'Department';

    public static function getModelLabel(): string
    {
        return 'Bộ phận';
    }

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}

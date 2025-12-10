<?php

namespace App\Filament\Clusters\Organization\Resources\Departments\Pages;

use App\Filament\Clusters\Organization\Resources\Departments\DepartmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;
}

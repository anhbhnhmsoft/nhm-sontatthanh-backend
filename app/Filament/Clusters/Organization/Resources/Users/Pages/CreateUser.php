<?php

namespace App\Filament\Clusters\Organization\Resources\Users\Pages;

use App\Core\Helper;
use App\Enums\UserRole;
use App\Filament\Clusters\Organization\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $prefix = UserRole::from($data['role'])->prefix();
        if($data['role'] !== UserRole::ADMIN->value) {
            $data['referral_code'] = Helper::generateReferCode($prefix);
        }
        $data['joined_at'] = now();
        $data['email_verified_at'] = now();
        $data['phone_verified_at'] = now();
        return $data;
    }
}

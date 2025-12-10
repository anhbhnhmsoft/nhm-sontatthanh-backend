<?php

namespace App\Filament\Clusters\Media\Resources\Banners\Pages;

use App\Filament\Clusters\Media\Resources\Banners\BannerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBanner extends CreateRecord
{
    protected static string $resource = BannerResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_active'] = true;
        return $data;
    }
}

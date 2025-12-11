<?php

namespace App\Filament\Pages;

use App\Service\ConfigService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Setting extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;
    protected string $view = 'filament.pages.setting';
    protected static int|null $navigationSort = 9999;
    protected static ?string $title = 'Cài đặt';

    public array $configList = [];
    public array $configValues = [];
    protected ConfigService $service;

    public function boot(): void
    {
        $this->service = app(ConfigService::class);
    }


    public function mount(): void
    {
        $result = $this->service->getAllConfig();

        if ($result->isError()) {
            Notification::make()
                ->title('Lỗi')
                ->body($result->getMessage())
                ->warning()
                ->send();
            return;
        }

        $configs = $result->getData();
        $this->configList = $configs->toArray();
        $this->configValues = $configs->pluck('config_value', 'config_key')->toArray();
    }

    public function updateConfig()
    {
        $result = $this->service->updateConfigValues($this->configValues);

        if ($result->isError()) {
            Notification::make()
                ->title('Lưu cấu hình thất bại')
                ->body($result->getMessage())
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title('Đã lưu cấu hình')
            ->body('Các thiết lập hệ thống đã được cập nhật thành công.')
            ->success()
            ->send();
    }
}

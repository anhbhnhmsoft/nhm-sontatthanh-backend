<?php

namespace App\Filament\Pages;

use App\Service\ConfigService;
use App\Enums\ConfigKey;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Setting extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Squares2x2;
    protected string $view = 'filament.pages.setting';
    protected static ?string $title = 'Cài đặt hệ thống';

    // Khai báo data cho form
    public ?array $data = [];

    public function mount(ConfigService $service): void
    {
        $result = $service->getAllConfig();

        if ($result->isSuccess()) {
            // Đổ dữ liệu từ DB vào form
            $this->form->fill(
                $result->getData()->pluck('config_value', 'config_key')->toArray()
            );
        }
    }

    public function form(Schema $form): Schema
    {
        $service = app(ConfigService::class);
        $result = $service->getAllConfig();
        $fields = [];

        if ($result->isSuccess()) {
            $configs = $result->getData();
            foreach ($configs as $config) {
                $key = $config->config_key;
                $label = str_replace('_', ' ', $key);
                $label = ucwords(strtolower($label));

                if ($key === ConfigKey::APP_AVATAR->value) {
                    $fields[] = FileUpload::make($key)
                        ->label('Hình ảnh giám đốc')
                        ->disk('public')
                        ->image()
                        ->required()
                        ->directory('configs');
                    continue;
                }
                if ($key == ConfigKey::APP_SALE_CODE->value) {
                    $fields[] = TextInput::make($key)
                        ->label($label)
                        ->disabled();
                    continue;
                }
                $fields[] = TextInput::make($key)
                    ->label($label);
            }
        }

        return $form
            ->schema([
                Section::make('Cấu hình chung')
                    ->description('Quản lý các thông số vận hành hệ thống')
                    ->schema($fields)
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(ConfigService $service): void
    {
        $formData = $this->form->getState();
        $result = $service->updateConfigValues($formData);

        if ($result->isError()) {
            Notification::make()->title('Lỗi')->danger()->body($result->getMessage())->send();
            return;
        }

        Notification::make()->title('Thành công')->success()->send();
    }
}

<x-filament-panels::page>
    @vite(['resources/css/app.css'])
    <x-filament::section>

        <x-slot name="description">
            Cấu hình hệ thống
        </x-slot>

        {{-- Loại bỏ <filament::page> thừa --}}

        <form wire:submit="updateConfig" class="flex flex-col gap-4">
            {{-- Kiểm tra xem configList có dữ liệu không --}}
            @if (count($this->configList) > 0)
                @foreach ($this->configList as $index => $config)
                    <div class="flex flex-col items-start gap-2">
                        <label for="config_{{ $config['config_key'] }}"
                            class="block text-sm font-bold text-gray-900 dark:text-white">{{ $config['config_key'] }}</label>

                        <x-filament::input.wrapper class="w-full">
                            {{-- THAY ĐỔI: Binding trực tiếp vào configValues key --}}
                            <x-filament::input wire:model="configValues.{{ $config['config_key'] }}" />
                        </x-filament::input.wrapper>

                        <p class="block text-sm italic text-gray-500 dark:text-gray-400">
                            {{-- Sử dụng Description mặc định nếu không khớp Case --}}
                            @switch($config['config_key'])
                                @case('CLIENT_ID_APP')
                                    App Secret Id
                                @break

                                @case('API_KEY')
                                    App Oumo ID
                                @break

                                @default
                                    {{ $config['description'] ?? 'Không có mô tả.' }}
                            @endswitch
                        </p>
                    </div>
                    @if (!$loop->last)
                        <hr class="my-4 dark:border-gray-700">
                    @endif
                @endforeach
            @else
                <p class="text-center text-gray-500">Không tìm thấy cấu hình nào trong hệ thống.</p>
            @endif

            <x-filament::button type="submit" icon="heroicon-m-pencil" wire:loading.attr="disabled">
                Lưu
                <div wire:loading>
                    <x-filament::loading-indicator class="h-5 w-5" />
                </div>
            </x-filament::button>
        </form>

    </x-filament::section>

</x-filament-panels::page>

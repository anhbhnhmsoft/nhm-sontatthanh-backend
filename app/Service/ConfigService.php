<?php

namespace App\Service;

use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceException;
use App\Core\Service\ServiceReturn;
use App\Models\Config;

class ConfigService extends BaseService
{
    public function __construct(protected Config $config) {}

    public function getAllConfig(): ServiceReturn
    {
        try {
            $configs = $this->config->all();
            if ($configs->isEmpty()) {
                throw new ServiceException('Không tìm thấy cấu hình');
            }
            return ServiceReturn::success(data: $configs);
        } catch (ServiceException $th) {
            LogHelper::debug(message: $th->getMessage());
            return ServiceReturn::error(message: $th->getMessage());
        } catch (\Throwable $th) {
            LogHelper::debug(message: $th->getMessage());
            return ServiceReturn::error(message: $th->getMessage());
        }
    }

    public function updateConfigValues(array $values): ServiceReturn
    {
        try {
            // Chuẩn bị dữ liệu cho phương thức upsert
            $dataToUpdate = collect($values)->map(function ($value, $key) {
                return [
                    'config_key' => $key,
                    'config_value' => $value,
                    'updated_at' => now(),
                ];
            })->values()->toArray();

            foreach ($dataToUpdate as $config) {
                $this->config->query()->where('config_key', $config['config_key'])
                    ->update([
                        'config_value' => $config['config_value'],
                        'updated_at' => now(),
                    ]);
            }

            return ServiceReturn::success(message: 'Cấu hình đã được cập nhật.');
        } catch (\Throwable $th) {
            LogHelper::debug(message: $th->getMessage());
            return ServiceReturn::error(message: 'Cập nhật cấu hình thất bại: ' . $th->getMessage());
        }
    }
}

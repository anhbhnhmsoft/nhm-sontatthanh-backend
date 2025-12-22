<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceException;
use App\Core\Service\ServiceReturn;
use App\Enums\ConfigKey;
use App\Models\Config;

class ConfigService extends BaseService
{

    const TIME_CACHE = 60 * 60 * 24; // 1 ngày  
    public function __construct(protected Config $config) {}

    public function getAllConfig(): ServiceReturn
    {
        try {
            $configs = $this->config->all();
            if ($configs->isEmpty()) {
                return ServiceReturn::error('Không tìm thấy cấu hình');
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

    /**
     * Lấy cấu hình theo key
     * @param ConfigKey $key
     * @return ServiceReturn
     */
    public function getConfigByKey(ConfigKey $key): ServiceReturn
    {
        try {
            $config = Caching::getCache(CacheKey::CACHE_CONFIG_KEY, $key->value);
            if ($config) {
                return ServiceReturn::success(data: $config);
            }
            /**
             * @var Config $config
             */
            $config = $this->config->query()->where('config_key', $key->value)->first();

            if ($config) {
                Caching::setCache(CacheKey::CACHE_CONFIG_KEY, $config->toArray(), $key->value, self::TIME_CACHE);
                return ServiceReturn::success(data: $config->toArray());
            }
            return ServiceReturn::error('Không tìm thấy cấu hình');
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
                Caching::deleteCache(CacheKey::CACHE_CONFIG_KEY, $key);
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

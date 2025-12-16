<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\ConfigType;
use App\Models\Config;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run additional seed routines (skip in production)
        $this->seedProvince();
        // $this->seedAdmin();
        // $this->seedConfig();
    }

    private function seedProvince()
    {
        DB::beginTransaction();
        try {
            $responseProvince = Http::get('https://provinces.open-api.vn/api/v1/p/');
            if ($responseProvince->successful()) {
                $data = $responseProvince->json();  // Lấy dữ liệu dưới dạng mảng
                // Lưu dữ liệu vào bảng provinces
                foreach ($data as $provinceData) {
                    Province::query()->updateOrCreate(
                        ['code' => $provinceData['code']],
                        [
                            'name' => $provinceData['name'],
                            'division_type' => $provinceData["division_type"],
                        ]
                    );
                }
            } else {
                DB::rollBack();
                return false;
            }

            $responseDistricts = Http::get('https://provinces.open-api.vn/api/v1/d/');
            if ($responseDistricts->successful()) {
                $data = $responseDistricts->json();  // Lấy dữ liệu dưới dạng mảng
                // Lưu dữ liệu vào bảng provinces
                foreach ($data as $district) {
                    District::query()->updateOrCreate(
                        ['code' => $district['code']],
                        [
                            'name' => $district['name'],
                            'division_type' => $district["division_type"],
                            'province_code' => $district['province_code']
                        ]
                    );
                }
            } else {
                DB::rollBack();
                return false;
            }

            $responseWards = Http::get('https://provinces.open-api.vn/api/v1/w/');
            if ($responseWards->successful()) {
                $data = $responseWards->json();  // Lấy dữ liệu dưới dạng mảng
                // Lưu dữ liệu vào bảng provinces
                foreach ($data as $ward) {
                    Ward::query()->updateOrCreate(
                        ['code' => $ward['code']],
                        [
                            'name' => $ward['name'],
                            'division_type' => $ward["division_type"],
                            'district_code' => $ward['district_code']
                        ]
                    );
                }
            } else {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception);
            return false;
        }
    }

    private function seedAdmin()
    {
        DB::beginTransaction();
        try {
            // Ensure admin user exists (avoid using non-existent columns)
            User::query()->updateOrCreate(
                ['email' => 'admin@admin.vn'],
                [
                    'name' => 'Super Admin',
                    'phone' => '0123456789',
                    'password' => bcrypt('Test12345678@'),
                    'role' => UserRole::ADMIN->value,
                    'is_active' => true,
                ]
            );
            DB::commit();
            return true;
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception);
            return false;
        }
    }

    private function seedConfig()
    {
        DB::beginTransaction();
        try {
            $configs = [
                [
                    'config_key' => 'APP_ID',
                    'config_value' => '12345678',
                    'config_type' => ConfigType::KEY->value,
                    'description' => 'Client ID App',
                ],
                [
                    'config_key' => 'APP_SECRET',
                    'config_value' => '12345678',
                    'config_type' => ConfigType::KEY->value,
                    'description' => 'App Secret',
                ],
            ];
            foreach ($configs as $config) {
                Config::query()->updateOrCreate(
                    ['config_key' => $config['config_key']],
                    [
                        'config_value' => $config['config_value'],
                        'config_type' => $config['config_type'],
                        'description' => $config['description'],
                    ]
                );
            }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            dump($th);
            return false;
        }
    }
}

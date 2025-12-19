<?php

namespace App\Service;

use App\Core\Controller\FilterDTO;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Enums\UserRole;
use App\Models\Camera;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ShowroomService extends BaseService
{
    public function __construct(
        protected Showroom $showroomModel,
        protected Camera $cameraModel
    ) {}

    /**
     * @param FilterDTO $filterOptions
     * @return ServiceReturn
     */
    public function showroomPaginate(FilterDTO $filterOptions): ServiceReturn
    {
        try {
            $query = $this->showroomModel->query();
            $filters = $filterOptions->filters;

            if (!empty($filters)) {
                if (isset($filters['keyword'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('name', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('address', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('weblink', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                    });
                }

                if (isset($filters['id']) && $filters['id'] != null) {
                    $query->where('id', $filters['id']);
                }

                if (isset($filters['province_code']) && $filters['province_code'] != null) {
                    $query->where('province_code', $filters['province_code']);
                }

                if (isset($filters['district_code']) && $filters['district_code'] != null) {
                    $query->where('district_code', $filters['district_code']);
                }

                if (isset($filters['ward_code']) && $filters['ward_code'] != null) {
                    $query->where('ward_code', $filters['ward_code']);
                }

                if (isset($filters['latitude']) && $filters['latitude'] != null) {
                    $query->where('latitude', $filters['latitude']);
                }

                if (isset($filters['longitude']) && $filters['longitude'] != null) {
                    $query->where('longitude', $filters['longitude']);
                }
            }


            $paginate = $query->paginate(
                perPage: $filterOptions->perPage,
                page: $filterOptions->page
            );
            return ServiceReturn::success(
                data: $paginate
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi ShowroomService@showroomPaginate",
                ex: $exception
            );
            return ServiceReturn::success(
                data: new LengthAwarePaginator(
                    items: [],
                    total: 0,
                    perPage: $filterOptions->perPage,
                    currentPage: $filterOptions->page
                )
            );
        }
    }


    /**
     * @return ServiceReturn
     */
    public function getAllShowroom(): ServiceReturn
    {
        try {
            $showrooms = $this->showroomModel->all();
            return ServiceReturn::success(
                data: $showrooms
            );
        } catch (\Throwable $th) {
            LogHelper::error(
                message: "Lỗi ShowroomService@getAllShowroom",
                ex: $th
            );
            return ServiceReturn::success(
                data: Collection::make([])
            );
        }
    }
    /**
     * Get showroom detail by ID
     * 
     * @param int $showroomId
     * @return ServiceReturn
     */
    public function getShowroomDetail(int $showroomId): ServiceReturn
    {
        try {
            $showroom = $this->showroomModel->find($showroomId);

            if (!$showroom) {
                return ServiceReturn::error(
                    message: "Không tìm thấy showroom"
                );
            }

            return ServiceReturn::success(
                data: $showroom
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi ShowroomService@getShowroomDetail",
                ex: $exception
            );
            return ServiceReturn::error(
                message: "Có lỗi xảy ra khi lấy thông tin showroom"
            );
        }
    }

    /**
     * @param User $user
     * @return ServiceReturn
     */
    public function cameraLibrary(User $user): ServiceReturn
    {
        try {

            if($user->role != UserRole::CTV->value){
                $cameras = $user->cameras->where('is_active', true)->where('bind_status', true)->where('enable', true)->get();
                
            }else{
                $cameras = $this->cameraModel->where('is_active', true)->where('bind_status', true)->where('enable', true)->get();
            }
            return ServiceReturn::success(
                data: $cameras
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi ShowroomService@cameraLibrary",
                ex: $exception
            );
            return ServiceReturn::success(
                data: []
            );
        }
    }
}

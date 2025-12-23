<?php

namespace App\Service;

use App\Core\Controller\FilterDTO;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Line;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandService extends BaseService
{
    public function __construct(
        protected Brand $brandModel,
        protected Banner $bannerModel,
        protected Line $lineModel
    ) {}

    /**
     * @param FilterDTO $filterOptions
     * @return ServiceReturn
     */
    public function brandPaginate(FilterDTO $filterOptions): ServiceReturn
    {
        try {
            $query = $this->brandModel->query();
            $filters = $filterOptions->filters;

            if (!empty($filters)) {
                if (isset($filters['keyword'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('name', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                    });
                }

                if (isset($filters['id']) && $filters['id'] != null) {
                    $query->where('id', $filters['id']);
                }

                if (isset($filters['is_active']) && $filters['is_active'] != null) {
                    $query->where('is_active', $filters['is_active']);
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
                message: "Lỗi BrandService@brandPaginate",
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
     * @param FilterDTO $filterOptions
     * @return ServiceReturn
     */
    public function bannerPaginate(FilterDTO $filterOptions): ServiceReturn
    {
        try {
            $query = $this->bannerModel->query();
            $filters = $filterOptions->filters;

            if (!empty($filters)) {
                if (isset($filters['keyword'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('name', 'like', '%' . $filters['keyword'] . '%');
                    });
                }

                if (isset($filters['id']) && $filters['id'] != null) {
                    $query->where('id', $filters['id']);
                }

                if (isset($filters['is_active']) && $filters['is_active'] != null) {
                    $query->where('is_active', $filters['is_active']);
                }


                if (isset($filters['position']) && $filters['position'] != null) {
                    $query->where('position', $filters['position']);
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
                message: "Lỗi BrandService@bannerPaginate",
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
    public function getAllBrand(): ServiceReturn
    {
        try {
            $brands = $this->brandModel->all();
            return ServiceReturn::success(
                data: $brands
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi BrandService@getAllBrand",
                ex: $exception
            );
            return ServiceReturn::success(
                data: Collection::make([])
            );
        }
    }

    /**
     * @return ServiceReturn
     */
    public function getAllBanner(): ServiceReturn
    {
        try {
            $banners = $this->bannerModel->all();
            return ServiceReturn::success(
                data: $banners
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi BrandService@getAllBanner",
                ex: $exception
            );
            return ServiceReturn::success(
                data: Collection::make([])
            );
        }
    }

    /**
     * @return ServiceReturn
     */
    public function getAllLine(): ServiceReturn
    {
        try {
            $lines = $this->lineModel->all();
            return ServiceReturn::success(
                data: $lines
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi BrandService@getAllLine",
                ex: $exception
            );
            return ServiceReturn::success(
                data: Collection::make([])
            );
        }
    }

    /**
     * @param FilterDTO $filterOptions
     * @return ServiceReturn
     */
    public function linePaginate(FilterDTO $filterOptions): ServiceReturn
    {
        try {
            $query = $this->lineModel->query();
            $filters = $filterOptions->filters;

            if (!empty($filters)) {
                if (isset($filters['keyword'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('name', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                    });
                }

                if (isset($filters['id']) && $filters['id'] != null) {
                    $query->where('id', $filters['id']);
                }

                if (isset($filters['is_active']) && $filters['is_active'] != null) {
                    $query->where('is_active', $filters['is_active']);
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
                message: "Lỗi BrandService@linePaginate",
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
}

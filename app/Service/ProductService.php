<?php

namespace App\Service;

use App\Core\Controller\FilterDTO;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService extends BaseService
{

    public function __construct(
        protected Product $product
    ) {}

    /**
     * Get paginated list of products with filters
     * 
     * @param FilterDTO $filterOptions
     * @return ServiceReturn
     */
    public function productPaginate(FilterDTO $filterOptions): ServiceReturn
    {
        try {
            $query = $this->product->query();
            $filters = $filterOptions->filters;

            // Eager load relationships
            $query->with(['brand', 'line']);

            if (!empty($filters)) {
                // Search by keyword
                if (isset($filters['keyword'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('name', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
                    });
                }

                // Filter by ID
                if (isset($filters['id']) && $filters['id'] != null) {
                    $query->where('id', $filters['id']);
                }

                // Filter by brand
                if (isset($filters['brand_id']) && $filters['brand_id'] != null) {
                    $query->where('brand_id', $filters['brand_id']);
                }

                // Filter by line
                if (isset($filters['line_id']) && $filters['line_id'] != null) {
                    $query->where('line_id', $filters['line_id']);
                }

                // Filter by active status
                if (isset($filters['is_active']) && $filters['is_active'] != null) {
                    $query->where('is_active', $filters['is_active']);
                }

                // Filter by price range
                if (isset($filters['min_price']) && $filters['min_price'] != null) {
                    $query->where('sell_price', '>=', $filters['min_price']);
                }

                if (isset($filters['max_price']) && $filters['max_price'] != null) {
                    $query->where('sell_price', '<=', $filters['max_price']);
                }

                // Filter by availability (quantity > 0)
                if (isset($filters['in_stock']) && $filters['in_stock'] == true) {
                    $query->where('quantity', '>', 0);
                }
            }

            // Default ordering by newest first
            $query->orderBy('created_at', 'desc');

            $paginate = $query->paginate(
                perPage: $filterOptions->perPage,
                page: $filterOptions->page
            );

            return ServiceReturn::success(
                data: $paginate
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi ProductService@productPaginate",
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
     * Get product detail by ID
     * 
     * @param int $productId
     * @return ServiceReturn
     */
    public function getProductDetail(int $productId): ServiceReturn
    {
        try {
            $product = $this->product
                ->with(['brand', 'line'])
                ->find($productId);

            if (!$product) {
                return ServiceReturn::error(
                    message: "Không tìm thấy sản phẩm"
                );
            }

            return ServiceReturn::success(
                data: $product
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi ProductService@getProductDetail",
                ex: $exception
            );
            return ServiceReturn::error(
                message: "Có lỗi xảy ra khi lấy thông tin sản phẩm"
            );
        }
    }
}

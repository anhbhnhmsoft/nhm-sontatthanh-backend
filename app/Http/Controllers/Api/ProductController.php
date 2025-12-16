<?php

namespace App\Http\Controllers\Api;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\Controller\BaseController;
use App\Core\Controller\FilterDTO;
use App\Core\Controller\ListRequest;
use App\Http\Resources\ProductResource;
use App\Service\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Get paginated list of products
     * NOTE: No caching because product data is dynamic with filters
     * 
     * @param ListRequest $request
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse
    {
        /** @var FilterDTO $filterOptions */
        $filterOptions = $request->getFilterOptions();
        $result = $this->productService->productPaginate($filterOptions);
        $products = $result->getData();
        $data = ProductResource::collection($products)->response()->getData(true);

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get product detail by ID
     * Cache strategy: Cache each product detail for 1 hour
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function detail(int $id): JsonResponse
    {
        // Check cache first
        $cacheKey = "detail_{$id}";
        $productCache = Caching::getCache(CacheKey::CACHE_PRODUCT, $cacheKey);

        if ($productCache) {
            return $this->sendSuccess(
                data: $productCache,
            );
        }

        $result = $this->productService->getProductDetail($id);

        if (!$result->isSuccess()) {
            return $this->sendError(
                message: $result->getMessage()
            );
        }

        $product = $result->getData();
        $data = new ProductResource($product);

        // Cache product detail for 1 hour
        Caching::setCache(CacheKey::CACHE_PRODUCT, $data, $cacheKey, 60 * 60);

        return $this->sendSuccess(
            data: $data,
        );
    }
}

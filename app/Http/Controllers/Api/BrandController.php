<?php

namespace App\Http\Controllers\Api;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\Controller\BaseController;
use App\Http\Resources\BannerResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\LineResource;
use App\Service\BrandService;

class BrandController extends BaseController
{
    public function __construct(
        protected BrandService $brandService
    ) {}

    /**
     * Get paginated list of brands
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $brandCache = Caching::getCache(
            CacheKey::CACHE_BRAND,
        );

        if ($brandCache) {
            return $this->sendSuccess(
                data: $brandCache,
            );
        }

        $result = $this->brandService->getAllBrand();
        $brands = $result->getData();
        $data = BrandResource::collection($brands)->response()->getData(true)['data'];

        Caching::setCache(
            CacheKey::CACHE_BRAND,
            $data,
            null,
            60 * 60 * 2
        );

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get paginated list of banners
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function banner()
    {
        $bannerCache = Caching::getCache(
            CacheKey::CACHE_BANNER,
        );

        if ($bannerCache) {
            return $this->sendSuccess(
                data: $bannerCache,
            );
        }
        $result = $this->brandService->getAllBanner();
        $banners = $result->getData();
        $data = BannerResource::collection($banners)->response()->getData(true)['data'];

        Caching::setCache(
            CacheKey::CACHE_BANNER,
            $data,
            null,
            60 * 60 * 2
        );

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get paginated list of lines
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function line()
    {
        $lineCache = Caching::getCache(
            CacheKey::CACHE_LINE,
        );

        if ($lineCache) {
            return $this->sendSuccess(
                data: $lineCache,
            );
        }

        $result = $this->brandService->getAllLine();
        $lines = $result->getData();
        $data = LineResource::collection($lines)->response()->getData(true)['data'];

        Caching::setCache(
            CacheKey::CACHE_LINE,
            $data,
            null,
            60 * 60 * 2
        );

        return $this->sendSuccess(
            data: $data,
        );
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\Controller\BaseController;
use App\Http\Resources\CameraResource;
use App\Http\Resources\ShowroomResource;
use App\Service\ShowroomService;
use Illuminate\Support\Facades\Auth;

class ShowroomController extends BaseController
{
    public function __construct(
        protected ShowroomService $showroomService
    ) {}

    /**
     * Get paginated list of showrooms
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $listCache = Caching::getCache(
            CacheKey::CACHE_SHOWROOM,
        );
        if ($listCache) {
            return $this->sendSuccess(
                data: $listCache,
            );
        }

        $result = $this->showroomService->getAllShowroom();
        $showrooms = $result->getData();
        $data = ShowroomResource::collection($showrooms)->response()->getData(true)['data'];
        Caching::setCache(
            CacheKey::CACHE_SHOWROOM,
            $data,
            null,
            60 * 60 * 2
        );

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get showroom detail by ID
     * Cache strategy: Cache each showroom detail for 2 hours (static data)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(int $id)
    {
        // Check cache first
        $cacheKey = "detail_{$id}";
        $showroomCache = Caching::getCache(CacheKey::CACHE_SHOWROOM, $cacheKey);

        if ($showroomCache) {
            return $this->sendSuccess(
                data: $showroomCache,
            );
        }

        $result = $this->showroomService->getShowroomDetail($id);

        if (!$result->isSuccess()) {
            return $this->sendError(
                message: $result->getMessage()
            );
        }

        $showroom = $result->getData();
        $data = new ShowroomResource($showroom);

        Caching::setCache(CacheKey::CACHE_SHOWROOM, $data, $cacheKey, 60 * 60 * 2);

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get camera library for authenticated user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function cameraLibrary()
    {
        $user = Auth::user();
        $libraryCache = Caching::getCache(CacheKey::CACHE_SALE_CAMERA, $user->id);

        if ($libraryCache) {
            return $this->sendSuccess(
                data: $libraryCache,
            );
        }

        $result = $this->showroomService->cameraLibrary($user);
        $cameras = $result->getData();
        $data = CameraResource::collection($cameras)->response()->getData(true)['data'];

        Caching::setCache(CacheKey::CACHE_SALE_CAMERA, $data, $user->id, 60 * 60 * 2);

        return $this->sendSuccess(
            data: $data,
        );
    }
}

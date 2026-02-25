<?php

namespace App\Http\Controllers\Api;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\Controller\BaseController;
use App\Enums\ConfigKey;
use App\Http\Resources\CameraResource;
use App\Http\Resources\ShowroomResource;
use App\Service\ConfigService;
use App\Service\ShowroomService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShowroomController extends BaseController
{
    public function __construct(
        protected ShowroomService $showroomService,
        protected ConfigService  $configService,
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
            60 * 24
        );

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get showroom detail by ID
     * Cache strategy: Cache each showroom detail for 1 hours (static data)
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

        Caching::setCache(CacheKey::CACHE_SHOWROOM, $data, $cacheKey, 60 * 24);

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
        $now = Carbon::now();
        if($now->hour < 7 || $now->hour > 21){
            return $this->sendError("Hệ thống camera chỉ hoạt động từ 7h đến 21h");
        }
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

        Caching::setCache(CacheKey::CACHE_SALE_CAMERA, $data, $user->id, 5 );

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * @return JsonResponse ;
     */
    public function hotlines() : JsonResponse
    {
        $result = $this->showroomService->getHotlines();
        if( $result->isError() ){
            return $this->sendError($result->getMessage());
        }
        $data = $result->getData();
        return $this->sendSuccess(data: ShowroomResource::collection($data)->response()->getData(true)['data'] );
    }

    /**
     * Lấy cấu hình
     * @param string $slug
     * @return JsonResponse
     * */
    public function config(string $slug) : JsonResponse
    {
        $slug = strtoupper($slug);
        $slug = ConfigKey::tryFrom($slug);
        $result = $this->configService->getConfigByKey($slug);

        if( $result->isError() ){
            return $this->sendError($result->getMessage());
        }
        $data = $result->getData();
        return $this->sendSuccess(data: $data);
    }

    public function configDirector() : JsonResponse
    {
        $list = ConfigKey::getConfigDirector();
        $data = [];
        foreach($list as $slug){
            $result = $this->configService->getConfigByKey($slug);
            if( $result->isError() ){
                return $this->sendError($result->getMessage());
            }
            $data[$slug->value] = $result->getData();
        }
        $res = [];
        foreach ($data as $key => $value){
            if($key === ConfigKey::APP_AVATAR->value){
                $res[$key] = Storage::disk('public')->url($value['config_value']);
                continue;
            }
            $res[$key] = $value['config_value'];
        }
        return $this->sendSuccess(data: $res);
    }
}

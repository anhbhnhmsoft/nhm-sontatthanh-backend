<?php

namespace App\Http\Controllers\Api;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\Controller\BaseController;
use App\Core\Controller\FilterDTO;
use App\Core\Controller\ListRequest;
use App\Http\Resources\CategoryNewsResource;
use App\Http\Resources\NewsResource;
use App\Service\NewsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class NewsController extends BaseController
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    /**
     * Get paginated list of news
     * NOTE: No caching because news data is dynamic with filters
     *
     * @param ListRequest $request
     * @return JsonResponse
     */
    public function list(ListRequest $request): JsonResponse
    {
        /** @var FilterDTO $filterOptions */
        $filterOptions = $request->getFilterOptions();
        $result = $this->newsService->newsPaginate($filterOptions);
        $newsList = $result->getData();
        $data = NewsResource::collection($newsList)->response()->getData(true);

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get news detail by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function detail(int $id): JsonResponse
    {
        $result = $this->newsService->getNewsDetail($id);

        if (!$result->isSuccess()) {
            return $this->sendError(
                message: $result->getMessage()
            );
        }

        $news = $result->getData();
        $data = new NewsResource($news);

        return $this->sendSuccess(
            data: $data,
        );
    }

    /**
     * Get list of news categories
     *
     * @return JsonResponse
     */
    public function category(): JsonResponse
    {
        /** @var Collection $categories */
        $categories = Caching::getCache(
            key: CacheKey::CACHE_CATEGORY_NEWS,
        );
        if ($categories) {
            return $this->sendSuccess(
                data: CategoryNewsResource::collection($categories)->response()->getData(true),
            );
        }

        $result = $this->newsService->getNewsCategories();
        if (!$result->isSuccess()) {
            return $this->sendError(
                message: $result->getMessage()
            );
        }

        $categories = $result->getData();
        Caching::setCache(
            key: CacheKey::CACHE_CATEGORY_NEWS,
            value: $categories,
            uniqueKey: null,
            expire: 60 * 24
        );
        $data = CategoryNewsResource::collection($categories)->response()->getData(true);

        return $this->sendSuccess(
            data: $data,
        );
    }
}

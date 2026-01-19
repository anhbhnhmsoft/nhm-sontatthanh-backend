<?php

namespace App\Service;

use App\Core\Controller\FilterDTO;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Core\Service\ServiceReturn;
use App\Models\CategoryNews;
use App\Models\News;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsService extends BaseService
{

    public function __construct(
        protected News $news,
        protected CategoryNews $categoryNews,
    ) {}

    /**
     * Get paginated list of news with filters
     *
     * @param FilterDTO $filterOptions
     * @return ServiceReturn
     */
    public function newsPaginate(FilterDTO $filterOptions): ServiceReturn
    {
        try {
            $query = $this->news->query();
            $filters = $filterOptions->filters;

            // Eager load relationships
            $query->with(['category']);

            if (!empty($filters)) {
                // Search by keyword
                if (isset($filters['keyword'])) {
                    $query->where(function ($query) use ($filters) {
                        $query->where('title', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('description', 'like', '%' . $filters['keyword'] . '%')
                            ->orWhere('content', 'like', '%' . $filters['keyword'] . '%');
                    });
                }

                // Filter by ID
                if (isset($filters['id']) && $filters['id'] != null) {
                    $query->where('id', $filters['id']);
                }

                // Filter by category
                if (isset($filters['category_id']) && $filters['category_id'] != null) {
                    $query->where('category_id', $filters['category_id']);
                }

                // Filter by type
                if (isset($filters['type']) && $filters['type'] != null) {
                    $query->where('type', $filters['type']);
                }

                // Filter by active status
                if (isset($filters['is_active']) && $filters['is_active'] != null) {
                    $query->where('is_active', $filters['is_active']);
                }

                // Filter by source
                if (isset($filters['source']) && $filters['source'] != null) {
                    $query->where('source', $filters['source']);
                }

                // Filter by published date range
                if (isset($filters['published_from']) && $filters['published_from'] != null) {
                    $query->where('published_at', '>=', $filters['published_from']);
                }

                if (isset($filters['published_to']) && $filters['published_to'] != null) {
                    $query->where('published_at', '<=', $filters['published_to']);
                }
            }

            // Default ordering by published date (newest first)
            $query->orderBy('published_at', 'desc');

            $paginate = $query->paginate(
                perPage: $filterOptions->perPage,
                page: $filterOptions->page
            );

            return ServiceReturn::success(
                data: $paginate
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi NewsService@newsPaginate",
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
     * Get news detail by ID and increment view count
     *
     * @param int $newsId
     * @return ServiceReturn
     */
    public function getNewsDetail(int $newsId): ServiceReturn
    {
        try {
            $news = $this->news->with('category')->find($newsId);

            if (!$news) {
                return ServiceReturn::error(
                    message: "Không tìm thấy tin tức"
                );
            }

            // Increment view count
            $news->increment('view_count');

            return ServiceReturn::success(
                data: $news
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi NewsService@getNewsDetail",
                ex: $exception
            );
            return ServiceReturn::error(
                message: "Có lỗi xảy ra khi lấy thông tin tin tức"
            );
        }
    }

    public function getNewsCategories(): ServiceReturn
    {
        try {
            $categories = $this->categoryNews->all();
            return ServiceReturn::success(
                data: $categories
            );
        } catch (\Exception $exception) {
            LogHelper::error(
                message: "Lỗi NewsService@getNewsCategories",
                ex: $exception
            );
            return ServiceReturn::error(
                message: "Có lỗi xảy ra khi lấy danh mục tin tức"
            );
        }
    }
}

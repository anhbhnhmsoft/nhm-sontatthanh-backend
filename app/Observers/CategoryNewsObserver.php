<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\CategoryNews;

class CategoryNewsObserver
{
    public function updated(CategoryNews $categoryNews): void
    {
        $this->refreshCache($categoryNews);
    }

    /**
     * Handle the CategoryNews "deleted" event.
     */
    public function deleted(CategoryNews $categoryNews): void
    {
        $this->refreshCache($categoryNews);
    }

    /**
     * Handle the CategoryNews "restored" event.
     */
    public function restored(CategoryNews $categoryNews): void
    {
        $this->refreshCache($categoryNews);
    }

    /**
     * Handle the CategoryNews "force deleted" event.
     */
    public function forceDeleted(CategoryNews $categoryNews): void
    {
        $this->refreshCache($categoryNews);
    }

    // ---------- Private methods ----------

    private function refreshCache(CategoryNews $categoryNews): void
    {
        Caching::deleteCache(CacheKey::CACHE_CATEGORY_NEWS);
    }
}

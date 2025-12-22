<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\Brand;

class BrandObserve
{
    /**
     * Handle the Brand "created" event.
     */
    public function created(Brand $brand): void
    {
        $this->refreshCache($brand);
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        $this->refreshCache($brand);
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        $this->refreshCache($brand);
    }

    /**
     * Handle the Brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        $this->refreshCache($brand);
    }

    /**
     * Handle the Brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        $this->refreshCache($brand);
    }

    // ---------- Private methods ----------

    private function refreshCache(Brand $brand): void
    {
        Caching::deleteCache(CacheKey::CACHE_BRAND);
    }
}

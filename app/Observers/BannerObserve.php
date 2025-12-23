<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\Banner;

class BannerObserve
{
    /**
     * Handle the Banner "created" event.
     */
    public function created(Banner $banner): void
    {
        $this->refreshCache($banner);
    }

    /**
     * Handle the Banner "updated" event.
     */
    public function updated(Banner $banner): void
    {
        $this->refreshCache($banner);
    }

    /**
     * Handle the Banner "deleted" event.
     */
    public function deleted(Banner $banner): void
    {
        $this->refreshCache($banner);
    }

    /**
     * Handle the Banner "restored" event.
     */
    public function restored(Banner $banner): void
    {
        $this->refreshCache($banner);
    }

    /**
     * Handle the Banner "force deleted" event.
     */
    public function forceDeleted(Banner $banner): void
    {
        $this->refreshCache($banner);
    }

    // ---------- Private methods ----------

    private function refreshCache(Banner $banner): void
    {
        Caching::deleteCache(CacheKey::CACHE_BANNER);
    }
}

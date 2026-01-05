<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\Showroom;

class ShowroomObserve
{
    /**
     * Handle the Showroom "created" event.
     */
    public function created(Showroom $showroom): void
    {
        $this->refreshCache($showroom);
    }

    /**
     * Handle the Showroom "updated" event.
     */
    public function updated(Showroom $showroom): void
    {
        $this->refreshCache($showroom);
    }

    /**
     * Handle the Showroom "deleted" event.
     */
    public function deleted(Showroom $showroom): void
    {
        $this->refreshCache($showroom);
    }

    /**
     * Handle the Showroom "restored" event.
     */
    public function restored(Showroom $showroom): void
    {
        $this->refreshCache($showroom);
    }

    /**
     * Handle the Showroom "force deleted" event.
     */
    public function forceDeleted(Showroom $showroom): void
    {
        $this->refreshCache($showroom);
    }

    private function refreshCache(Showroom $showroom): void
    {
        Caching::deleteCache(CacheKey::CACHE_SHOWROOM,"detail_{$showroom->id}");
        Caching::deleteCache(CacheKey::CACHE_SHOWROOM);
    }
}

<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\Camera;

class CameraObserve
{
    /**
     * Handle the Camera "created" event.
     */
    public function created(Camera $camera): void
    {
        $this->refreshCache($camera);
    }

    /**
     * Handle the Camera "updated" event.
     */
    public function updated(Camera $camera): void
    {
        $this->refreshCache($camera);
    }

    /**
     * Handle the Camera "deleted" event.
     */
    public function deleted(Camera $camera): void
    {
        $this->refreshCache($camera);
    }

    /**
     * Handle the Camera "restored" event.
     */
    public function restored(Camera $camera): void
    {
        $this->refreshCache($camera);
    }

    /**
     * Handle the Camera "force deleted" event.
     */
    public function forceDeleted(Camera $camera): void
    {
        $this->refreshCache($camera);
    }

    // ---------- Private methods ----------

    private function refreshCache(Camera $camera): void
    {
        Caching::deleteCache(CacheKey::CACHE_SALE_CAMERA);
        Caching::deleteCache(CacheKey::CACHE_LIVE_STREAM_INFO, $camera->device_id);
    }
}

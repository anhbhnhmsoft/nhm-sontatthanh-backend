<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\Line;

class LineObserve
{
    /**
     * Handle the Line "created" event.
     */
    public function created(Line $line): void
    {
        $this->refreshCache($line);
    }

    /**
     * Handle the Line "updated" event.
     */
    public function updated(Line $line): void
    {
        $this->refreshCache($line);
    }

    /**
     * Handle the Line "deleted" event.
     */
    public function deleted(Line $line): void
    {
        $this->refreshCache($line);
    }

    /**
     * Handle the Line "restored" event.
     */
    public function restored(Line $line): void
    {
        $this->refreshCache($line);
    }

    /**
     * Handle the Line "force deleted" event.
     */
    public function forceDeleted(Line $line): void
    {
        $this->refreshCache($line);
    }

    private function refreshCache(Line $line): void
    {
        Caching::deleteCache(CacheKey::CACHE_LINE);
        $products = $line->products()->get();
        foreach ($products as $product) {
            Caching::deleteCache(CacheKey::CACHE_PRODUCT, "detail_{$product->id}");
        }
    }
}

<?php

namespace App\Observers;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Models\Product;

class ProductObserve
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->refreshCache($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->refreshCache($product);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->refreshCache($product);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        $this->refreshCache($product);
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        $this->refreshCache($product);
    }

    private function refreshCache(Product $product): void
    {
        Caching::deleteCache(CacheKey::CACHE_PRODUCT, "detail_{$product->id}");
    }
}

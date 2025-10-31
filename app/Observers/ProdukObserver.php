<?php

namespace App\Observers;

use App\Models\Produk;
use App\Services\CacheService;

class ProdukObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the Produk "created" event.
     */
    public function created(Produk $produk): void
    {
        $this->cache->invalidateProducts();
    }

    /**
     * Handle the Produk "updated" event.
     */
    public function updated(Produk $produk): void
    {
        $this->cache->invalidateProducts();
    }

    /**
     * Handle the Produk "deleted" event.
     */
    public function deleted(Produk $produk): void
    {
        $this->cache->invalidateProducts();
    }

    /**
     * Handle the Produk "restored" event.
     */
    public function restored(Produk $produk): void
    {
        $this->cache->invalidateProducts();
    }
}

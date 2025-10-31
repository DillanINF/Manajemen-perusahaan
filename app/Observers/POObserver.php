<?php

namespace App\Observers;

use App\Models\PO;
use App\Services\CacheService;

class POObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the PO "created" event.
     */
    public function created(PO $po): void
    {
        $this->cache->invalidatePO();
    }

    /**
     * Handle the PO "updated" event.
     */
    public function updated(PO $po): void
    {
        $this->cache->invalidatePO();
    }

    /**
     * Handle the PO "deleted" event.
     */
    public function deleted(PO $po): void
    {
        $this->cache->invalidatePO();
    }

    /**
     * Handle the PO "restored" event.
     */
    public function restored(PO $po): void
    {
        $this->cache->invalidatePO();
    }
}

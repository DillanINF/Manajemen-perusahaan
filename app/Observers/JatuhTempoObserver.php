<?php

namespace App\Observers;

use App\Models\JatuhTempo;
use App\Services\CacheService;

class JatuhTempoObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    public function created(JatuhTempo $jatuhTempo): void
    {
        $this->cache->invalidateJatuhTempo();
    }

    public function updated(JatuhTempo $jatuhTempo): void
    {
        $this->cache->invalidateJatuhTempo();
    }

    public function deleted(JatuhTempo $jatuhTempo): void
    {
        $this->cache->invalidateJatuhTempo();
    }

    public function restored(JatuhTempo $jatuhTempo): void
    {
        $this->cache->invalidateJatuhTempo();
    }
}

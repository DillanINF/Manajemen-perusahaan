<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\CacheService;

class CustomerObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    public function created(Customer $customer): void
    {
        $this->cache->invalidateCustomers();
    }

    public function updated(Customer $customer): void
    {
        $this->cache->invalidateCustomers();
    }

    public function deleted(Customer $customer): void
    {
        $this->cache->invalidateCustomers();
    }

    public function restored(Customer $customer): void
    {
        $this->cache->invalidateCustomers();
    }
}

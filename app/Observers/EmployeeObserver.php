<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\CacheService;

class EmployeeObserver
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }

    public function created(Employee $employee): void
    {
        $this->cache->invalidateEmployees();
    }

    public function updated(Employee $employee): void
    {
        $this->cache->invalidateEmployees();
    }

    public function deleted(Employee $employee): void
    {
        $this->cache->invalidateEmployees();
    }

    public function restored(Employee $employee): void
    {
        $this->cache->invalidateEmployees();
    }
}

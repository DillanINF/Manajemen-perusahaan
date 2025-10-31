<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Produk;
use App\Models\PO;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\JatuhTempo;
use App\Observers\ProdukObserver;
use App\Observers\POObserver;
use App\Observers\EmployeeObserver;
use App\Observers\CustomerObserver;
use App\Observers\JatuhTempoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CacheService as singleton
        $this->app->singleton(\App\Services\CacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Model Observers untuk auto cache invalidation
        Produk::observe(ProdukObserver::class);
        PO::observe(POObserver::class);
        Employee::observe(EmployeeObserver::class);
        Customer::observe(CustomerObserver::class);
        JatuhTempo::observe(JatuhTempoObserver::class);
    }
}

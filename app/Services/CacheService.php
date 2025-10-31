<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache duration in seconds
     */
    const CACHE_SHORT = 300;      // 5 minutes
    const CACHE_MEDIUM = 1800;    // 30 minutes
    const CACHE_LONG = 3600;      // 1 hour
    const CACHE_DAY = 86400;      // 24 hours

    /**
     * Cache tags untuk grouping
     */
    const TAG_PRODUCTS = 'products';
    const TAG_CUSTOMERS = 'customers';
    const TAG_EMPLOYEES = 'employees';
    const TAG_PO = 'po';
    const TAG_INVOICE = 'invoice';
    const TAG_JATUH_TEMPO = 'jatuh_tempo';
    const TAG_BARANG = 'barang';
    const TAG_STATISTICS = 'statistics';

    /**
     * Remember cache with callback
     */
    public function remember(string $key, $ttl, callable $callback, array $tags = [])
    {
        try {
            if (!empty($tags) && $this->supportsTagging()) {
                return Cache::tags($tags)->remember($key, $ttl, $callback);
            }
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache remember failed for key: {$key}", ['error' => $e->getMessage()]);
            return $callback();
        }
    }

    /**
     * Get cache value
     */
    public function get(string $key, $default = null, array $tags = [])
    {
        try {
            if (!empty($tags) && $this->supportsTagging()) {
                return Cache::tags($tags)->get($key, $default);
            }
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::warning("Cache get failed for key: {$key}", ['error' => $e->getMessage()]);
            return $default;
        }
    }

    /**
     * Put cache value
     */
    public function put(string $key, $value, $ttl, array $tags = [])
    {
        try {
            if (!empty($tags) && $this->supportsTagging()) {
                return Cache::tags($tags)->put($key, $value, $ttl);
            }
            return Cache::put($key, $value, $ttl);
        } catch (\Exception $e) {
            Log::warning("Cache put failed for key: {$key}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Forget specific cache key
     */
    public function forget(string $key, array $tags = [])
    {
        try {
            if (!empty($tags) && $this->supportsTagging()) {
                return Cache::tags($tags)->forget($key);
            }
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::warning("Cache forget failed for key: {$key}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Flush cache by tags
     */
    public function flushTags(array $tags)
    {
        try {
            if ($this->supportsTagging()) {
                return Cache::tags($tags)->flush();
            }
            // Fallback: clear all cache
            return $this->flushAll();
        } catch (\Exception $e) {
            Log::warning("Cache flush tags failed", ['tags' => $tags, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Flush all cache
     */
    public function flushAll()
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::warning("Cache flush all failed", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if cache driver supports tagging
     */
    protected function supportsTagging(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'array']);
    }

    /**
     * Generate cache key with prefix
     */
    public function key(string $name, ...$params): string
    {
        $key = 'app_' . $name;
        if (!empty($params)) {
            $key .= '_' . implode('_', array_map(function($p) {
                return is_array($p) ? md5(json_encode($p)) : $p;
            }, $params));
        }
        return $key;
    }

    /**
     * Cache products list
     */
    public function getProducts($ttl = self::CACHE_MEDIUM)
    {
        return $this->remember(
            $this->key('products_all'),
            $ttl,
            fn() => \App\Models\Produk::all(),
            [self::TAG_PRODUCTS]
        );
    }

    /**
     * Cache customers list
     */
    public function getCustomers($ttl = self::CACHE_MEDIUM)
    {
        return $this->remember(
            $this->key('customers_all'),
            $ttl,
            fn() => \App\Models\Customer::all(),
            [self::TAG_CUSTOMERS]
        );
    }

    /**
     * Cache employees list
     */
    public function getEmployees($ttl = self::CACHE_MEDIUM)
    {
        return $this->remember(
            $this->key('employees_all'),
            $ttl,
            fn() => \App\Models\Employee::all(),
            [self::TAG_EMPLOYEES]
        );
    }

    /**
     * Cache employee statistics
     */
    public function getEmployeeStatistics($ttl = self::CACHE_SHORT)
    {
        return $this->remember(
            $this->key('employees_stats'),
            $ttl,
            function() {
                $employees = \App\Models\Employee::all();
                return [
                    'total' => $employees->count(),
                    'total_gaji' => $employees->sum('gaji_pokok'),
                    'rata_rata' => $employees->count() > 0 ? $employees->avg('gaji_pokok') : 0,
                ];
            },
            [self::TAG_EMPLOYEES, self::TAG_STATISTICS]
        );
    }

    /**
     * Cache PO data with items
     */
    public function getPOWithItems($id, $ttl = self::CACHE_SHORT)
    {
        return $this->remember(
            $this->key('po_with_items', $id),
            $ttl,
            fn() => \App\Models\PO::with(['items.produk'])->find($id),
            [self::TAG_PO]
        );
    }

    /**
     * Cache invoice list
     */
    public function getInvoices($filters = [], $ttl = self::CACHE_SHORT)
    {
        $key = $this->key('invoices_list', $filters);
        
        return $this->remember(
            $key,
            $ttl,
            function() use ($filters) {
                $query = \App\Models\PO::with(['items.produk'])->latest();
                
                // Apply filters
                if (!empty($filters['status'])) {
                    $query->where('status_approval', $filters['status']);
                }
                if (!empty($filters['customer'])) {
                    $query->where('customer', 'like', '%' . $filters['customer'] . '%');
                }
                
                return $query->get();
            },
            [self::TAG_INVOICE, self::TAG_PO]
        );
    }

    /**
     * Cache dashboard statistics
     */
    public function getDashboardStats($ttl = self::CACHE_SHORT)
    {
        return $this->remember(
            $this->key('dashboard_stats'),
            $ttl,
            function() {
                return [
                    'total_po' => \App\Models\PO::count(),
                    'pending_po' => \App\Models\PO::where('status_approval', 'Pending')->count(),
                    'accept_po' => \App\Models\PO::where('status_approval', 'Accept')->count(),
                    'total_customers' => \App\Models\Customer::count(),
                    'total_products' => \App\Models\Produk::count(),
                ];
            },
            [self::TAG_STATISTICS, self::TAG_PO]
        );
    }

    /**
     * Cache Jatuh Tempo data
     */
    public function getJatuhTempoData($filters = [], $ttl = self::CACHE_SHORT)
    {
        $key = $this->key('jatuh_tempo_data', $filters);
        
        return $this->remember(
            $key,
            $ttl,
            function() use ($filters) {
                $query = \App\Models\JatuhTempo::latest();
                
                if (!empty($filters['status'])) {
                    $query->where('status_pembayaran', $filters['status']);
                }
                
                return $query->get();
            },
            [self::TAG_JATUH_TEMPO]
        );
    }

    /**
     * Invalidate products cache
     */
    public function invalidateProducts()
    {
        $this->flushTags([self::TAG_PRODUCTS]);
        Log::info('Products cache invalidated');
    }

    /**
     * Invalidate customers cache
     */
    public function invalidateCustomers()
    {
        $this->flushTags([self::TAG_CUSTOMERS]);
        Log::info('Customers cache invalidated');
    }

    /**
     * Invalidate employees cache
     */
    public function invalidateEmployees()
    {
        $this->flushTags([self::TAG_EMPLOYEES, self::TAG_STATISTICS]);
        Log::info('Employees cache invalidated');
    }

    /**
     * Invalidate PO cache
     */
    public function invalidatePO()
    {
        $this->flushTags([self::TAG_PO, self::TAG_INVOICE, self::TAG_STATISTICS]);
        Log::info('PO cache invalidated');
    }

    /**
     * Invalidate Jatuh Tempo cache
     */
    public function invalidateJatuhTempo()
    {
        $this->flushTags([self::TAG_JATUH_TEMPO]);
        Log::info('Jatuh Tempo cache invalidated');
    }

    /**
     * Invalidate all statistics
     */
    public function invalidateStatistics()
    {
        $this->flushTags([self::TAG_STATISTICS]);
        Log::info('Statistics cache invalidated');
    }
}

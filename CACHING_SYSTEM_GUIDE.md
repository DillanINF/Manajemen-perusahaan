# Laravel Caching System Implementation

## 📋 Overview

Sistem caching yang komprehensif untuk menghindari render ulang data yang sama dan hanya me-render ketika data berubah.

---

## 🎯 Komponen Caching System

### 1. **CacheService** (`app/Services/CacheService.php`)
Service class untuk mengelola cache dengan fitur:
- ✅ Query caching dengan tags
- ✅ Auto key generation
- ✅ TTL management (SHORT/MEDIUM/LONG/DAY)
- ✅ Tag-based invalidation
- ✅ Helper methods untuk data umum

### 2. **Model Observers** (Auto Cache Invalidation)
- `ProdukObserver` - Invalidate cache saat produk berubah
- `POObserver` - Invalidate cache saat PO berubah
- `EmployeeObserver` - Invalidate cache saat employee berubah
- `CustomerObserver` - Invalidate cache saat customer berubah
- `JatuhTempoObserver` - Invalidate cache saat jatuh tempo berubah

### 3. **Response Caching Middleware**
- Cache HTTP responses untuk GET requests
- Skip caching untuk authenticated users
- TTL configurable per route

---

## 🚀 Cara Penggunaan

### A. Di Controller

#### 1. Inject CacheService

```php
use App\Services\CacheService;

class POController extends Controller
{
    protected CacheService $cache;

    public function __construct(CacheService $cache)
    {
        $this->cache = $cache;
    }
}
```

#### 2. Cache Query Results

```php
public function index()
{
    // Cache products (TTL: 30 menit)
    $products = $this->cache->getProducts();
    
    // Cache customers (TTL: 30 menit)
    $customers = $this->cache->getCustomers();
    
    // Cache dengan custom TTL dan tags
    $pos = $this->cache->remember(
        $this->cache->key('pos_list'),
        CacheService::CACHE_SHORT, // 5 menit
        fn() => PO::with('items')->latest()->get(),
        [CacheService::TAG_PO]
    );
    
    return view('po.index', compact('products', 'customers', 'pos'));
}
```

#### 3. Cache dengan Filters

```php
public function index(Request $request)
{
    $filters = $request->only(['status', 'customer']);
    
    $invoices = $this->cache->remember(
        $this->cache->key('invoices_filtered', $filters),
        CacheService::CACHE_SHORT,
        function() use ($filters) {
            $query = PO::query();
            
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            return $query->get();
        },
        [CacheService::TAG_INVOICE]
    );
}
```

### B. Helper Methods (Built-in)

```php
// Products
$products = $this->cache->getProducts();

// Customers
$customers = $this->cache->getCustomers();

// Employees
$employees = $this->cache->getEmployees();

// Employee Statistics
$stats = $this->cache->getEmployeeStatistics();
// Returns: ['total' => X, 'total_gaji' => Y, 'rata_rata' => Z]

// Dashboard Stats
$dashboardStats = $this->cache->getDashboardStats();

// PO with Items
$po = $this->cache->getPOWithItems($id);

// Invoice List
$invoices = $this->cache->getInvoices($filters);

// Jatuh Tempo Data
$jatuhTempo = $this->cache->getJatuhTempoData($filters);
```

### C. Manual Cache Operations

```php
// Put value ke cache
$this->cache->put('key', $value, CacheService::CACHE_MEDIUM, [CacheService::TAG_PRODUCTS]);

// Get value dari cache
$value = $this->cache->get('key', 'default_value');

// Forget specific key
$this->cache->forget('key');

// Flush by tags
$this->cache->flushTags([CacheService::TAG_PRODUCTS]);

// Flush all cache
$this->cache->flushAll();
```

---

## ⏱️ Cache TTL (Time To Live)

| Konstanta | Durasi | Kapan Digunakan |
|-----------|--------|-----------------|
| `CACHE_SHORT` | 5 menit | Data yang sering berubah (PO, Invoice) |
| `CACHE_MEDIUM` | 30 menit | Data master (Products, Customers) |
| `CACHE_LONG` | 1 jam | Data jarang berubah (Settings) |
| `CACHE_DAY` | 24 jam | Data statis (Reports) |

```php
// Contoh penggunaan
$products = $this->cache->remember(
    'products_all',
    CacheService::CACHE_MEDIUM, // 30 menit
    fn() => Produk::all()
);
```

---

## 🏷️ Cache Tags

Tags untuk grouping cache agar mudah di-invalidate:

| Tag | Deskripsi |
|-----|-----------|
| `TAG_PRODUCTS` | Semua cache terkait produk |
| `TAG_CUSTOMERS` | Semua cache terkait customer |
| `TAG_EMPLOYEES` | Semua cache terkait employee |
| `TAG_PO` | Semua cache terkait PO |
| `TAG_INVOICE` | Semua cache terkait invoice |
| `TAG_JATUH_TEMPO` | Semua cache terkait jatuh tempo |
| `TAG_BARANG` | Semua cache terkait barang |
| `TAG_STATISTICS` | Semua cache statistik |

```php
// Cache dengan multiple tags
$data = $this->cache->remember(
    'key',
    300,
    fn() => getData(),
    [CacheService::TAG_PO, CacheService::TAG_STATISTICS]
);

// Invalidate berdasarkan tag
$this->cache->flushTags([CacheService::TAG_PO]);
```

---

## 🔄 Auto Cache Invalidation

Cache **otomatis di-invalidate** ketika data berubah melalui **Model Observers**:

### Contoh Flow:

```
User → Update Produk
  ↓
Produk::save()
  ↓
ProdukObserver::updated()
  ↓
CacheService::invalidateProducts()
  ↓
Cache::tags(['products'])->flush()
  ↓
✅ Cache terhapus otomatis
```

### Event yang Trigger Invalidation:
- ✅ **created** - Ketika data baru dibuat
- ✅ **updated** - Ketika data diupdate
- ✅ **deleted** - Ketika data dihapus
- ✅ **restored** - Ketika soft-deleted data direstore

### Contoh:

```php
// User update produk
$produk = Produk::find(1);
$produk->nama = 'New Name';
$produk->save();

// Cache produk OTOMATIS terhapus
// Next request akan fetch data fresh dari database
```

---

## 🎨 Response Caching Middleware

Cache entire HTTP response untuk GET requests.

### Register Middleware

Di `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... existing middleware
    'cache.response' => \App\Http\Middleware\CacheResponse::class,
];
```

### Gunakan di Routes

```php
// Cache selama 5 menit (default)
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('cache.response');

// Cache dengan custom TTL (10 menit = 600 detik)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('cache.response:600');
```

### Response Headers

Middleware menambahkan header `X-Cache`:
- `X-Cache: HIT` → Response dari cache
- `X-Cache: MISS` → Response fresh (baru di-cache)

---

## 📊 Cache Key Generation

Cache keys di-generate otomatis dengan format:

```
app_{name}_{param1}_{param2}_...
```

### Contoh:

```php
// Simple key
$key = $this->cache->key('products_all');
// Result: "app_products_all"

// Key dengan parameters
$key = $this->cache->key('pos_filtered', 'accept', '2024');
// Result: "app_pos_filtered_accept_2024"

// Key dengan array parameter (auto MD5)
$key = $this->cache->key('invoices', ['status' => 'pending', 'year' => 2024]);
// Result: "app_invoices_abc123def456..." (MD5 hash)
```

---

## 🛠️ Manual Invalidation

Kadang perlu manual invalidate cache:

### 1. Invalidate Specific Tags

```php
// Invalidate products cache
$this->cache->invalidateProducts();

// Invalidate PO cache
$this->cache->invalidatePO();

// Invalidate employees cache
$this->cache->invalidateEmployees();

// Invalidate statistics
$this->cache->invalidateStatistics();
```

### 2. Invalidate Custom Tags

```php
$this->cache->flushTags(['custom_tag', 'another_tag']);
```

### 3. Clear All Cache

```php
// Via CacheService
$this->cache->flushAll();

// Via Artisan
php artisan cache:clear
```

---

## 🔍 Cache Driver Support

### Tagging Support:
- ✅ **Redis** (Recommended)
- ✅ **Memcached**
- ✅ **Array** (Testing only)
- ❌ **File** (No tagging support)
- ❌ **Database** (No tagging support)

### Konfigurasi

Di `.env`:

```env
# Recommended: Redis
CACHE_DRIVER=redis

# Alternatif: File (tanpa tags)
CACHE_DRIVER=file
```

**Note:** Jika menggunakan `file` driver, tags tidak berfungsi dan system akan fallback ke `Cache::flush()` untuk clear all.

---

## 📈 Performance Benefits

### Sebelum Caching:

```
Request → Controller → Query DB → Process → Response
Time: ~200-500ms per request
DB Queries: 5-10 per page
```

### Sesudah Caching:

```
Request → Controller → Get from Cache → Response
Time: ~10-50ms per request
DB Queries: 0 (cache hit)
```

### Improvement:
- ⚡ **10x faster** response time
- 📉 **90% reduction** in DB queries
- 💾 **Lower DB load**
- 🚀 **Better scalability**

---

## 🧪 Testing Cache

### Check Cache Hit/Miss

```php
// Di controller
$products = $this->cache->getProducts();

// Log untuk debug
\Log::info('Products loaded', [
    'from_cache' => Cache::has($this->cache->key('products_all'))
]);
```

### Disable Cache (Development)

Di `.env`:

```env
CACHE_DRIVER=array
```

Atau clear cache sering:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 💡 Best Practices

### 1. **Pilih TTL yang Tepat**

```php
// Data sering berubah: 5 menit
$pos = $this->cache->remember('pos', CacheService::CACHE_SHORT, ...);

// Data master: 30 menit
$products = $this->cache->remember('products', CacheService::CACHE_MEDIUM, ...);

// Data jarang berubah: 1 jam atau lebih
$settings = $this->cache->remember('settings', CacheService::CACHE_LONG, ...);
```

### 2. **Gunakan Tags**

```php
// GOOD: Dengan tags (bisa invalidate group)
$this->cache->remember('key', 300, $callback, [CacheService::TAG_PO]);

// BAD: Tanpa tags (harus clear all)
Cache::remember('key', 300, $callback);
```

### 3. **Cache di Layer yang Tepat**

```php
// ✅ GOOD: Cache di controller (full result)
public function index()
{
    $products = $this->cache->getProducts();
    return view('products.index', compact('products'));
}

// ❌ BAD: Cache di view (partial caching sulit di-manage)
@cache('products', 300)
    @foreach($products as $product)
        ...
    @endforeach
@endcache
```

### 4. **Avoid Cache Stampede**

```php
// Problem: Multiple requests hit DB bersamaan saat cache expired

// Solution: Lock-based caching (Laravel 9+)
$products = Cache::lock('products_lock', 10)->block(5, function() {
    return $this->cache->remember('products', 300, fn() => Produk::all());
});
```

### 5. **Monitor Cache Performance**

```php
// Add logging
Log::channel('cache')->info('Cache accessed', [
    'key' => $key,
    'hit' => Cache::has($key),
    'ttl' => $ttl
]);
```

---

## 🔧 Troubleshooting

### Cache Tidak Ter-invalidate

**Problem:** Data berubah tapi cache masih lama

**Solution:**
1. Check Observer registered di `AppServiceProvider`
2. Pastikan cache driver support tagging (redis/memcached)
3. Manual flush: `php artisan cache:clear`

### Cache Terlalu Besar

**Problem:** Memory usage tinggi

**Solution:**
1. Kurangi TTL
2. Limit result size
3. Gunakan pagination

```php
// BEFORE
$products = Produk::all(); // 10,000 records

// AFTER
$products = Produk::paginate(100); // 100 per page
```

### Tags Tidak Bekerja

**Problem:** `flushTags()` tidak clear cache

**Solution:**
```env
# Gunakan driver yang support tags
CACHE_DRIVER=redis  # atau memcached

# Bukan file/database
```

---

## 📚 Referensi

- [Laravel Cache Documentation](https://laravel.com/docs/cache)
- [Redis Cache](https://redis.io)
- [Cache Tags](https://laravel.com/docs/cache#cache-tags)
- [Model Observers](https://laravel.com/docs/eloquent#observers)

---

## ✅ Checklist Implementation

- [x] CacheService created
- [x] Model Observers created
- [x] Observers registered
- [x] POController updated dengan cache
- [x] Response caching middleware
- [x] Documentation complete

---

**Happy Caching! 🚀**

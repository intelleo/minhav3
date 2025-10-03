# 🚀 Redis Caching Implementation - Minha-AI

## 📋 **Overview**

Implementasi Redis caching untuk optimasi performa aplikasi Minha-AI. Caching ini akan meningkatkan response time dan mengurangi beban database.

## ⚙️ **Konfigurasi Redis**

### **1. Cache Configuration**

File: `app/Config/Cache.php`

```php
public string $handler = 'redis';           // Primary handler
public string $backupHandler = 'file';      // Fallback handler
public string $prefix = 'minha_ai:';        // Cache key prefix
public int $ttl = 300;                      // Default TTL 5 menit
```

### **2. Redis Server Configuration**

File: `app/Config/Cache.php` - Redis settings

```php
public array $redis = [
    'host'     => '127.0.0.1',
    'password' => null,
    'port'     => 6379,
    'timeout'  => 0,
    'database' => 0,
];
```

## 🗄️ **Model Caching Implementation**

### **1. MadingModel Caching**

#### **Methods dengan Caching:**

- `getAllWithAdmin()` - Cache 5 menit
- `getWithAdmin($id)` - Cache 5 menit
- `getLatest($limit)` - Cache 5 menit

#### **Cache Keys:**

```php
"mading_all_with_admin_{date}"     // Semua mading
"mading_single_{id}_{date}"        // Single mading
"mading_latest_{limit}_{date}"     // Latest mading
```

#### **Cache Invalidation:**

```php
$madingModel->invalidateCache($madingId);
```

### **2. LayananModel Caching**

#### **Methods dengan Caching:**

- `getAllWithCache()` - Cache 10 menit
- `getByKategori($kategori)` - Cache 10 menit
- `getLatest($limit)` - Cache 5 menit
- `searchLayanan($keyword)` - Cache 5 menit

#### **Cache Keys:**

```php
"layanan_all_{date}"                    // Semua layanan
"layanan_kategori_{kategori}_{date}"    // By kategori
"layanan_latest_{limit}_{date}"         // Latest layanan
"layanan_search_{md5_keyword}_{date}"   // Search results
```

#### **Cache Invalidation:**

```php
$layananModel->invalidateCache();
```

## 🌐 **API Response Caching**

### **1. Layanan API Endpoints**

#### **Available Endpoints:**

```php
GET /api/layanan                    // Semua layanan (cached 5 menit)
GET /api/layanan/{kategori}         // By kategori (cached 5 menit)
GET /api/layanan/search?q={keyword} // Search (cached 5 menit)
GET /api/layanan/latest/{limit}     // Latest (cached 5 menit)
```

#### **Response Format:**

```json
{
    "status": "success",
    "total": 5,
    "data": [...],
    "cached_at": "2025-09-23 12:17:40"
}
```

## 🧪 **Testing Redis Cache**

### **Test Command:**

```bash
php spark cache:test-redis
```

### **Test Coverage:**

1. ✅ Redis connection test
2. ✅ Basic cache operations (save/get)
3. ✅ Cache remember functionality
4. ✅ Model caching integration
5. ✅ Cache invalidation

## 📊 **Performance Benefits**

### **Expected Improvements:**

- **Database Queries**: Reduced by 70-80%
- **Response Time**: 50-70% faster
- **Server Load**: Significantly reduced
- **Cache Hit Rate**: Target >90%

### **Cache TTL Strategy:**

- **Static Data**: 10-30 menit
- **Dynamic Data**: 5 menit
- **Search Results**: 5 menit
- **User-specific**: 1-2 menit

## 🔧 **Cache Management**

### **1. Manual Cache Operations**

```php
// Save to cache
cache()->save('key', $data, 300);

// Get from cache
$data = cache()->get('key');

// Remember pattern
$data = cache()->remember('key', 300, function() {
    return expensiveOperation();
});

// Delete cache
cache()->delete('key');

// Clear all cache
cache()->clean();
```

### **2. Cache Invalidation Strategy**

#### **Automatic Invalidation:**

- Cache keys include date untuk auto-expire
- Model methods call `invalidateCache()` on data changes

#### **Manual Invalidation:**

```php
// Invalidate specific mading
$madingModel->invalidateCache($madingId);

// Invalidate all layanan cache
$layananModel->invalidateCache();
```

## 🚨 **Troubleshooting**

### **Common Issues:**

#### **1. Redis Connection Failed**

```bash
# Check Redis status
redis-cli ping

# Expected response: PONG
```

#### **2. Cache Not Working**

- Verify Redis server is running
- Check cache configuration
- Run test command: `php spark cache:test-redis`

#### **3. Fallback to File Cache**

- If Redis fails, automatically falls back to file cache
- Check logs for Redis connection errors

## 📈 **Monitoring & Analytics**

### **Cache Performance Metrics:**

- Cache hit/miss ratio
- Response time improvements
- Memory usage
- Redis connection status

### **Recommended Monitoring:**

```php
// Add to your monitoring
$cacheStats = cache()->getCacheInfo();
$hitRate = $cacheStats['hits'] / ($cacheStats['hits'] + $cacheStats['misses']);
```

## 🔄 **Cache Warming Strategy**

### **Preload Critical Data:**

```php
// Warm up cache on application start
$madingModel->getAllWithAdmin();      // Preload mading
$layananModel->getAllWithCache();     // Preload layanan
$layananModel->getLatest(10);         // Preload latest
```

## 📝 **Best Practices**

### **1. Cache Key Naming:**

- Use descriptive prefixes
- Include relevant parameters
- Add date for auto-expiration

### **2. TTL Selection:**

- Static data: Longer TTL
- Dynamic data: Shorter TTL
- User-specific: Very short TTL

### **3. Cache Invalidation:**

- Invalidate on data changes
- Use tags for bulk invalidation
- Monitor cache hit rates

### **4. Error Handling:**

- Always have fallback strategy
- Log cache errors
- Graceful degradation

## 🎯 **Next Steps**

### **Phase 1: ✅ Completed**

- [x] Redis configuration
- [x] Model caching implementation
- [x] API response caching
- [x] Testing framework

### **Phase 2: Future Enhancements**

- [ ] Cache tags implementation
- [ ] Advanced cache warming
- [ ] Cache analytics dashboard
- [ ] CDN integration
- [ ] Cache compression

---

**Last Updated**: 2025-09-23  
**Version**: 1.0  
**Status**: ✅ Production Ready

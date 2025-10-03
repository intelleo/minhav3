# 🚀 MinhaVerse - Performance Optimization Strategy

## 🎯 **Overview**

Strategi optimasi untuk MinhaVerse agar mampu menampung data besar dengan performa tinggi dan response time minimal.

## 🗄️ **Database Optimization**

### **1. Indexing Strategy**

**Primary Indexes (Essential)**

```sql
-- mv_posts table
CREATE INDEX idx_posts_user_created ON mv_posts(user_id, created_at DESC);
CREATE INDEX idx_posts_privacy_created ON mv_posts(privacy, created_at DESC);
CREATE INDEX idx_posts_media_type ON mv_posts(media_type);

-- mv_friendships table
CREATE INDEX idx_friendships_requester ON mv_friendships(requester_id, status);
CREATE INDEX idx_friendships_addressee ON mv_friendships(addressee_id, status);
CREATE INDEX idx_friendships_status ON mv_friendships(status, created_at DESC);

-- mv_post_likes table
CREATE INDEX idx_likes_post_user ON mv_post_likes(post_id, user_id);
CREATE INDEX idx_likes_user_created ON mv_post_likes(user_id, created_at DESC);

-- mv_post_comments table
CREATE INDEX idx_comments_post_created ON mv_post_comments(post_id, created_at ASC);
CREATE INDEX idx_comments_user_created ON mv_post_comments(user_id, created_at DESC);
CREATE INDEX idx_comments_parent ON mv_post_comments(parent_id);

-- mv_notifications table
CREATE INDEX idx_notifications_user_read ON mv_notifications(user_id, is_read, created_at DESC);
CREATE INDEX idx_notifications_type ON mv_notifications(type, created_at DESC);
```

**Composite Indexes (Performance Critical)**

```sql
-- Feed query optimization
CREATE INDEX idx_posts_feed ON mv_posts(privacy, created_at DESC, user_id);

-- Friend feed optimization
CREATE INDEX idx_friendships_active ON mv_friendships(requester_id, addressee_id, status)
WHERE status = 'accepted';

-- Notification optimization
CREATE INDEX idx_notifications_unread ON mv_notifications(user_id, is_read, created_at DESC)
WHERE is_read = FALSE;
```

### **2. Database Partitioning**

**Time-based Partitioning untuk Posts**

```sql
-- Partition posts by month untuk archiving
ALTER TABLE mv_posts PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202401 VALUES LESS THAN (202402),
    PARTITION p202402 VALUES LESS THAN (202403),
    PARTITION p202403 VALUES LESS THAN (202404),
    -- ... continue for each month
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### **3. Query Optimization**

**Feed Query dengan JOIN Optimization**

```sql
-- Optimized feed query
SELECT
    p.id, p.content, p.media_url, p.media_type, p.created_at,
    u.username, u.foto_profil,
    COUNT(DISTINCT l.id) as like_count,
    COUNT(DISTINCT c.id) as comment_count,
    CASE WHEN pl.id IS NOT NULL THEN 1 ELSE 0 END as is_liked
FROM mv_posts p
INNER JOIN auth_admin u ON p.user_id = u.id
LEFT JOIN mv_post_likes l ON p.id = l.post_id
LEFT JOIN mv_post_comments c ON p.id = c.post_id
LEFT JOIN mv_post_likes pl ON p.id = pl.post_id AND pl.user_id = ?
WHERE p.privacy = 'public'
   OR p.user_id = ?
   OR p.user_id IN (
       SELECT CASE
           WHEN requester_id = ? THEN addressee_id
           ELSE requester_id
       END
       FROM mv_friendships
       WHERE (requester_id = ? OR addressee_id = ?)
       AND status = 'accepted'
   )
GROUP BY p.id
ORDER BY p.created_at DESC
LIMIT 20 OFFSET ?;
```

## ⚡ **Caching Strategy**

### **1. Redis Caching Layers**

**Application-level Caching**

```php
// Cache user profiles (TTL: 1 hour)
$cacheKey = "mv_profile_{$userId}";
$profile = $this->cache->get($cacheKey);
if (!$profile) {
    $profile = $this->mvProfileModel->getProfile($userId);
    $this->cache->save($cacheKey, $profile, 3600);
}

// Cache friend lists (TTL: 30 minutes)
$cacheKey = "mv_friends_{$userId}";
$friends = $this->cache->get($cacheKey);
if (!$friends) {
    $friends = $this->mvFriendModel->getFriends($userId);
    $this->cache->save($cacheKey, $friends, 1800);
}

// Cache trending posts (TTL: 15 minutes)
$cacheKey = "mv_trending_posts";
$trending = $this->cache->get($cacheKey);
if (!$trending) {
    $trending = $this->mvPostModel->getTrendingPosts();
    $this->cache->save($cacheKey, $trending, 900);
}
```

**Database Query Caching**

```php
// Cache expensive queries
$cacheKey = "mv_feed_{$userId}_page_{$page}";
$feed = $this->cache->get($cacheKey);
if (!$feed) {
    $feed = $this->mvPostModel->getFeed($userId, $page);
    $this->cache->save($cacheKey, $feed, 300); // 5 minutes
}
```

### **2. CDN Strategy**

**Static Assets**

```php
// Image optimization dengan CDN
public function getOptimizedImage($imagePath, $width = null, $height = null) {
    $cdnUrl = 'https://cdn.minhaverse.com';
    $optimizedPath = $imagePath;

    if ($width || $height) {
        $optimizedPath .= "?w={$width}&h={$height}&q=80&f=webp";
    }

    return $cdnUrl . '/' . $optimizedPath;
}
```

## 📱 **Frontend Optimization**

### **1. Lazy Loading Strategy**

**Image Lazy Loading**

```javascript
// Intersection Observer untuk lazy loading
const imageObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      const img = entry.target;
      img.src = img.dataset.src;
      img.classList.remove("lazy");
      imageObserver.unobserve(img);
    }
  });
});

// Apply to all lazy images
document.querySelectorAll("img[data-src]").forEach((img) => {
  imageObserver.observe(img);
});
```

**Infinite Scroll dengan Virtual Scrolling**

```javascript
// Virtual scrolling untuk feed panjang
class VirtualFeed {
  constructor(container, itemHeight = 400) {
    this.container = container;
    this.itemHeight = itemHeight;
    this.visibleItems = Math.ceil(container.clientHeight / itemHeight) + 2;
    this.startIndex = 0;
    this.endIndex = this.visibleItems;
  }

  renderVisibleItems(posts) {
    const visiblePosts = posts.slice(this.startIndex, this.endIndex);
    this.container.innerHTML = "";

    visiblePosts.forEach((post, index) => {
      const postElement = this.createPostElement(post);
      postElement.style.transform = `translateY(${
        (this.startIndex + index) * this.itemHeight
      }px)`;
      this.container.appendChild(postElement);
    });
  }
}
```

### **2. Bundle Optimization**

**Code Splitting**

```javascript
// Dynamic imports untuk route-based splitting
const routes = {
  "/": () => import("./pages/feed.js"),
  "/profile": () => import("./pages/profile.js"),
  "/friends": () => import("./pages/friends.js"),
  "/search": () => import("./pages/search.js"),
};

// Lazy load components
const loadComponent = async (componentName) => {
  const module = await import(`./components/${componentName}.js`);
  return module.default;
};
```

**Asset Optimization**

```javascript
// Image optimization
const optimizeImage = (file, maxWidth = 1200, quality = 0.8) => {
  return new Promise((resolve) => {
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    const img = new Image();

    img.onload = () => {
      const ratio = Math.min(maxWidth / img.width, maxWidth / img.height);
      canvas.width = img.width * ratio;
      canvas.height = img.height * ratio;

      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      canvas.toBlob(resolve, "image/webp", quality);
    };

    img.src = URL.createObjectURL(file);
  });
};
```

## 🔄 **Real-time Optimization**

### **1. WebSocket Connection Management**

**Connection Pooling**

```javascript
class WebSocketManager {
  constructor() {
    this.connections = new Map();
    this.reconnectAttempts = 0;
    this.maxReconnectAttempts = 5;
  }

  connect(userId) {
    const ws = new WebSocket(`wss://minhaverse.com/ws/${userId}`);

    ws.onopen = () => {
      this.reconnectAttempts = 0;
      this.connections.set(userId, ws);
    };

    ws.onclose = () => {
      this.handleReconnect(userId);
    };

    return ws;
  }

  handleReconnect(userId) {
    if (this.reconnectAttempts < this.maxReconnectAttempts) {
      setTimeout(() => {
        this.reconnectAttempts++;
        this.connect(userId);
      }, 1000 * this.reconnectAttempts);
    }
  }
}
```

### **2. Event Batching**

**Batch Notifications**

```php
// Batch multiple notifications
public function batchNotifications($notifications) {
    $batched = [];
    $userGroups = [];

    // Group by user
    foreach ($notifications as $notification) {
        $userGroups[$notification['user_id']][] = $notification;
    }

    // Send batched notifications
    foreach ($userGroups as $userId => $userNotifications) {
        $this->sendBatchedNotification($userId, $userNotifications);
    }
}
```

## 📊 **Monitoring & Analytics**

### **1. Performance Monitoring**

**Database Query Monitoring**

```php
// Query performance tracking
class QueryMonitor {
    private $queries = [];
    private $slowQueryThreshold = 100; // ms

    public function logQuery($sql, $executionTime) {
        $this->queries[] = [
            'sql' => $sql,
            'time' => $executionTime,
            'timestamp' => time()
        ];

        if ($executionTime > $this->slowQueryThreshold) {
            $this->logSlowQuery($sql, $executionTime);
        }
    }

    private function logSlowQuery($sql, $time) {
        log_message('warning', "Slow query detected: {$time}ms - {$sql}");
    }
}
```

**Frontend Performance Tracking**

```javascript
// Performance monitoring
class PerformanceMonitor {
  static trackPageLoad() {
    window.addEventListener("load", () => {
      const perfData = performance.getEntriesByType("navigation")[0];
      const loadTime = perfData.loadEventEnd - perfData.loadEventStart;

      // Send to analytics
      this.sendMetric("page_load_time", loadTime);
    });
  }

  static trackUserInteraction(action, element) {
    const startTime = performance.now();

    // Track interaction time
    setTimeout(() => {
      const endTime = performance.now();
      this.sendMetric("interaction_time", endTime - startTime, {
        action: action,
        element: element.tagName,
      });
    }, 0);
  }
}
```

### **2. Resource Usage Optimization**

**Memory Management**

```javascript
// Cleanup unused resources
class ResourceManager {
  static cleanup() {
    // Clear old cached data
    this.clearOldCache();

    // Remove unused DOM elements
    this.removeUnusedElements();

    // Clear event listeners
    this.clearEventListeners();
  }

  static clearOldCache() {
    const cache = window.mvCache;
    const now = Date.now();
    const maxAge = 30 * 60 * 1000; // 30 minutes

    Object.keys(cache).forEach((key) => {
      if (now - cache[key].timestamp > maxAge) {
        delete cache[key];
      }
    });
  }
}
```

## 🚀 **Deployment Optimization**

### **1. Server Configuration**

**Nginx Configuration**

```nginx
# Gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;

# Browser caching
location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# API caching
location /minhaverse/api/ {
    proxy_cache api_cache;
    proxy_cache_valid 200 5m;
    proxy_cache_valid 404 1m;
}
```

**PHP-FPM Optimization**

```ini
; php-fpm.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000
```

### **2. Database Configuration**

**MySQL Optimization**

```ini
# my.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
query_cache_size = 64M
query_cache_type = 1
max_connections = 200
```

## 📈 **Scaling Strategy**

### **1. Horizontal Scaling**

**Load Balancer Configuration**

```nginx
upstream minhaverse_backend {
    server 127.0.0.1:8001;
    server 127.0.0.1:8002;
    server 127.0.0.1:8003;
}

server {
    listen 80;
    location / {
        proxy_pass http://minhaverse_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

### **2. Database Scaling**

**Read Replicas**

```php
// Database configuration untuk read replicas
class DatabaseConfig {
    public function getReadConnection() {
        return [
            'hostname' => 'read-replica.minhaverse.com',
            'database' => 'minhaverse_read',
            'username' => 'read_user',
            'password' => 'read_password'
        ];
    }

    public function getWriteConnection() {
        return [
            'hostname' => 'master.minhaverse.com',
            'database' => 'minhaverse_write',
            'username' => 'write_user',
            'password' => 'write_password'
        ];
    }
}
```

## 🎯 **Performance Targets**

### **Response Time Goals**

- **Feed Load**: < 200ms
- **Post Creation**: < 300ms
- **Like/Comment**: < 100ms
- **Search Results**: < 500ms
- **Profile Load**: < 150ms

### **Throughput Goals**

- **Concurrent Users**: 10,000+
- **Posts per Second**: 100+
- **API Requests**: 1,000+ per second
- **Database Queries**: < 50ms average

### **Resource Usage**

- **Memory Usage**: < 512MB per process
- **CPU Usage**: < 70% average
- **Database Connections**: < 80% of max
- **Cache Hit Rate**: > 90%

## 📋 **Implementation Checklist**

### **Phase 1: Database Optimization**

- [ ] Implement essential indexes
- [ ] Set up query caching
- [ ] Configure database partitioning
- [ ] Optimize slow queries

### **Phase 2: Application Caching**

- [ ] Implement Redis caching
- [ ] Set up CDN for static assets
- [ ] Configure browser caching
- [ ] Implement cache invalidation

### **Phase 3: Frontend Optimization**

- [ ] Implement lazy loading
- [ ] Set up code splitting
- [ ] Optimize bundle size
- [ ] Implement virtual scrolling

### **Phase 4: Monitoring & Scaling**

- [ ] Set up performance monitoring
- [ ] Configure load balancing
- [ ] Implement database scaling
- [ ] Set up automated scaling

---

_Last Updated: [Current Date]_
_Version: 1.0_

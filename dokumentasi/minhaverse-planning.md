# 📋 MinhaVerse - Social Media Platform Planning

## 🎯 **Overview**

MinhaVerse adalah platform sosial media internal untuk komunitas Minha, terpisah dari sistem user utama dengan struktur modular dan SPA routing sendiri.

## 🏗️ **Project Structure**

### **Controllers**

```
app/Controllers/MinhaVerse/
├── MVMain.php              # Main feed controller
├── MVPosts.php             # Post CRUD operations
├── MVProfiles.php          # User profiles
├── MVFriends.php           # Friend management
├── MVNotifications.php      # Real-time notifications
├── MVSearch.php            # Search & discovery
└── MVUpload.php            # Media upload handling
```

### **Models**

```
app/Models/MinhaVerse/
├── MVPostModel.php         # Posts data management
├── MVFriendModel.php       # Friendships data
├── MVLikeModel.php         # Likes data
├── MVCommentModel.php      # Comments data
├── MVProfileModel.php      # User profiles
└── MVNotificationModel.php # Notifications data
```

### **Views**

```
app/Views/minhaverse/
├── layout/
│   ├── mv_template.php     # Main MinhaVerse layout
│   └── mv_navbar.php       # Navigation component
├── pages/
│   ├── mv_feed.php         # Main feed page
│   ├── mv_profile.php      # User profile page
│   ├── mv_friends.php      # Friends management
│   ├── mv_search.php       # Search results
│   └── mv_notifications.php # Notifications page
├── components/
│   ├── post_card.php       # Post component
│   ├── user_card.php       # User card component
│   ├── comment_section.php # Comments component
│   ├── upload_modal.php    # Upload modal
│   └── friend_request.php  # Friend request component
└── partials/
    ├── mv_sidebar.php      # Sidebar component
    ├── mv_stories.php       # Stories component
    └── mv_trending.php      # Trending posts
```

### **Assets**

```
public/
├── css/minhaverse/
│   ├── mv-main.css         # Main styles
│   ├── mv-components.css   # Component styles
│   ├── mv-responsive.css    # Responsive styles
│   └── mv-animations.css   # Animation styles
├── js/minhaverse/
│   ├── mv-app.js           # Main application
│   ├── mv-feed.js          # Feed management
│   ├── mv-posts.js         # Post operations
│   ├── mv-friends.js       # Friend system
│   ├── mv-upload.js        # File upload
│   ├── mv-notifications.js # Real-time updates
│   └── mv-router.js        # SPA routing
└── uploads/minhaverse/
    ├── posts/              # Post media
    ├── avatars/           # Profile pictures
    └── temp/              # Temporary uploads
```

## 🗄️ **Database Schema**

### **Migration Files**

```
app/Database/Migrations/MinhaVerse/
├── 2024-01-01-000001_CreateMVPosts.php
├── 2024-01-01-000002_CreateMVFriendships.php
├── 2024-01-01-000003_CreateMVPostLikes.php
├── 2024-01-01-000004_CreateMVPostComments.php
├── 2024-01-01-000005_CreateMVUserProfiles.php
├── 2024-01-01-000006_CreateMVNotifications.php
└── 2024-01-01-000007_CreateMVStories.php
```

### **Table Structures**

**mv_posts**

```sql
CREATE TABLE mv_posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT,
    media_url VARCHAR(255),
    media_type ENUM('image', 'video') NULL,
    privacy ENUM('public', 'friends_only', 'private') DEFAULT 'public',
    location VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES auth_admin(id) ON DELETE CASCADE
);
```

**mv_friendships**

```sql
CREATE TABLE mv_friendships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    requester_id INT NOT NULL,
    addressee_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected', 'blocked') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES auth_admin(id) ON DELETE CASCADE,
    FOREIGN KEY (addressee_id) REFERENCES auth_admin(id) ON DELETE CASCADE,
    UNIQUE KEY unique_friendship (requester_id, addressee_id)
);
```

**mv_post_likes**

```sql
CREATE TABLE mv_post_likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES mv_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES auth_admin(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (post_id, user_id)
);
```

**mv_post_comments**

```sql
CREATE TABLE mv_post_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES mv_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES auth_admin(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES mv_post_comments(id) ON DELETE CASCADE
);
```

**mv_user_profiles**

```sql
CREATE TABLE mv_user_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    bio TEXT NULL,
    website VARCHAR(255) NULL,
    location VARCHAR(255) NULL,
    birth_date DATE NULL,
    privacy_settings JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES auth_admin(id) ON DELETE CASCADE
);
```

**mv_notifications**

```sql
CREATE TABLE mv_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('like', 'comment', 'friend_request', 'friend_accepted', 'mention') NOT NULL,
    from_user_id INT NOT NULL,
    post_id INT NULL,
    comment_id INT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES auth_admin(id) ON DELETE CASCADE,
    FOREIGN KEY (from_user_id) REFERENCES auth_admin(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES mv_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (comment_id) REFERENCES mv_post_comments(id) ON DELETE CASCADE
);
```

## 🛣️ **Routing Structure**

### **Routes Configuration**

```php
// app/Config/Routes.php - MinhaVerse Routes
$routes->group('minhaverse', ['namespace' => 'MinhaVerse'], function($routes) {
    // Main pages
    $routes->get('/', 'MVMain::index');
    $routes->get('/feed', 'MVMain::feed');
    $routes->get('/profile/(:num)', 'MVProfiles::view/$1');
    $routes->get('/profile/edit', 'MVProfiles::edit');
    $routes->get('/friends', 'MVFriends::index');
    $routes->get('/search', 'MVSearch::index');
    $routes->get('/notifications', 'MVNotifications::index');

    // Post operations
    $routes->post('/posts/create', 'MVPosts::create');
    $routes->post('/posts/(:num)/like', 'MVPosts::like/$1');
    $routes->post('/posts/(:num)/comment', 'MVPosts::comment/$1');
    $routes->delete('/posts/(:num)', 'MVPosts::delete/$1');

    // Friend operations
    $routes->post('/friends/request', 'MVFriends::sendRequest');
    $routes->post('/friends/accept/(:num)', 'MVFriends::accept/$1');
    $routes->post('/friends/reject/(:num)', 'MVFriends::reject/$1');
    $routes->delete('/friends/(:num)', 'MVFriends::unfriend/$1');

    // Upload operations
    $routes->post('/upload/image', 'MVUpload::image');
    $routes->post('/upload/video', 'MVUpload::video');

    // API endpoints
    $routes->group('api', function($routes) {
        $routes->get('/feed', 'MVMain::apiFeed');
        $routes->get('/notifications/count', 'MVNotifications::count');
        $routes->post('/notifications/read', 'MVNotifications::markRead');
    });
});
```

## 🎨 **UI/UX Design System**

### **Color Palette**

```css
:root {
  /* Primary Colors */
  --mv-primary: #6366f1;
  --mv-primary-dark: #4f46e5;
  --mv-primary-light: #a5b4fc;

  /* Secondary Colors */
  --mv-secondary: #8b5cf6;
  --mv-accent: #06b6d4;

  /* Status Colors */
  --mv-success: #10b981;
  --mv-warning: #f59e0b;
  --mv-danger: #ef4444;
  --mv-info: #3b82f6;

  /* Neutral Colors */
  --mv-gray-50: #f9fafb;
  --mv-gray-100: #f3f4f6;
  --mv-gray-200: #e5e7eb;
  --mv-gray-300: #d1d5db;
  --mv-gray-400: #9ca3af;
  --mv-gray-500: #6b7280;
  --mv-gray-600: #4b5563;
  --mv-gray-700: #374151;
  --mv-gray-800: #1f2937;
  --mv-gray-900: #111827;
}
```

### **Component Library**

- **Post Card**: Instagram-style post layout
- **User Card**: Profile preview with follow button
- **Comment Section**: Nested comments with replies
- **Upload Modal**: Drag & drop file upload
- **Notification Dropdown**: Real-time notifications
- **Search Bar**: Autocomplete user search
- **Friend Request**: Incoming/outgoing requests

## 📱 **SPA Architecture**

### **Router Configuration**

```javascript
// public/js/minhaverse/mv-router.js
const MVRouter = {
  routes: {
    "/": "feed",
    "/profile/:id": "profile",
    "/friends": "friends",
    "/search": "search",
    "/notifications": "notifications",
  },

  init() {
    // Initialize SPA routing
    this.handleRoute();
    window.addEventListener("popstate", () => this.handleRoute());
  },

  navigate(path) {
    history.pushState({}, "", path);
    this.handleRoute();
  },

  handleRoute() {
    // Route handling logic
  },
};
```

### **State Management**

```javascript
// public/js/minhaverse/mv-state.js
const MVState = {
  user: null,
  posts: [],
  friends: [],
  notifications: [],

  // State management methods
  setUser(user) {
    this.user = user;
  },
  addPost(post) {
    this.posts.unshift(post);
  },
  updatePost(id, data) {
    /* update logic */
  },
  removePost(id) {
    /* remove logic */
  },
};
```

## 🚀 **Development Phases**

### **Phase 1: Foundation (Week 1-2)**

- [ ] Database schema & migrations
- [ ] Basic controller structure
- [ ] Main layout template
- [ ] SPA routing system
- [ ] Authentication integration

### **Phase 2: Core Features (Week 3-4)**

- [ ] Post creation & display
- [ ] User profiles
- [ ] Basic friend system
- [ ] Like/comment functionality
- [ ] File upload system

### **Phase 3: Social Features (Week 5-6)**

- [ ] Advanced friend management
- [ ] Real-time notifications
- [ ] Search functionality
- [ ] Privacy settings
- [ ] Content moderation

### **Phase 4: Polish & Optimization (Week 7-8)**

- [ ] Performance optimization
- [ ] Mobile responsiveness
- [ ] Accessibility improvements
- [ ] Testing & bug fixes
- [ ] Documentation

## 🔧 **Technical Requirements**

### **Dependencies**

- CodeIgniter 4 (existing)
- Tailwind CSS (existing)
- Alpine.js (existing)
- Axios (existing)
- Font Awesome (existing)

### **New Dependencies**

- WebSocket support (optional)
- Image processing library
- File validation library

### **Performance Considerations**

- Lazy loading for images
- Infinite scroll for feed
- Caching for frequent queries
- CDN for static assets
- Database indexing

## 📊 **Progress Tracking**

### **Completed Tasks**

- [ ] Project structure planning
- [ ] Database schema design
- [ ] UI/UX design system
- [ ] SPA architecture planning

### **In Progress**

- [ ] Database migrations
- [ ] Controller implementation
- [ ] View templates

### **Pending**

- [ ] Model implementation
- [ ] JavaScript modules
- [ ] Testing
- [ ] Deployment

## 📝 **Notes**

- MinhaVerse akan terpisah sepenuhnya dari sistem user utama
- Menggunakan SPA routing sendiri tanpa terikat dengan usertemplate
- Struktur modular untuk kemudahan maintenance
- Fokus pada performance dan user experience
- Dokumentasi lengkap untuk setiap komponen

---

_Last Updated: [Current Date]_
_Version: 1.0_

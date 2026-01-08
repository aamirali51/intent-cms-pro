# Intent CMS Pro - Architecture

## Overview

Intent CMS Pro is a modern, AI-native content management system built on the **Intent Framework v0.8.1**. It uses a **Multi-Page PHP Application** architecture for the admin panel, preserving a "Single Page App" feel through a shared layout and vanilla JavaScript for dynamic interactions.

---

## Technology Stack

| Layer | Technology |
|-------|------------|
| **Backend** | PHP 8.2+, Intent Framework 0.8.1 |
| **Frontend** | Multi-Page PHP + Vanilla JS + Tailwind CSS |
| **Editor** | Editor.js (Block-based, outputting JSON) |
| **Database** | MySQL (icms) |
| **Icons** | Material Icons Round |
| **Font** | Inter (Google Fonts) |
| **Testing** | PHPUnit 12.5, PHPStan Level 9 |

---

## Directory Structure

```
intent-cms-pro/
├── app/
│   ├── Handlers/
│   │   ├── Auth/             # Login/Logout handlers
│   │   ├── Content/          # Post/Page handlers
│   │   └── Media/            # Media handlers
│   ├── Middleware/
│   │   └── AuthMiddleware.php
│   ├── Services/
│   │   └── Hooks.php         # Advanced hook system (NEW)
│   └── helpers.php           # Global helper functions (NEW)
├── config/
│   ├── app.php               # App + DB config
│   ├── api.php               # Versioned API routes (NEW)
│   ├── bootstrap.php         # Application bootstrap (NEW)
│   └── routes.php            # Route definitions
├── plugins/                  # Plugin directory (NEW)
│   └── example/
│       └── plugin.php        # Example plugin
├── public/
│   ├── index.php             # Front controller
│   ├── home.php              # Public homepage
│   ├── blog.php              # Blog listing
│   ├── single.php            # Single post/page view
│   ├── header.php            # Shared header
│   ├── footer.php            # Shared footer
│   ├── assets/js/
│   │   └── editorjs-renderer.js  # Editor.js block renderer
│   └── admin/
│       ├── layout.php        # Shared master layout
│       ├── dashboard.php     # Dashboard view
│       ├── posts.php         # Posts list view
│       ├── post-editor.php   # WordPress-style post editor
│       ├── pages.php         # Pages management view
│       ├── media.php         # Media library view
│       └── includes/
│           └── editorjs-init.php
├── resources/
│   └── views/
│       └── auth/login.php    # Login view
└── vendor/                   # Dependencies (DO NOT MODIFY)
```

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         Browser                                  │
66: ├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌─────────────────┐     ┌─────────────────────────────────┐   │
│  │  Login Form     │     │  Admin Panel (PHP Views)        │   │
│  │  (SSR PHP)      │     │  (/admin/posts.php, etc.)       │   │
│  │                 │     │                                   │   │
│  │  /login         │────▶│  - Server-rendered HTML Shell    │   │
│  └─────────────────┘     │  - layout.php (Shared)           │   │
│                          │  - Vanilla JS for Interactivity  │   │
│                          └──────────────┬────────────────────┘   │
│                                         │                        │
│                                         ▼                        │
│                          ┌──────────────────────────────────┐    │
│                          │  JSON API (/api/*)               │    │
│                          │  - Data Fetching (AJAX)          │    │
│                          │  - Form Submission               │    │
│                          │  - Auth via Session Cookies      │    │
│                          └──────────────┬────────────────────┘    │
│                                         │                        │
│                                         ▼                        │
│                          ┌──────────────────────────────────┐    │
│                          │  Backend (Intent Framework)      │    │
│                          │  - Handlers (Static Controllers) │    │
│                          │  - Database (MySQL)              │    │
│                          └──────────────────────────────────┘    │
```

---

## Authentication Flow

1. **Login Request** → `POST /login`
2. **LoginHandler** validates credentials & creates Session.
3. **Redirect** to `/admin/dashboard.php`.
4. **Browser** loads PHP view (Session protected via AuthMiddleware on `/admin/*` routes if configured, or client-side redirect).
5. **Layout JS (`App`)** fetches `/api/user` to confirm identity and update UI (Avatar, Name).

---

## Database Schema

### users
*Standard user table with role-based access.*

### cms_content
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| type | VARCHAR(50) | `post` or `page` |
| title | VARCHAR(255) | Content title |
| slug | VARCHAR(255) | URL slug |
| **content** | **LONGTEXT** | **JSON string (Editor.js output)** |
| status | VARCHAR(50) | `draft` or `published` |
| featured_image | VARCHAR(255) | Image URL |

### cms_media
*Stores metadata for uploaded files.*

### cms_media_folders
*Hierarchical folder structure for media organization.*

---

## Post Editor Architecture

The post editor (`post-editor.php`) follows a WordPress Gutenberg-inspired design:

```
┌─────────────────────────────────────────────────────────────┐
│  ← Posts    │ Unsaved changes │ Save Draft │ Publish │ ⚙ │
├─────────────────────────────────────────────┬───────────────┤
│                                             │ Status        │
│   Post Title                                │ [Draft ▼]     │
│   ─────────────────────────                 ├───────────────┤
│                                             │ URL Slug      │
│   [Editor.js Block Editor]                  │ [_________]   │
│   - Headers (H1-H4)                         ├───────────────┤
│   - Paragraphs                              │ Featured Image│
│   - Lists                                   │ [Upload Area] │
│   - Images (with upload)                    ├───────────────┤
│   - Quotes                                  │ Excerpt       │
│   - Delimiters                              │ [_________]   │
│                                             ├───────────────┤
│                                             │ Categories    │
│                                             │ ☐ General     │
│                                             │ ☐ News        │
│                                             ├───────────────┤
│                                             │ Tags          │
│                                             │ [_________]   │
└─────────────────────────────────────────────┴───────────────┘
```

### Editor.js Tools
| Tool | CDN Package |
|------|-------------|
| Header | `@editorjs/header@2.8.1` |
| List | `@editorjs/list@1.9.0` |
| Image | `@editorjs/image@2.9.0` |
| Quote | `@editorjs/quote@2.6.0` |
| Delimiter | `@editorjs/delimiter@1.4.0` |

### Content Storage
Editor.js outputs JSON which is serialized and stored in `cms_content.content` as a LONGTEXT string.

---

## Design System

| Component | Style |
|-----------|-------|
| **Theme** | Light/Dark Mode (Tailwind `dark:` classes) |
| **Primary Color** | `#8b5cf6` (Purple) |
| **Layout** | Fixed Sidebar + Scrollable Main Content |
| **Modals** | Global Modal System (Dynamic sizing) |

---

## Quality Assurance

```bash
composer analyse   # PHPStan Level 9
composer test      # PHPUnit tests
```

- **Strict Typing**: All backend code is `strict_types=1`.
- **No Reliance on Build Tools**: Front-end is raw ES6+ and Tailwind CDN.

---

## Advanced Hook System

Intent CMS Pro features a **modern, typed hook system** that is superior to WordPress:

### Why It's Better Than WordPress

| Aspect | WordPress | Intent CMS Pro |
|--------|-----------|----------------|
| **Typing** | Untyped, any value | Fully typed PHP 8+ |
| **Namespacing** | Global strings, collisions | Namespaced: `cms.post.saved@myplugin` |
| **Global State** | Heavy global pollution | Clean singleton service |
| **Architecture** | Core dependency | Application-layer only |
| **Performance** | Always loaded | Lazy-loaded on demand |

### Hook Categories

**Actions** - Execute side effects (logging, notifications, integrations):
```php
add_action('cms.post.saved', function(int $postId, array $data): void {
    // Send notification, log event, sync to external service
});
```

**Filters** - Modify values through a pipeline:
```php
add_filter('cms.the_content', function(string $content, int $postId): string {
    return $content . '<div class="share-buttons">...</div>';
});
```

### Hook Naming Convention

```
{namespace}.{area}.{event}@{source}

Examples:
  cms.post.saved        # Core post save event
  cms.post.saved@seo    # SEO plugin's hook
  cms.media.uploaded    # Media upload event
  cms.user.registered   # User registration
```

### Available Hooks

| Hook | Type | Arguments |
|------|------|-----------|
| `cms.post.saved` | Action | `(int $postId, array $data)` |
| `cms.post.published` | Action | `(int $postId, string $title)` |
| `cms.post.deleted` | Action | `(int $postId, array $data)` |
| `cms.the_content` | Filter | `(string $content, int $postId): string` |
| `cms.api.posts` | Filter | `(array $posts): array` |
| `cms.init` | Action | `()` |

### Plugin Development

Plugins are auto-loaded from `plugins/{name}/plugin.php`:

```php
// plugins/my-seo/plugin.php
add_filter('cms.the_content', function(string $content, int $postId): string {
    return optimize_content_for_seo($content);
}, 5); // Priority 5 = runs early
```

---

## Versioned REST API

Intent CMS Pro provides a **clean, versioned REST API** at `/api/v1/`:

### API Structure

```
/api/v1/
├── /posts          # GET, POST
├── /posts/{id}     # GET, PUT, DELETE
├── /pages          # GET
├── /pages/{id}     # GET
├── /media          # GET
├── /users          # GET (admin only)
└── /system/info    # GET
```

### Benefits Over Unversioned APIs

1. **Breaking changes** don't affect existing clients
2. **Multiple versions** can coexist during migration
3. **Clear deprecation** path for old endpoints
4. **Client SDK** generation is simpler

### Example Request

```bash
curl -X GET "https://example.com/api/v1/posts?limit=10" \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

### Response Format

```json
{
  "data": [...],
  "meta": {
    "limit": 10,
    "offset": 0,
    "version": "v1"
  }
}
```

### Legacy API

The original `/api/*` endpoints remain available for backward compatibility but new integrations should use `/api/v1/*`.

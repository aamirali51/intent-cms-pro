---
description: Intent CMS Pro architecture reference for AI assistants
---

# Intent CMS Pro - AI Architecture Reference

> **Purpose**: This document provides comprehensive context for AI assistants working on this codebase. It captures architectural decisions, conventions, and constraints to ensure consistent development.

---

## 🏗️ Project Overview

**Intent CMS Pro** is a modern, AI-native content management system built on the **Intent Framework v0.8.1**.

| Aspect | Detail |
|--------|--------|
| **Architecture** | Pure Static SPA + JSON API (Headless) |
| **Backend** | PHP 8.2+, Intent Framework 0.8.1 |
| **Frontend** | Vanilla JS SPA, Tailwind CSS |
| **Database** | MySQL (database: `icms`) |
| **Hosting** | Shared hosting compatible (no CLI required) |

---

## 📁 Directory Structure

```
intent-cms-pro/
├── app/                          # Application code
│   ├── Handlers/                 # Request handlers (controllers)
│   │   ├── Auth/
│   │   │   ├── LoginHandler.php
│   │   │   └── LogoutHandler.php
│   │   └── Media/
│   │       └── MediaHandler.php
│   └── Middleware/
│       └── AuthMiddleware.php
│
├── config/                       # Configuration files
│   ├── app.php                   # App + database config
│   ├── auth.php                  # Auth configuration
│   └── routes.php                # All routes (web + API)
│
├── database/
│   └── migrations/               # SQL migrations
│       ├── 2026_01_07_000001_create_users_table.php
│       ├── 2026_01_07_000002_create_cms_content_table.php
│       ├── 2026_01_07_000004_seed_admin_user.php
│       ├── 2026_01_07_000005_create_cms_media_table.php
│       ├── 2026_01_07_000006_add_thumbnails_to_media.php
│       └── 2026_01_07_000007_create_media_folders.php
│
├── public/                       # Web root
│   ├── index.php                 # Front controller
│   └── admin/
│       └── index.html            # SPA entry point
│
├── resources/
│   ├── css/                      # CSS files
│   └── views/                    # Server-rendered views
│       ├── auth/login.php
│       ├── dashboard.php
│       └── ...
│
├── storage/
│   └── cache/                    # Framework cache
│
├── tests/                        # PHPUnit tests
│   ├── TestCase.php
│   ├── Unit/
│   └── Feature/
│
├── vendor/intent/framework/src/  # Intent Framework
│   ├── Core/                     # 29 core components
│   └── helpers.php               # Global helper functions
│
├── composer.json
├── phpstan.neon                  # PHPStan Level 9
├── phpstan-baseline.neon
└── phpunit.xml
```

---

## 🎯 Core Constraints (MUST FOLLOW)

> [!CAUTION]
> These rules are NON-NEGOTIABLE. Violating them breaks the project.

1. **Do NOT introduce frameworks** (Laravel, Symfony, Slim, etc.)
2. **Do NOT refactor** `Core\App`, `Core\Router`, `Core\Event` unless explicitly asked
3. **Do NOT break shared hosting compatibility**
4. **Do NOT change database schemas** unless explicitly instructed
5. **Follow the existing event-driven micro-kernel architecture**
6. **Keep changes minimal, additive, and reversible**
7. **If unsure, ASK** by commenting in code instead of guessing
8. **Always provide commit message and summary text at the end**

---

## 🔧 Intent Framework Components

The framework provides these core components in `vendor/intent/framework/src/Core/`:

### Request/Response Cycle
| Component | Purpose |
|-----------|---------|
| `App.php` | Application bootstrap, config loading |
| `Router.php` | Route registration and matching |
| `Route.php` | Static route facade |
| `Request.php` | HTTP request wrapper |
| `Response.php` | HTTP response builder |
| `Pipeline.php` | Middleware pipeline |
| `Middleware.php` | Middleware interface |

### Data Layer
| Component | Purpose |
|-----------|---------|
| `DB.php` | Database connection (PDO) |
| `QueryBuilder.php` | Fluent query builder |
| `Query.php` | Query helper |
| `Schema.php` | Schema builder for migrations |
| `Migration.php` | Migration base class |
| `Migrator.php` | Migration runner |

### Authentication & Security
| Component | Purpose |
|-----------|---------|
| `Auth.php` | Authentication (session + token) |
| `Session.php` | Session management |
| `ApiToken.php` | API token handling |
| `OAuth.php` | OAuth2 client |
| `RateLimiter.php` | Rate limiting |
| `SecurityHeaders.php` | Security headers |

### Utilities
| Component | Purpose |
|-----------|---------|
| `Config.php` | Configuration registry |
| `Cache.php` | PSR-16 compatible cache |
| `Log.php` | PSR-3 compatible logging |
| `Validator.php` | Input validation |
| `Upload.php` | File upload handling |
| `Paginator.php` | Pagination helper |
| `Event.php` | Event dispatcher |
| `Registry.php` | Service container |
| `Package.php` | Package management |

---

## 🌐 API Architecture

### Authentication Flow
```
1. POST /login → LoginHandler → Auth::attempt() → Session created
2. Redirect to /admin → SPA loads
3. SPA fetches /api/user → Returns current user
4. All API calls include session cookie
```

### API Endpoints (Protected by AuthMiddleware)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/user` | Current authenticated user |
| `GET` | `/api/csrf-token` | CSRF token for forms |
| `GET` | `/api/dashboard/stats` | Dashboard statistics |
| `GET` | `/api/posts` | List posts |
| `GET` | `/api/posts/{id}` | Single post |
| `GET` | `/api/pages` | List pages |
| `GET` | `/api/media` | List media |
| `POST` | `/api/media/upload` | Upload media |
| `DELETE` | `/api/media/{id}` | Delete media |
| `PUT` | `/api/media/{id}` | Update media |
| `GET` | `/api/media/folders` | List folders |
| `POST` | `/api/media/folders` | Create folder |
| `GET` | `/api/categories` | List categories |
| `GET` | `/api/users` | List users |
| `GET` | `/api/settings` | Site settings |

---

## 🗄️ Database Schema

### users
```sql
id INT PRIMARY KEY
name VARCHAR(255)
email VARCHAR(255) UNIQUE
password VARCHAR(255) -- bcrypt
role VARCHAR(50) -- user/admin
avatar VARCHAR(255)
api_token VARCHAR(255)
created_at TIMESTAMP
updated_at TIMESTAMP
```

### cms_content
```sql
id INT PRIMARY KEY
type VARCHAR(50) -- post/page
title VARCHAR(255)
slug VARCHAR(255)
content LONGTEXT
excerpt TEXT
status VARCHAR(50) -- draft/published
author_id INT FK(users)
featured_image VARCHAR(255)
created_at TIMESTAMP
updated_at TIMESTAMP
```

### cms_media
```sql
id INT PRIMARY KEY
folder_id INT FK(cms_media_folders)
filename VARCHAR(255)
path VARCHAR(512)
type VARCHAR(100) -- MIME type
size INT
thumbnail_path VARCHAR(512)
created_at TIMESTAMP
```

### cms_media_folders
```sql
id INT PRIMARY KEY
name VARCHAR(255)
parent_id INT FK(self)
created_at TIMESTAMP
updated_at TIMESTAMP
```

---

## 🔌 Plugin System (v0.9.0)

Intent CMS Pro features a hybrid plugin system that supports both procedural and Attribute-based development.

### 1. Lifecycle & Management
- **Interface**: `App\Interfaces\PluginInterface` (activate/deactivate hooks).
- **Activation**: Managed via `/admin/plugins.php`. State stored in `cms_settings` (`active_plugins`).
- **Discovery**: `PluginLoader::loadActivePlugins()` called during bootstrap.

### 2. Extensibility Pillars
- **Hooks**: Modern `Hooks` service (`add_action`, `add_filter`).
- **UI Hooks**: `cms.admin.menu` filter for sidebar items; `cms.admin.head` and `cms.admin.footer` for assets.
- **Routing**: Active plugins can register custom routes via `plugins/{name}/routes.php`.
- **Composer**: Automatic loading of `plugins/{name}/vendor/autoload.php`.

### 3. Creating a Plugin
1. Create `plugins/my-plugin/Plugin.php`.
2. Use `#[Plugin]` attribute on the class.
3. (Optional) Implement `PluginInterface` for activation logic.
4. (Optional) Create `routes.php` for custom API endpoints.

---

## 🎨 Design System

| Token | Value |
|-------|-------|
| Background | `#f3f4f6` |
| Surface | `#ffffff` |
| Primary | `#8b5cf6` (purple) |
| Primary Hover | `#7c3aed` |
| Border | `#e2e8f0` |
| Text | `#111827` |
| Text Muted | `#6b7280` |
| Font | Inter (Google Fonts) |
| Icons | Material Icons Round |
| Border Radius | 0.5rem |

---

## 🧪 Quality Assurance

```bash
# Run PHPStan analysis (Level 9 - strictest)
composer analyse

# Run PHPUnit tests
composer test

# CLI commands (available)
php intent migrate            # Run migrations
php intent migrate:rollback   # Rollback last batch
php intent serve              # Development server
php intent help               # Show all commands
```

### ❌ CLI Commands that DO NOT EXIST:
- `php intent migrate:fresh`
- `php intent migrate:status`
- `php intent make:*`
- `php intent tinker`

---

## 📝 Code Conventions

### Handler Pattern (Not Controllers)
```php
<?php
declare(strict_types=1);

namespace App\Handlers\Example;

use Core\Request;
use Core\Response;

class ExampleHandler
{
    public function index(Request $req, Response $res): Response
    {
        return $res->json(['data' => []]);
    }
}
```

### Route Definition
```php
// Closure style
Route::get('/path', function(Request $req, Response $res) {
    return $res->json([]);
});

// Handler class style
Route::get('/path', [ExampleHandler::class, 'index']);

// Route groups with middleware
Route::group(['prefix' => '/api', 'middleware' => [AuthMiddleware::class]], function () {
    Route::get('/endpoint', fn($req, $res) => $res->json([]));
});
```

### Database Queries
```php
// Raw queries (preferred for complex queries)
$results = db()->raw('SELECT * FROM table WHERE col = ?', [$value]);

// Query builder
$users = db()->table('users')->where('role', 'admin')->get();
```

---

## 🔄 Adding New Features

### Step 1: Create Handler
```php
// app/Handlers/Feature/FeatureHandler.php
namespace App\Handlers\Feature;

class FeatureHandler
{
    public function index(Request $req, Response $res): Response
    {
        // Implementation
    }
}
```

### Step 2: Register Route
```php
// config/routes.php
Route::get('/api/feature', [FeatureHandler::class, 'index']);
```

### Step 3: Add Migration (if needed)

> [!CAUTION]
> Intent Framework migrations use **raw SQL**, NOT Laravel-style Schema builders.

```php
// database/migrations/YYYY_MM_DD_HHMMSS_create_feature_table.php
<?php
declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        db()->raw('
            CREATE TABLE IF NOT EXISTS feature_table (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ');
    }

    public function down(): void
    {
        db()->raw('DROP TABLE IF EXISTS feature_table');
    }
};
```

---

## ⚠️ Common Pitfalls

1. **Config keys use dot notation**: `config('app.name')` not `config('app')['name']`
2. **Define BASE_PATH first**: Always ensure `BASE_PATH` is defined before loading framework
3. **Session started automatically**: Don't call `session_start()` manually
4. **CSRF protection**: Use `csrf_token()` helper for form protection
5. **JSON responses**: Always return `$res->json()` for API endpoints

---

## 📚 Reference Files

| File | Purpose |
|------|---------|
| [ARCHITECTURE.md](file:///f:/XAMPP/htdocs/intent-cms-pro/ARCHITECTURE.md) | Original architecture doc |
| [config/routes.php](file:///f:/XAMPP/htdocs/intent-cms-pro/config/routes.php) | All route definitions |
| [config/app.php](file:///f:/XAMPP/htdocs/intent-cms-pro/config/app.php) | App configuration |
| [public/index.php](file:///f:/XAMPP/htdocs/intent-cms-pro/public/index.php) | Front controller |
| [vendor/intent/framework/src/helpers.php](file:///f:/XAMPP/htdocs/intent-cms-pro/vendor/intent/framework/src/helpers.php) | Helper functions |

---

## 🚫 Correct Namespaces (CRITICAL)

> [!CAUTION]
> These namespaces are WRONG and will cause fatal errors:

| ❌ WRONG | ✅ CORRECT |
|----------|------------|
| `Core\Database\Migration` | `Core\Migration` |
| `Core\Database\Schema` | `Core\Schema` |
| `Core\Database\DB` | `Core\DB` |
| `Core\Http\Request` | `Core\Request` |
| `Core\Http\Response` | `Core\Response` |

**All core classes are in the `Core\` namespace directly, NOT nested.**

---

## 🔧 Helper Functions Reference

| Helper | Purpose |
|--------|---------|
| `db()` | Get QueryBuilder instance |
| `config('key')` | Get config value |
| `auth()` | Get Auth instance |
| `session()` | Get Session instance |
| `csrf_token()` | Get CSRF token |
| `validate($data, $rules)` | Create Validator |
| `redirect($url)` | Redirect response |
| `view($name, $data)` | Render view |

---

## 📦 Config Key Format

Config uses **flat dot-notation keys**, NOT nested arrays:

```php
// ✅ CORRECT:
return [
    'db.host' => 'localhost',
    'db.name' => 'icms',
    'app.debug' => true,
];

// ❌ WRONG:
return [
    'db' => [
        'host' => 'localhost', // WRONG - nested
    ],
];
```

---

*Last updated: 2026-01-07*

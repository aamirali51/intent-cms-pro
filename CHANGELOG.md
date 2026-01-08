# Intent CMS Pro - Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [Unreleased]

### Added
- **Multi-Page Admin Architecture** (Refactored from SPA)
  - `public/admin/layout.php` - Shared Master Layout with Sidebar, Header, and Global Modals
  - `public/admin/dashboard.php` - Server-rendered Dashboard view
  - `public/admin/posts.php` - Posts list view with table
  - `public/admin/post-editor.php` - **Dedicated WordPress-style post editor**
  - `public/admin/pages.php` - Pages management
  - `public/admin/media.php` - Full-featured Media Library view
  - `public/admin/includes/editorjs-init.php` - Shared Editor.js initialization logic
  - `public/admin/index.php` - Entry point redirecting to dashboard

- **WordPress-Style Post Editor** (NEW)
  - **Dedicated Editor Page**: Full-page distraction-free writing experience
  - **Editor.js Integration**: Block-based content with Header, List, Quote, Delimiter, Image tools
  - **Collapsible Sidebar Panels**: Status, URL Slug, Featured Image, Excerpt, Categories, Tags
  - **Auto-Slug Generation**: Title automatically generates URL slug
  - **Save Status Indicator**: Shows "Unsaved changes", "Saving...", "Saved"
  - **Featured Image Picker**: Integrated with Media Library

- **Rich Content Editing**
  - **Editor.js Integration**: Replaced plain HTML storage with structured JSON data.
  - **Plugins**: Header, List, Image, Quote, Delimiter (with backend upload integration).
  - **Auto-save**: Content is JSON-serialized automatically on save.

- **Media Library Enhancements**
  - **Folder Management**: Create, Rename, Delete, and Nested navigation.
  - **Bulk Actions**: Delete and Move multiple files.
  - **Drag & Drop**: Native drag-and-drop upload zone.
  - **View Modes**: Toggle between Grid and List views.

- **Public Frontend** (NEW)
  - `public/home.php` - Homepage with hero section or latest posts grid
  - `public/blog.php` - Blog listing with responsive post cards
  - `public/single.php` - Single post/page view with Editor.js rendering
  - `public/header.php` - Shared header with navigation
  - `public/footer.php` - Shared footer with copyright
  - `public/assets/js/editorjs-renderer.js` - Vanilla JS Editor.js block renderer
  - Public routes for `/`, `/blog`, and `/{slug}` catch-all

- **Advanced Hook System** (NEW)
  - `app/Services/Hooks.php` - Typed singleton hook service wrapping Core\Event
  - `app/helpers.php` - Global helper functions (add_action, do_action, add_filter, apply_filters)
  - `config/bootstrap.php` - Application bootstrap with plugin auto-loading
  - Namespaced hooks to prevent collisions (e.g., `cms.post.saved@myplugin`)
  - Priority support for execution order
  - PHPStan Level 9 compliant

- **Plugin System** (NEW)
  - Auto-discovery of plugins in `plugins/{name}/plugin.php`
  - `plugins/example/plugin.php` - Example demonstrating actions and filters

- **Versioned REST API** (NEW)
  - `config/api.php` - Clean `/api/v1/` routes with AuthMiddleware
  - Full CRUD for posts: GET, POST, PUT, DELETE
  - Pages, media, users, and system info endpoints
  - Hook integration (cms.post.saved, cms.the_content filters)

### Changed
- **Admin Panel Refactor**:
  - Converted `public/admin/index.html` (SPA) to individual PHP files to improve shared hosting compatibility and maintainability.
  - Removed client-side hash routing (`App.navigate`) in favor of standard browser navigation.
  - Moved global `App` object to `<head>` in `layout.php` for better script availability.
- **Backend Handlers**:
  - `PostHandler` methods converted to `static` to fix Router compatibility.
  - `PostHandler` now handles `LIMIT` and `OFFSET` interpolation safely.
- **Frontend Assets**:
  - Optimized Tailwind usage for PHP views.
  - Improved Modal system to support dynamic widths (e.g., wider for Editor.js).
- **API Routing Fixes**:
  - Fixed `loadCsrfToken` circular dependency in `layout.php`.
  - Moved `/api/csrf-token` route outside AuthMiddleware group for public access.
  - Updated AuthMiddleware to return JSON 401 for API routes.
  - Added dynamic `basePath` calculation for subdirectory installations.
- **Media Library Sidebar**:
  - Restored full-featured file detail panel (alt text, URL copy, download, rename, delete).

### Removed
- `public/admin/index.html` (Renamed to `spa_backup.html` as backup).
- Complex client-side routing logic from `App` object.

---

## [0.1.0] - 2026-01-07

### Initial Setup
- **Authentication System** (Login, Logout, Middleware).
- **Core Database Schema** (Users, Content, Media).
- **Basic SPA Admin** (Initial version).
- **API Endpoints** for content and media.
- **Testing Infrastructure** (PHPStan Level 9, PHPUnit).

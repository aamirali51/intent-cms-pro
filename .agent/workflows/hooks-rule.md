---
description: Hooks implementation rule - add action/filter hooks when building features
---

# Hooks Implementation Rule

**MANDATORY**: When building any new feature in Intent CMS Pro, you MUST add appropriate action and filter hooks to make the feature extensible by plugins.

## ⚠️ CRITICAL RULE: Update Documentation

**EVERY time you add or modify hooks, you MUST update `public/admin/developer.php`**:
1. Add new hooks to the "Hooks Reference" tab
2. Include hook name, type (action/filter), parameters, and example usage
3. Group hooks by feature area (Posts, Pages, Media, Users, etc.)

---

## Hook Naming Convention

```
{namespace}.{area}.{event}
```

Examples:
- `cms.post.saved` - Post was saved
- `cms.media.uploaded` - Media file uploaded
- `cms.user.login` - User logged in

---

## Current Implementation Status (AUDIT)

### ✅ IMPLEMENTED (with hooks)

| Feature | Hooks | Location |
|---------|-------|----------|
| **Posts** | `cms.post.saved`, `cms.post.published`, `cms.post.deleted`, `cms.the_content`, `cms.api.posts` | `config/api.php` |
| **Admin Layout** | `cms.admin.head`, `cms.admin.footer`, `cms.admin.menu`, `cms.init` | `layout.php`, `bootstrap.php` |

### ❌ MISSING HOOKS (need to add)

| Handler | File | Missing Hooks |
|---------|------|---------------|
| **PageHandler** | `app/Handlers/Content/PageHandler.php` | `cms.page.saved`, `cms.page.deleted`, `cms.page.published`, `cms.api.pages`, `cms.the_page_content` |
| **MediaHandler** | `app/Handlers/Media/MediaHandler.php` | `cms.media.uploaded`, `cms.media.deleted`, `cms.media.moved`, `cms.folder.created`, `cms.folder.deleted`, `cms.api.media` |
| **SettingsHandler** | `app/Handlers/Settings/SettingsHandler.php` | `cms.settings.saved`, `cms.settings.loaded` |
| **LoginHandler** | `app/Handlers/Auth/LoginHandler.php` | `cms.user.login`, `cms.user.login_failed` |
| **LogoutHandler** | `app/Handlers/Auth/LogoutHandler.php` | `cms.user.logout` |
| **PluginHandler** | `app/Handlers/Settings/PluginHandler.php` | `cms.plugin.activated`, `cms.plugin.deactivated` |
| **PluginUploadHandler** | `app/Handlers/Settings/PluginUploadHandler.php` | `cms.plugin.uploaded`, `cms.plugin.deleted` |

---

## When to Add Hooks

### Actions (Side Effects)
Add `do_action()` calls for:
- **Before** major operations: `cms.{entity}.before_save`
- **After** major operations: `cms.{entity}.saved`, `cms.{entity}.deleted`
- **State changes**: `cms.{entity}.published`, `cms.{entity}.status_changed`
- **UI injection points**: `cms.admin.{area}.head`, `cms.admin.{area}.footer`

### Filters (Modify Values)
Add `apply_filters()` calls for:
- **Content output**: `cms.the_content`, `cms.the_title`, `cms.the_excerpt`
- **API responses**: `cms.api.{entity}`, `cms.api.{entity}.single`
- **Admin UI elements**: `cms.admin.menu`, `cms.admin.columns`
- **Validation/Processing**: `cms.{entity}.validate`, `cms.{entity}.sanitize`

---

## Implementation Checklist

When building a new feature (e.g., Pages, Comments, Users), add these hooks:

### For CRUD Operations:
```php
// Before save (filter data)
$data = apply_filters('cms.page.data', $data);

// After create
do_action('cms.page.created', $id, $data);

// After update
do_action('cms.page.updated', $id, $data, $oldData);

// After delete
do_action('cms.page.deleted', $id, $data);
```

### For Content Display:
```php
// Filter content before output
$content = apply_filters('cms.the_page_content', $content, $pageId);

// Filter title
$title = apply_filters('cms.the_page_title', $title, $pageId);
```

### For API Endpoints:
```php
// Filter list response
$pages = apply_filters('cms.api.pages', $pages);

// Filter single item response
$page = apply_filters('cms.api.page', $page, $id);
```

---

## Full Hooks Reference

### Posts ✅
- `cms.post.saved` - After post create/update
- `cms.post.published` - When status changes to published
- `cms.post.deleted` - After post deletion
- `cms.the_content` - Filter post content
- `cms.api.posts` - Filter posts list API response

### Pages ❌ (TODO)
- `cms.page.saved` - After page create/update
- `cms.page.deleted` - After page deletion
- `cms.the_page_content` - Filter page content
- `cms.api.pages` - Filter pages list API response

### Media ❌ (TODO)
- `cms.media.uploaded` - After file upload
- `cms.media.deleted` - After file deletion
- `cms.media.moved` - After file moved to folder
- `cms.folder.created` - After folder creation
- `cms.folder.deleted` - After folder deletion
- `cms.api.media` - Filter media list API response

### Users ❌ (TODO)
- `cms.user.login` - After successful login
- `cms.user.login_failed` - After failed login attempt
- `cms.user.logout` - After logout
- `cms.user.registered` - After user registration
- `cms.user.updated` - After user profile update
- `cms.user.deleted` - After user deletion

### Settings ❌ (TODO)
- `cms.settings.saved` - After settings saved
- `cms.settings.loaded` - When settings loaded (filter)

### Plugins ❌ (TODO)
- `cms.plugin.activated` - After plugin activation
- `cms.plugin.deactivated` - After plugin deactivation
- `cms.plugin.uploaded` - After plugin upload
- `cms.plugin.deleted` - After plugin deletion

### Admin ✅
- `cms.init` - CMS fully initialized
- `cms.admin.head` - Admin head section
- `cms.admin.footer` - Admin footer section
- `cms.admin.menu` - Filter admin sidebar menu

---

## Example: Adding Hooks to PageHandler

```php
public static function store(Request $req, Response $res): Response
{
    $data = $req->json();
    
    // Filter data before save
    $data = apply_filters('cms.page.data', $data);
    
    // ... validation and insert ...
    
    $id = DB::connection()->lastInsertId();
    
    // Fire action after save
    do_action('cms.page.created', (int) $id, $data);
    
    if (($data['status'] ?? '') === 'published') {
        do_action('cms.page.published', (int) $id, $data['title'] ?? '');
    }
    
    return $res->json(['id' => $id]);
}
```

---

## Documentation Update Template

When adding hooks, add this to `public/admin/developer.php` in the Hooks Reference tab:

```html
<!-- cms.{entity}.{action} -->
<div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
    <div class="flex items-start justify-between">
        <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.page.saved</code>
        <span class="text-xs text-gray-400">Action</span>
    </div>
    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a page is created or updated.</p>
    <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
        <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.page.saved', function(int $pageId, array $data): void {
    // Your custom logic here
}, 10);</code></pre>
    </div>
</div>
```

# Plugin Development Guide

## Overview

Intent CMS Pro has a powerful yet simple plugin system. This guide covers everything from creating a basic plugin to advanced features.

---

## Quick Start (1 File Plugin)

Create a folder in `/plugins` and add a single `Plugin.php` file:

```
plugins/
└── my-plugin/
    └── Plugin.php
```

### Minimal Plugin

```php
<?php
use App\Attributes\Plugin;

#[Plugin(name: 'My Plugin', version: '1.0.0')]
class MyPlugin {
    // That's it! Your plugin is ready.
}
```

---

## Plugin Attributes

### #[Plugin] - Main Attribute (Required)

```php
#[Plugin(
    name: 'My Plugin',           // Display name
    version: '1.0.0',            // Semantic version
    description: 'Description',   // Short description
    author: 'Your Name',         // Author name
    authorUri: 'https://...',    // Author website
    icon: 'extension',           // Material Icon name
    tags: ['tag1', 'tag2'],      // Searchable tags
    minPhpVersion: '8.1',        // Minimum PHP version
    minCmsVersion: '1.0.0',      // Minimum CMS version
    requires: ['other-plugin' => '^1.0'], // Dependencies
    conflicts: ['bad-plugin'],   // Incompatible plugins
)]
class MyPlugin { }
```

---

### #[PluginSetting] - Define Settings

Settings are automatically saved to database and displayed in the admin UI.

```php
use App\Attributes\PluginSetting;

#[PluginSetting(
    key: 'api_key',
    type: 'password',          // text, password, number, toggle, select, textarea
    label: 'API Key',
    description: 'Enter your API key',
    default: '',
    required: true,
    group: 'general'           // Group settings together
)]
#[PluginSetting(
    key: 'enabled',
    type: 'toggle',
    label: 'Enable Feature',
    default: true
)]
#[PluginSetting(
    key: 'theme',
    type: 'select',
    label: 'Color Theme',
    options: ['light' => 'Light', 'dark' => 'Dark', 'auto' => 'Auto'],
    default: 'auto'
)]
class MyPlugin { }
```

**Reading Settings:**
```php
$manager = \App\Services\PluginManager::getInstance();
$value = $manager->getSetting('my-plugin', 'api_key', 'default');
```

---

### #[AdminMenuItem] - Add Sidebar Menu Items

```php
use App\Attributes\AdminMenuItem;

#[AdminMenuItem(
    label: 'My Plugin',
    route: '/admin/my-plugin.php',
    icon: 'dashboard',
    position: 50,              // Lower = higher in menu
    badge: 'NEW'               // Optional badge
)]
class MyPlugin { }
```

---

### #[PluginAsset] - Register CSS/JS

```php
use App\Attributes\PluginAsset;

#[PluginAsset(type: 'css', path: 'assets/style.css', location: 'admin')]
#[PluginAsset(type: 'js', path: 'assets/script.js', location: 'frontend', defer: true)]
class MyPlugin { }
```

---

## Lifecycle Hooks

Implement `PluginInterface` for lifecycle callbacks:

```php
use App\Contracts\PluginInterface;

#[Plugin(name: 'My Plugin', version: '1.0.0')]
class MyPlugin implements PluginInterface
{
    public function boot(): void
    {
        // Called every request when active
        // Register hooks, filters, routes here
        add_action('cms.footer', [$this, 'myFooter']);
    }

    public function activate(): void
    {
        // Called once on activation
        // Create database tables, set defaults
    }

    public function deactivate(): void
    {
        // Called once on deactivation
        // Preserve data for reactivation
    }

    public function uninstall(): void
    {
        // Called on permanent deletion
        // Remove all plugin data
    }
}
```

---

## Actions & Filters

### Using Actions

```php
// In your plugin boot():
add_action('cms.post.saved', [$this, 'onPostSaved']);

// Your method:
public function onPostSaved(int $postId, array $data): void
{
    // Handle the event
}
```

### Using Filters

```php
// In your plugin boot():
add_filter('cms.the_content', [$this, 'filterContent']);

// Your method:
public function filterContent(string $content, int $postId): string
{
    return $content . '<p>Added by my plugin!</p>';
}
```

### Attribute-Based Hooks

```php
use App\Attributes\Action;
use App\Attributes\Filter;

class MyPlugin
{
    #[Action('cms.post.saved', priority: 10)]
    public function onPostSaved(int $postId, array $data): void { }

    #[Filter('cms.the_content', priority: 5)]
    public function filterContent(string $content): string { }
}
```

---

## Available Hooks

### Actions
| Hook | Parameters | Description |
|------|------------|-------------|
| `cms.init` | - | CMS initialized |
| `cms.admin.head` | - | Admin `<head>` section |
| `cms.admin.footer` | - | Admin footer |
| `cms.footer` | - | Frontend footer |
| `cms.post.saved` | int $postId, array $data | Post saved |
| `cms.post.published` | int $postId, string $title | Post published |

### Filters
| Filter | Parameters | Description |
|--------|------------|-------------|
| `cms.the_content` | string $content, int $postId | Filter content |
| `cms.the_title` | string $title, int $postId | Filter title |
| `cms.post.meta` | array $meta, int $postId | Filter meta tags |

---

## Full Plugin Structure

```
plugins/
└── advanced-plugin/
    ├── Plugin.php          # Main entry point
    ├── composer.json       # Dependencies (optional)
    ├── vendor/             # Composer autoload
    ├── src/                # Additional PHP classes
    │   └── MyService.php
    ├── views/              # Templates
    │   └── admin.php
    ├── assets/
    │   ├── css/
    │   │   └── style.css
    │   └── js/
    │       └── script.js
    └── routes.php          # Custom routes (optional)
```

---

## Plugin API Reference

```php
$manager = PluginManager::getInstance();

// Discovery
$manager->discoverPlugins();
$plugins = $manager->getAll();
$plugin = $manager->get('plugin-id');

// Status
$isActive = $manager->isActive('plugin-id');

// Lifecycle
$manager->activate('plugin-id');
$manager->deactivate('plugin-id');

// Settings
$value = $manager->getSetting('plugin-id', 'key', 'default');
$manager->setSetting('plugin-id', 'key', 'value');
$all = $manager->getPluginSettings('plugin-id');
```

---

## Best Practices

1. **Use descriptive IDs** - Plugin folder name becomes the ID
2. **Validate inputs** - Never trust user data
3. **Handle errors gracefully** - Use try/catch
4. **Clean up on uninstall** - Remove all plugin data
5. **Document your hooks** - Help other developers extend your plugin
6. **Test compatibility** - Set accurate minPhpVersion/minCmsVersion

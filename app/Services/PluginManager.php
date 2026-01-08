<?php

declare(strict_types=1);

namespace App\Services;

use App\Attributes\Plugin;
use App\Attributes\PluginSetting;
use App\Attributes\AdminMenuItem;
use App\Attributes\PluginAsset;
use App\Contracts\PluginInterface;
use ReflectionClass;

/**
 * Plugin Manager
 * 
 * Central service for plugin discovery, lifecycle management, and APIs.
 * Designed to be both powerful and simple to use.
 * 
 * @example
 * ```php
 * $manager = PluginManager::getInstance();
 * $manager->discoverPlugins();
 * $manager->loadActivePlugins();
 * ```
 */
final class PluginManager
{
    private static ?self $instance = null;

    /** @var array<string, PluginInfo> */
    private array $plugins = [];

    /** @var array<string, mixed> */
    private array $settingsCache = [];

    /** @var array<int, AdminMenuItem> */
    private array $menuItems = [];

    /** @var array<int, PluginAsset> */
    private array $assets = [];

    private bool $discovered = false;

    private const CMS_VERSION = '1.0.0';

    private function __construct()
    {
        // Private constructor - use getInstance()
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get plugins directory path
     */
    private function getPluginsDir(): string
    {
        return dirname(__DIR__, 2) . '/plugins';
    }

    // ─────────────────────────────────────────────────────────────────
    // Discovery & Loading
    // ─────────────────────────────────────────────────────────────────

    /**
     * Discover all plugins in the plugins directory
     */
    public function discoverPlugins(): void
    {
        if ($this->discovered) {
            return;
        }

        $pluginsDir = $this->getPluginsDir();
        if (!is_dir($pluginsDir)) {
            $this->discovered = true;
            return;
        }

        $dirs = glob($pluginsDir . '/*', GLOB_ONLYDIR);
        if ($dirs === false) {
            $this->discovered = true;
            return;
        }

        $activeList = $this->getActivePluginIds();

        foreach ($dirs as $path) {
            $id = basename($path);
            $info = $this->loadPluginInfo($path, in_array($id, $activeList, true));
            if ($info !== null) {
                $this->plugins[$id] = $info;
            }
        }

        $this->discovered = true;
    }

    /**
     * Load plugin metadata from directory
     */
    private function loadPluginInfo(string $directory, bool $isActive): ?PluginInfo
    {
        // Find Plugin.php
        $pluginFile = $directory . '/Plugin.php';
        if (!file_exists($pluginFile)) {
            $pluginFile = $directory . '/plugin.php';
        }
        if (!file_exists($pluginFile)) {
            return null;
        }

        // Load Composer autoload if exists
        if (file_exists($directory . '/vendor/autoload.php')) {
            require_once $directory . '/vendor/autoload.php';
        }

        require_once $pluginFile;

        // Find class with #[Plugin] attribute
        $declaredClasses = get_declared_classes();
        $pluginClass = null;
        $pluginAttr = null;
        $settings = [];
        $menuItems = [];
        $assets = [];

        foreach ($declaredClasses as $className) {
            if (!class_exists($className, false)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            $file = $reflection->getFileName();
            if ($file === false || realpath($file) !== realpath($pluginFile)) {
                continue;
            }

            // Check for #[Plugin] attribute
            $attrs = $reflection->getAttributes(Plugin::class);
            if (empty($attrs)) {
                continue;
            }

            $pluginClass = $className;
            $pluginAttr = $attrs[0]->newInstance();

            // Collect #[PluginSetting] attributes
            foreach ($reflection->getAttributes(PluginSetting::class) as $attr) {
                $settings[] = $attr->newInstance();
            }

            // Collect #[AdminMenuItem] attributes
            foreach ($reflection->getAttributes(AdminMenuItem::class) as $attr) {
                $menuItems[] = $attr->newInstance();
            }

            // Collect #[PluginAsset] attributes
            foreach ($reflection->getAttributes(PluginAsset::class) as $attr) {
                $assets[] = $attr->newInstance();
            }

            break;
        }

        if ($pluginClass === null || $pluginAttr === null) {
            return null;
        }

        return new PluginInfo(
            id: basename($directory),
            path: $directory,
            className: $pluginClass,
            metadata: $pluginAttr,
            settings: $settings,
            menuItems: $menuItems,
            assets: $assets,
            isActive: $isActive,
            instance: null,
            error: null,
        );
    }

    /**
     * Load and boot all active plugins
     */
    public function loadActivePlugins(): void
    {
        $this->discoverPlugins();

        // Sort by dependencies (simple topological sort)
        $sorted = $this->sortByDependencies();

        foreach ($sorted as $id) {
            $info = $this->plugins[$id] ?? null;
            if ($info === null || !$info->isActive) {
                continue;
            }

            $this->bootPlugin($info);
        }
    }

    /**
     * Boot a single plugin
     */
    private function bootPlugin(PluginInfo $info): void
    {
        try {
            // Check compatibility
            if (!$info->metadata->isPhpCompatible()) {
                $info->error = "Requires PHP {$info->metadata->minPhpVersion}+";
                return;
            }

            if (!$info->metadata->isCmsCompatible(self::CMS_VERSION)) {
                $info->error = "Requires CMS {$info->metadata->minCmsVersion}+";
                return;
            }

            // Check dependencies
            foreach ($info->metadata->requires as $depId => $constraint) {
                if (!$this->isPluginActive($depId)) {
                    $info->error = "Missing dependency: {$depId}";
                    return;
                }
            }

            // Instantiate
            $instance = new $info->className();
            $info->instance = $instance;

            // Boot if implements PluginInterface
            if ($instance instanceof PluginInterface) {
                $instance->boot();
            }

            // Register menu items
            foreach ($info->menuItems as $item) {
                $this->menuItems[] = $item;
            }

            // Register assets
            foreach ($info->assets as $asset) {
                $this->assets[] = new PluginAssetWithPath(
                    $asset,
                    $info->path,
                    $info->id
                );
            }

            // Load routes if exists
            $routesFile = $info->path . '/routes.php';
            if (file_exists($routesFile)) {
                require_once $routesFile;
            }

        } catch (\Throwable $e) {
            $info->error = $e->getMessage();
        }
    }

    /**
     * Simple dependency sorting
     * 
     * @return array<int, string>
     */
    private function sortByDependencies(): array
    {
        $sorted = [];
        $visited = [];

        $visit = function (string $id) use (&$visit, &$sorted, &$visited): void {
            if (isset($visited[$id])) {
                return;
            }
            $visited[$id] = true;

            $info = $this->plugins[$id] ?? null;
            if ($info !== null) {
                foreach (array_keys($info->metadata->requires) as $depId) {
                    if (is_string($depId)) {
                        $visit($depId);
                    }
                }
                $sorted[] = $id;
            }
        };

        foreach (array_keys($this->plugins) as $id) {
            $visit($id);
        }

        return $sorted;
    }

    // ─────────────────────────────────────────────────────────────────
    // Plugin State Management
    // ─────────────────────────────────────────────────────────────────

    /**
     * Get all discovered plugins
     * 
     * @return array<string, PluginInfo>
     */
    public function getAll(): array
    {
        $this->discoverPlugins();
        return $this->plugins;
    }

    /**
     * Get a specific plugin
     */
    public function get(string $id): ?PluginInfo
    {
        $this->discoverPlugins();
        return $this->plugins[$id] ?? null;
    }

    /**
     * Check if a plugin is active
     */
    public function isActive(string $id): bool
    {
        return in_array($id, $this->getActivePluginIds(), true);
    }

    /**
     * Alias for isActive
     */
    public function isPluginActive(string $id): bool
    {
        return $this->isActive($id);
    }

    /**
     * Activate a plugin
     */
    public function activate(string $id): bool
    {
        $info = $this->get($id);
        if ($info === null) {
            return false;
        }

        // Check if already active
        if ($info->isActive) {
            return true;
        }

        // Validate dependencies
        foreach ($info->metadata->requires as $depId => $constraint) {
            if (!$this->isActive($depId)) {
                throw new \RuntimeException("Missing dependency: {$depId}");
            }
        }

        // Check conflicts
        foreach ($info->metadata->conflicts as $conflictId) {
            if ($this->isActive($conflictId)) {
                throw new \RuntimeException("Conflicts with: {$conflictId}");
            }
        }

        // Instantiate and call activate()
        try {
            $instance = new $info->className();
            if ($instance instanceof PluginInterface) {
                $instance->activate();
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException("Activation failed: " . $e->getMessage());
        }

        // Save to active list
        $active = $this->getActivePluginIds();
        $active[] = $id;
        $this->saveActivePluginIds($active);

        $info->isActive = true;
        $info->instance = $instance ?? null;

        return true;
    }

    /**
     * Deactivate a plugin
     */
    public function deactivate(string $id): bool
    {
        $info = $this->get($id);
        if ($info === null || !$info->isActive) {
            return false;
        }

        // Check if other plugins depend on this one
        foreach ($this->plugins as $otherId => $otherInfo) {
            if ($otherInfo->isActive && isset($otherInfo->metadata->requires[$id])) {
                throw new \RuntimeException("Required by: {$otherInfo->metadata->name}");
            }
        }

        // Call deactivate()
        try {
            $instance = $info->instance ?? new $info->className();
            if ($instance instanceof PluginInterface) {
                $instance->deactivate();
            }
        } catch (\Throwable $e) {
            // Log but continue
        }

        // Remove from active list
        $active = array_values(array_diff($this->getActivePluginIds(), [$id]));
        $this->saveActivePluginIds($active);

        $info->isActive = false;
        $info->instance = null;

        return true;
    }

    // ─────────────────────────────────────────────────────────────────
    // Settings API
    // ─────────────────────────────────────────────────────────────────

    /**
     * Get a plugin setting
     */
    public function getSetting(string $pluginId, string $key, mixed $default = null): mixed
    {
        $cacheKey = "{$pluginId}.{$key}";
        if (isset($this->settingsCache[$cacheKey])) {
            return $this->settingsCache[$cacheKey];
        }

        $allSettings = $this->getPluginSettings($pluginId);
        $value = $allSettings[$key] ?? $default;
        $this->settingsCache[$cacheKey] = $value;

        return $value;
    }

    /**
     * Set a plugin setting
     */
    public function setSetting(string $pluginId, string $key, mixed $value): void
    {
        $allSettings = $this->getPluginSettings($pluginId);
        $allSettings[$key] = $value;
        $this->savePluginSettings($pluginId, $allSettings);

        $cacheKey = "{$pluginId}.{$key}";
        $this->settingsCache[$cacheKey] = $value;
    }

    /**
     * Get all settings for a plugin
     * 
     * @return array<string, mixed>
     */
    public function getPluginSettings(string $pluginId): array
    {
        $dbKey = "plugin_settings_{$pluginId}";
        $json = site_setting($dbKey, '{}');
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Save all settings for a plugin
     * 
     * @param array<string, mixed> $settings
     */
    private function savePluginSettings(string $pluginId, array $settings): void
    {
        $dbKey = "plugin_settings_{$pluginId}";
        $json = json_encode($settings);

        $exists = db()->raw('SELECT id FROM cms_settings WHERE `key` = ?', [$dbKey]);
        if (!empty($exists)) {
            db()->raw('UPDATE cms_settings SET `value` = ?, updated_at = NOW() WHERE `key` = ?', [$json, $dbKey]);
        } else {
            db()->raw('INSERT INTO cms_settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())', [$dbKey, $json]);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Menu & Asset APIs
    // ─────────────────────────────────────────────────────────────────

    /**
     * Get all registered menu items (sorted by position)
     * 
     * @return array<int, AdminMenuItem>
     */
    public function getMenuItems(): array
    {
        usort($this->menuItems, fn($a, $b) => $a->position <=> $b->position);
        return $this->menuItems;
    }

    /**
     * Get all registered assets for a location
     * 
     * @return array<int, PluginAssetWithPath>
     */
    public function getAssets(string $location = 'admin'): array
    {
        return array_filter(
            $this->assets,
            fn($a) => $a->asset->location === $location || $a->asset->location === 'both'
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // Persistence Helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * @return array<int, string>
     */
    private function getActivePluginIds(): array
    {
        try {
            $json = site_setting('active_plugins', '[]');
            $list = json_decode($json, true);
            return is_array($list) ? $list : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @param array<int, string> $ids
     */
    private function saveActivePluginIds(array $ids): void
    {
        $json = json_encode(array_values(array_unique($ids)));

        $exists = db()->raw('SELECT id FROM cms_settings WHERE `key` = ?', ['active_plugins']);
        if (!empty($exists)) {
            db()->raw('UPDATE cms_settings SET `value` = ?, updated_at = NOW() WHERE `key` = ?', [$json, 'active_plugins']);
        } else {
            db()->raw('INSERT INTO cms_settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())', ['active_plugins', $json]);
        }
    }
}

// ─────────────────────────────────────────────────────────────────
// Data Transfer Objects
// ─────────────────────────────────────────────────────────────────

/**
 * Plugin information container
 */
final class PluginInfo
{
    public function __construct(
        public readonly string $id,
        public readonly string $path,
        public readonly string $className,
        public readonly Plugin $metadata,
        /** @var array<int, PluginSetting> */
        public readonly array $settings,
        /** @var array<int, AdminMenuItem> */
        public readonly array $menuItems,
        /** @var array<int, PluginAsset> */
        public readonly array $assets,
        public bool $isActive,
        public ?object $instance,
        public ?string $error,
    ) {}

    /**
     * Check if plugin has settings
     */
    public function hasSettings(): bool
    {
        return count($this->settings) > 0 || file_exists($this->path . '/settings.php');
    }
}

/**
 * Asset with full path information
 */
final class PluginAssetWithPath
{
    public function __construct(
        public readonly PluginAsset $asset,
        public readonly string $pluginPath,
        public readonly string $pluginId,
    ) {}

    /**
     * Get full URL to asset
     */
    public function getUrl(): string
    {
        return "/plugins/{$this->pluginId}/{$this->asset->path}";
    }
}

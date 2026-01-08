<?php

declare(strict_types=1);

namespace App\Services;

use App\Attributes\Plugin;
use App\Attributes\Action;
use App\Attributes\Filter;
use App\Interfaces\PluginInterface;
use ReflectionClass;
use ReflectionMethod;

/**
 * Plugin Loader Service
 * 
 * Discovers and loads plugins using PHP 8 Attributes.
 * Handles lifecycle (activation/deactivation), routing, and Composer autoloading.
 */
final class PluginLoader
{
    /** @var array<string, array{class: string, instance: object|null, metadata: Plugin, path: string, isActive: bool}> */
    private static array $plugins = [];

    /** @var array<int, array{hook: string, callback: callable, priority: int, source: string, type: string}> */
    private static array $registeredHooks = [];

    /**
     * Load all active plugins during bootstrap
     */
    public static function loadActivePlugins(): void
    {
        $activePlugins = self::getActivePluginsList();
        $pluginsDir = dirname(__DIR__, 2) . '/plugins';

        if (!is_dir($pluginsDir)) {
            return;
        }

        foreach ($activePlugins as $pluginDirName) {
            $path = $pluginsDir . '/' . $pluginDirName;
            if (is_dir($path)) {
                self::loadPlugin($path, true);
            }
        }
    }

    /**
     * Discover all available plugins (active or inactive)
     * Used for the Admin UI
     */
    public static function discoverAll(): void
    {
        $pluginsDir = dirname(__DIR__, 2) . '/plugins';
        if (!is_dir($pluginsDir)) {
            return;
        }

        $dirs = glob($pluginsDir . '/*', GLOB_ONLYDIR);
        if ($dirs === false) {
            return;
        }

        $activePlugins = self::getActivePluginsList();

        foreach ($dirs as $path) {
            $dirName = basename($path);
            $isActive = in_array($dirName, $activePlugins, true);
            
            // If it's already loaded (because it's active), just update the list
            // Otherwise, load it in "metadata only" mode (don't register hooks)
            self::loadPlugin($path, $isActive, !$isActive);
        }
    }

    /**
     * Load a single plugin
     * 
     * @param string $directory Full path to plugin directory
     * @param bool $registerHooks Whether to register actions/filters/routes
     * @param bool $metadataOnly If true, only reads #[Plugin] attribute, doesn't instantiate
     */
    private static function loadPlugin(string $directory, bool $registerHooks = true, bool $metadataOnly = false): void
    {
        // 1. Composer Autoloading (The "Library" Pillar)
        if ($registerHooks && file_exists($directory . '/vendor/autoload.php')) {
            require_once $directory . '/vendor/autoload.php';
        }

        // 2. Load Main Plugin File
        $pluginFile = $directory . '/Plugin.php';
        if (!file_exists($pluginFile)) {
            $pluginFile = $directory . '/plugin.php';
        }
        
        if (!file_exists($pluginFile)) {
            return;
        }

        // We use require_once to ensure classes are defined
        require_once $pluginFile;

        // 3. Find Plugin Class
        $declaredClasses = get_declared_classes();
        $pluginClass = null;
        $pluginAttr = null;

        // Scan recent classes to find one with #[Plugin]
        // (Optimization: Reverse scan or specific namespace matching could be faster)
        foreach ($declaredClasses as $className) {
            if (!class_exists($className, false)) {
                continue;
            }
            
            $reflection = new ReflectionClass($className);
            
            // Check if this class belongs to the file we just loaded
            // This prevents re-scanning the whole system
            $file = $reflection->getFileName();
            if ($file === false || realpath($file) !== realpath($pluginFile)) {
                continue;
            }

            $attributes = $reflection->getAttributes(Plugin::class);
            if (!empty($attributes)) {
                $pluginClass = $className;
                $pluginAttr = $attributes[0]->newInstance();
                break;
            }
        }

        if (!$pluginClass || !$pluginAttr) {
            return;
        }

        // 4. Store Metadata
        $dirName = basename($directory);
        
        if (!isset(self::$plugins[$dirName])) {
            self::$plugins[$dirName] = [
                'class' => $pluginClass,
                'instance' => null,
                'metadata' => $pluginAttr,
                'path' => $directory,
                'isActive' => $registerHooks,
            ];
        }

        // If we only wanted metadata, stop here
        if ($metadataOnly) {
            return;
        }

        // 5. Instantiate & Register Hooks
        $instance = new $pluginClass();
        self::$plugins[$dirName]['instance'] = $instance;
        
        if ($registerHooks) {
            self::registerHooksFromClass(new ReflectionClass($pluginClass), $instance, $pluginAttr->name);

            // 6. Custom Routing (The "API" Pillar)
            if (file_exists($directory . '/routes.php')) {
                require_once $directory . '/routes.php';
            }
        }
    }

    /**
     * Activate a plugin
     */
    public static function activate(string $pluginDirName): void
    {
        $activePlugins = self::getActivePluginsList();
        if (in_array($pluginDirName, $activePlugins, true)) {
            return;
        }

        // Load to get instance
        $pluginsDir = dirname(__DIR__, 2) . '/plugins';
        $path = $pluginsDir . '/' . $pluginDirName;
        
        self::loadPlugin($path, true);
        
        if (isset(self::$plugins[$pluginDirName]['instance'])) {
            $instance = self::$plugins[$pluginDirName]['instance'];
            if ($instance instanceof PluginInterface) {
                $instance->activate();
            }
        }

        // Save to DB
        $activePlugins[] = $pluginDirName;
        self::saveActivePluginsList($activePlugins);
    }

    /**
     * Deactivate a plugin
     */
    public static function deactivate(string $pluginDirName): void
    {
        $activePlugins = self::getActivePluginsList();
        if (!in_array($pluginDirName, $activePlugins, true)) {
            return;
        }

        // Load to get instance (if not loaded)
        if (!isset(self::$plugins[$pluginDirName]['instance'])) {
             $pluginsDir = dirname(__DIR__, 2) . '/plugins';
             self::loadPlugin($pluginsDir . '/' . $pluginDirName, true);
        }

        if (isset(self::$plugins[$pluginDirName]['instance'])) {
            $instance = self::$plugins[$pluginDirName]['instance'];
            if ($instance instanceof PluginInterface) {
                $instance->deactivate();
            }
        }

        // Remove from DB
        $activePlugins = array_values(array_diff($activePlugins, [$pluginDirName]));
        self::saveActivePluginsList($activePlugins);
    }

    /**
     * Get list of active plugins from DB
     * @return array<int, string>
     */
    private static function getActivePluginsList(): array
    {
        try {
            $setting = site_setting('active_plugins', '[]');
            $list = json_decode($setting, true);
            if (!is_array($list)) {
                return [];
            }
            // Filter to only strings to match return type
            return array_values(array_filter($list, 'is_string'));
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Save list of active plugins to DB
     * @param array<int, string> $list
     */
    private static function saveActivePluginsList(array $list): void
    {
        $json = json_encode(array_unique($list));
        
        // Upsert logic manually
        $exists = db()->raw('SELECT id FROM cms_settings WHERE `key` = ?', ['active_plugins']);
        if (!empty($exists)) {
            db()->raw('UPDATE cms_settings SET `value` = ?, updated_at = NOW() WHERE `key` = ?', [$json, 'active_plugins']);
        } else {
            db()->raw('INSERT INTO cms_settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())', ['active_plugins', $json]);
        }
    }

    /**
     * Register hooks from attributed methods
     * 
     * @param ReflectionClass<object> $reflection
     */
    private static function registerHooksFromClass(
        ReflectionClass $reflection,
        object $instance,
        string $pluginName
    ): void {
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Check for Action attribute
            $actionAttrs = $method->getAttributes(Action::class);
            foreach ($actionAttrs as $attr) {
                /** @var Action $action */
                $action = $attr->newInstance();
                $callback = [$instance, $method->getName()];
                
                if (is_callable($callback)) {
                    add_action($action->hook, $callback, $action->priority);
                    
                    self::$registeredHooks[] = [
                        'hook' => $action->hook,
                        'callback' => $callback,
                        'priority' => $action->priority,
                        'source' => $pluginName,
                        'type' => 'action',
                    ];
                }
            }

            // Check for Filter attribute
            $filterAttrs = $method->getAttributes(Filter::class);
            foreach ($filterAttrs as $attr) {
                /** @var Filter $filter */
                $filter = $attr->newInstance();
                $callback = [$instance, $method->getName()];
                
                if (is_callable($callback)) {
                    add_filter($filter->hook, $callback, $filter->priority);
                    
                    self::$registeredHooks[] = [
                        'hook' => $filter->hook,
                        'callback' => $callback,
                        'priority' => $filter->priority,
                        'source' => $pluginName,
                        'type' => 'filter',
                    ];
                }
            }
        }
    }

    /**
     * Get all loaded plugins
     * 
     * @return array<string, array{class: string, instance: object|null, metadata: Plugin, path: string, isActive: bool}>
     */
    public static function getPlugins(): array
    {
        return self::$plugins;
    }

    /**
     * Get all registered hooks
     * 
     * @return array<int, array{hook: string, callback: callable, priority: int, source: string, type: string}>
     */
    public static function getRegisteredHooks(): array
    {
        return self::$registeredHooks;
    }

    /**
     * Get plugin count
     */
    public static function count(): int
    {
        return count(self::$plugins);
    }
}

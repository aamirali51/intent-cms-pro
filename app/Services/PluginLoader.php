<?php

declare(strict_types=1);

namespace App\Services;

use App\Attributes\Plugin;
use App\Attributes\Action;
use App\Attributes\Filter;
use ReflectionClass;
use ReflectionMethod;

/**
 * Plugin Loader Service
 * 
 * Discovers and loads plugins using PHP 8 Attributes.
 * Automatically registers action and filter hooks from decorated methods.
 */
final class PluginLoader
{
    /** @var array<string, array{class: string, instance: object, metadata: Plugin}> */
    private static array $plugins = [];

    /** @var array<string, array{hook: string, callback: callable, priority: int, source: string}> */
    private static array $registeredHooks = [];

    /**
     * Discover and load all plugins from a directory
     */
    public static function discover(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $pluginDirs = glob($directory . '/*/');
        if ($pluginDirs === false) {
            return;
        }

        foreach ($pluginDirs as $pluginDir) {
            self::loadPluginFromDirectory($pluginDir);
        }
    }

    /**
     * Load a plugin from a directory
     */
    private static function loadPluginFromDirectory(string $directory): void
    {
        // Look for Plugin.php or plugin.php
        $pluginFile = $directory . 'Plugin.php';
        if (!file_exists($pluginFile)) {
            $pluginFile = $directory . 'plugin.php';
        }
        
        if (!file_exists($pluginFile)) {
            return;
        }

        require_once $pluginFile;

        // Find declared classes in the file
        $declaredClasses = get_declared_classes();
        
        foreach ($declaredClasses as $className) {
            if (!class_exists($className)) {
                continue;
            }
            
            $reflection = new ReflectionClass($className);
            $attributes = $reflection->getAttributes(Plugin::class);
            
            if (empty($attributes)) {
                continue;
            }

            /** @var Plugin $pluginAttr */
            $pluginAttr = $attributes[0]->newInstance();
            
            if (!$pluginAttr->enabled) {
                continue;
            }

            // Create plugin instance
            $instance = new $className();
            
            self::$plugins[$className] = [
                'class' => $className,
                'instance' => $instance,
                'metadata' => $pluginAttr,
            ];

            // Register hooks from methods
            self::registerHooksFromClass($reflection, $instance, $pluginAttr->name);
        }
    }

    /**
     * Register hooks from attributed methods
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
     * Register a plugin class directly (for manual registration)
     */
    public static function register(string $className): void
    {
        if (!class_exists($className)) {
            return;
        }

        $reflection = new ReflectionClass($className);
        $attributes = $reflection->getAttributes(Plugin::class);
        
        if (empty($attributes)) {
            return;
        }

        /** @var Plugin $pluginAttr */
        $pluginAttr = $attributes[0]->newInstance();
        $instance = new $className();
        
        self::$plugins[$className] = [
            'class' => $className,
            'instance' => $instance,
            'metadata' => $pluginAttr,
        ];

        self::registerHooksFromClass($reflection, $instance, $pluginAttr->name);
    }

    /**
     * Get all loaded plugins
     * 
     * @return array<string, array{class: string, instance: object, metadata: Plugin}>
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

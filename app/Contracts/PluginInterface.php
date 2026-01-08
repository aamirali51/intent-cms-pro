<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Plugin Interface
 * 
 * Implement this interface for lifecycle hooks.
 * All methods are optional - only implement what you need.
 * 
 * @example
 * ```php
 * #[Plugin(name: 'My Plugin', version: '1.0.0')]
 * class MyPlugin implements PluginInterface
 * {
 *     public function boot(): void
 *     {
 *         add_action('cms.post.saved', [$this, 'onPostSaved']);
 *     }
 * }
 * ```
 */
interface PluginInterface
{
    /**
     * Called when the plugin is loaded (every request if active).
     * Use this for registering hooks, filters, and routes.
     */
    public function boot(): void;

    /**
     * Called once when the plugin is activated.
     * Use this for database setup, option initialization, etc.
     */
    public function activate(): void;

    /**
     * Called once when the plugin is deactivated.
     * Use this for cleanup, but preserve data for potential reactivation.
     */
    public function deactivate(): void;

    /**
     * Called when the plugin is permanently deleted.
     * Use this to remove all plugin data, options, and database tables.
     */
    public function uninstall(): void;
}

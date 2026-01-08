<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Plugin Interface
 * 
 * Implement this interface in your main Plugin class to handle
 * lifecycle events.
 */
interface PluginInterface
{
    /**
     * Called when the plugin is activated.
     * Use this to create database tables, add capabilities, etc.
     */
    public function activate(): void;

    /**
     * Called when the plugin is deactivated.
     * Use this to cleanup temporary data, clear caches, etc.
     * Note: Do not delete user data (tables) here; do that in uninstall.
     */
    public function deactivate(): void;
}

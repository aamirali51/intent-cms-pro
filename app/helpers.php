<?php

declare(strict_types=1);

/**
 * Intent CMS Pro - Application Helpers
 * 
 * Global helper functions for the hook system and other utilities.
 * These provide a clean, WordPress-like API while being fully typed.
 */

use App\Services\Hooks;

/**
 * Get the Hooks singleton instance
 */
function hooks(): Hooks
{
    return Hooks::getInstance();
}

/**
 * Register an action callback
 * 
 * Actions are hooks that perform side effects (logging, notifications, etc.)
 * 
 * @param string $hook Hook name (e.g., 'cms.post.saved', 'cms.user.registered@myplugin')
 * @param callable $callback Function to execute
 * @param int $priority Execution order (lower = earlier, default: 10)
 */
function add_action(string $hook, callable $callback, int $priority = 10): void
{
    hooks()->addAction($hook, $callback, $priority);
}

/**
 * Execute all callbacks for an action hook
 * 
 * @param string $hook Hook name
 * @param mixed ...$args Arguments passed to each callback
 */
function do_action(string $hook, mixed ...$args): void
{
    hooks()->doAction($hook, ...$args);
}

/**
 * Register a filter callback
 * 
 * Filters modify and return values through a chain of callbacks.
 * 
 * @param string $hook Hook name (e.g., 'cms.the_content', 'cms.post.title@seo')
 * @param callable $callback Function that receives value and returns modified value
 * @param int $priority Execution order (lower = earlier, default: 10)
 */
function add_filter(string $hook, callable $callback, int $priority = 10): void
{
    hooks()->addFilter($hook, $callback, $priority);
}

/**
 * Apply all registered filters to a value
 * 
 * @param string $hook Hook name
 * @param mixed $value Initial value to filter
 * @param mixed ...$args Additional arguments passed to each filter
 * @return mixed The filtered value
 */
function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
{
    return hooks()->applyFilters($hook, $value, ...$args);
}

/**
 * Check if an action hook has registered callbacks
 */
function has_action(string $hook): bool
{
    return hooks()->hasAction($hook);
}

/**
 * Check if a filter hook has registered callbacks
 */
function has_filter(string $hook): bool
{
    return hooks()->hasFilter($hook);
}

/**
 * Remove all callbacks for an action hook
 */
function remove_action(string $hook): void
{
    hooks()->removeAction($hook);
}

/**
 * Remove all callbacks for a filter hook
 */
function remove_filter(string $hook): void
{
    hooks()->removeFilter($hook);
}

/**
 * Get a site setting value from cms_settings table
 * 
 * Uses static caching to avoid repeated database queries.
 * 
 * @param string $key Setting key (e.g., 'site_title', 'tagline')
 * @param string $default Default value if setting not found
 * @return string The setting value
 */
function site_setting(string $key, string $default = ''): string
{
    /** @var array<string, string>|null $cache */
    static $cache = null;
    
    // Load all settings on first call (single query approach)
    if ($cache === null) {
        $cache = [];
        try {
            $settings = db()->raw('SELECT `key`, `value` FROM cms_settings');
            foreach ($settings as $setting) {
                if (is_array($setting) && isset($setting['key']) && is_string($setting['key'])) {
                    $value = $setting['value'] ?? '';
                    $cache[$setting['key']] = is_string($value) ? $value : '';
                }
            }
        } catch (\Throwable $e) {
            // Silently fail - use defaults
        }
    }
    
    return $cache[$key] ?? $default;
}

/**
 * Get the site title
 */
function site_title(): string
{
    return site_setting('site_title', 'Intent CMS');
}

/**
 * Get the site tagline
 */
function site_tagline(): string
{
    return site_setting('tagline', 'A modern content management system');
}

/**
 * Format a date using the configured date format
 * 
 * @param string|int $date Date string or timestamp
 * @return string Formatted date
 */
function site_date(string|int $date): string
{
    $format = site_setting('date_format', 'F j, Y');
    $timestamp = is_int($date) ? $date : strtotime($date);
    return $timestamp !== false ? date($format, $timestamp) : '';
}

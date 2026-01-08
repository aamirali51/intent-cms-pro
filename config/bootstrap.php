<?php

declare(strict_types=1);

/**
 * Intent CMS Pro - Application Bootstrap
 * 
 * This file is loaded early in the application lifecycle.
 * Load custom helpers and initialize services here.
 */

// Load application helpers (hook system, etc.)
require_once __DIR__ . '/../app/helpers.php';

// Load legacy plugins (procedural style)
$pluginsDir = __DIR__ . '/../plugins';
if (is_dir($pluginsDir)) {
    $plugins = glob($pluginsDir . '/*/plugin.php');
    if ($plugins !== false) {
        foreach ($plugins as $pluginFile) {
            require_once $pluginFile;
        }
    }
}

// Load modern plugins (PHP 8 Attribute-based)
use App\Services\PluginLoader;

// Auto-discover plugins with #[Plugin] attribute
PluginLoader::discover(__DIR__ . '/../plugins');

// Fire init action
if (function_exists('do_action')) {
    do_action('cms.init');
}

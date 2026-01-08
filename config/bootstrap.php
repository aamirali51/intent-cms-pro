<?php

declare(strict_types=1);

/**
 * Intent CMS Pro - Application Bootstrap
 * 
 * This file is loaded early in the application lifecycle.
 * Load custom helpers and initialize services here.
 */

// Define BASE_PATH if not defined (for direct access scenarios)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Manual fallback for critical services if autoloader is stale
if (!class_exists('App\Services\Hooks')) {
    require_once BASE_PATH . '/app/Services/Hooks.php';
}
if (!class_exists('App\Services\PluginManager')) {
    require_once BASE_PATH . '/app/Services/PluginManager.php';
}

// Manual require for Attributes
$attributes = ['Plugin', 'Action', 'Filter', 'PluginSetting', 'AdminMenuItem', 'PluginAsset'];
foreach ($attributes as $attr) {
    if (!class_exists("App\\Attributes\\$attr")) {
        $path = BASE_PATH . "/app/Attributes/$attr.php";
        if (file_exists($path)) {
            require_once $path;
        }
    }
}

// Manual require for Contracts
if (!interface_exists('App\Contracts\PluginInterface')) {
    $path = BASE_PATH . '/app/Contracts/PluginInterface.php';
    if (file_exists($path)) {
        require_once $path;
    }
}

// Load application helpers (hook system, etc.)
require_once __DIR__ . '/../app/helpers.php';

// Ensure Core Services are available (DB, Auth, etc.)
// This supports legacy admin pages that bypass public/index.php
if (!class_exists('Core\Registry') || !\Core\Registry::has('app')) {
    new \Core\App();
}

// Load plugins using the new PluginManager
use App\Services\PluginManager;

try {
    $pluginManager = PluginManager::getInstance();
    $pluginManager->loadActivePlugins();
} catch (\Throwable $e) {
    // Log but don't crash - plugins shouldn't break the CMS
    error_log('Plugin loading error: ' . $e->getMessage());
}

// Fire init action
if (function_exists('do_action')) {
    do_action('cms.init');
}

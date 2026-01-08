<?php

declare(strict_types=1);

/**
 * Intent CMS Entry Point
 * 
 * When using Intent Framework as a library, we need to:
 * 1. Define BASE_PATH
 * 2. Load the autoloader
 * 3. Initialize and run the App
 */

// Define BASE_PATH as the project root (one level up from public)
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Initialize and run the application
// Core\App automatically loads config/app.php and config/routes.php
$app = new \Core\App();
$app->run();

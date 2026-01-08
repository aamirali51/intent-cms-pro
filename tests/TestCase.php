<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base test case for all tests.
 */
abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Define BASE_PATH if not defined
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }
    }
}

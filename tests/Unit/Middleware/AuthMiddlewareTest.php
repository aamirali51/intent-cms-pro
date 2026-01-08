<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Middleware\AuthMiddleware;

/**
 * Unit tests for AuthMiddleware.
 */
final class AuthMiddlewareTest extends TestCase
{
    public function testAuthMiddlewareClassExists(): void
    {
        $this->assertTrue(class_exists(AuthMiddleware::class));
    }

    public function testAuthMiddlewareImplementsMiddleware(): void
    {
        $reflection = new \ReflectionClass(AuthMiddleware::class);
        $this->assertTrue($reflection->implementsInterface(\Core\Middleware::class));
    }

    public function testAuthMiddlewareHasHandleMethod(): void
    {
        $this->assertTrue(method_exists(AuthMiddleware::class, 'handle'));
    }
}

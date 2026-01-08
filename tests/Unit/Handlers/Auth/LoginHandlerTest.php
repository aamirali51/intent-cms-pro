<?php

declare(strict_types=1);

namespace Tests\Unit\Handlers\Auth;

use Tests\TestCase;
use App\Handlers\Auth\LoginHandler;

/**
 * Unit tests for LoginHandler.
 */
final class LoginHandlerTest extends TestCase
{
    public function testLoginHandlerClassExists(): void
    {
        $this->assertTrue(class_exists(LoginHandler::class));
    }

    public function testLoginHandlerHasShowMethod(): void
    {
        $this->assertTrue(method_exists(LoginHandler::class, 'show'));
    }

    public function testLoginHandlerHasAuthenticateMethod(): void
    {
        $this->assertTrue(method_exists(LoginHandler::class, 'authenticate'));
    }
}

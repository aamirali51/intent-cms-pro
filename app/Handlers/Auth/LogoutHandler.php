<?php

declare(strict_types=1);

namespace App\Handlers\Auth;

use Core\Auth;
use Core\Request;
use Core\Response;

/**
 * Logout Handler
 * 
 * Handles user logout.
 */
class LogoutHandler
{
    /**
     * Log the user out and redirect to login.
     */
    public function handle(Request $request, Response $response): Response
    {
        Auth::logout();
        
        flash('success', 'You have been logged out.');
        
        return $response->redirect('/login');
    }
}

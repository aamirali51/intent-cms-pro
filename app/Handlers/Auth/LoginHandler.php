<?php

declare(strict_types=1);

namespace App\Handlers\Auth;

use Core\Auth;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Validator;

/**
 * Login Handler
 * 
 * Handles login form display and authentication.
 */
class LoginHandler
{
    /**
     * Show the login form.
     */
    public function show(Request $request, Response $response): Response
    {
        // Ensure session is started
        Session::start();
        
        // If already logged in, redirect to admin
        if (Auth::check()) {
            return $response->redirect('/admin');
        }

        return $response->html(view('auth/login'));
    }

    /**
     * Authenticate the user.
     */
    public function authenticate(Request $request, Response $response): Response
    {
        // Ensure session is started
        Session::start();
        
        $data = $request->post;

        // Validate CSRF token using Session class
        $submittedToken = is_string($data['_token'] ?? null) ? $data['_token'] : '';
        $sessionToken = Session::get('_csrf_token');
        $sessionToken = is_string($sessionToken) ? $sessionToken : '';
        
        if ($submittedToken === '' || !hash_equals($sessionToken, $submittedToken)) {
            flash('error', 'Invalid security token. Please try again.');
            return $response->redirect('/login');
        }

        // Validate input
        $validator = new Validator($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            flash('error', $validator->errors()[0] ?? 'Invalid input.');
            return $response->redirect('/login');
        }

        // Attempt authentication
        $email = is_string($data['email'] ?? null) ? $data['email'] : '';
        $password = is_string($data['password'] ?? null) ? $data['password'] : '';
        
        if (Auth::attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            // Success - redirect to admin
            return $response->redirect('/admin');
        }

        // Failed - show error
        flash('error', 'Invalid email or password.');
        return $response->redirect('/login');
    }
}

<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\ApiToken;
use Core\Auth;
use Core\Middleware;
use Core\Request;
use Core\Response;

/**
 * Authentication Middleware
 * 
 * Supports both session-based (web) and Bearer token (API) authentication.
 */
class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): Response
    {
        // Check session first (web auth)
        if (Auth::check()) {
            return $next($request);
        }

        // Check Bearer token (API auth)
        $token = ApiToken::fromRequest($request);
        if ($token !== null) {
            $user = ApiToken::validate($token);
            if ($user !== null) {
                Auth::setUser($user);
                return $next($request);
            }
        }

        // Not authenticated
        // Check if this is an API request (by URL path or Accept header)
        $uri = $request->uri;
        $isApiRequest = str_starts_with($uri, '/api/') || $request->wantsJson();
        
        if ($isApiRequest) {
            return (new Response())->json([
                'error' => 'Unauthorized',
                'message' => 'Authentication required',
            ], 401);
        }

        // Web request - redirect to login
        return (new Response())->redirect('/login');
    }
}

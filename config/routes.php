<?php

declare(strict_types=1);

use Core\Route;
use Core\Request;
use Core\Response;
use Core\Auth;

use App\Middleware\AuthMiddleware;
use App\Handlers\Auth\LoginHandler;
use App\Handlers\Auth\LogoutHandler;

/**
 * Intent CMS Pro Routes
 * 
 * Multi-Page PHP Architecture:
 * - Public routes: /login, /logout, / (homepage), /blog, /{slug}
 * - Admin: /admin/*.php (served by Apache)
 * - API routes: /api/* and /api/v1/* → JSON responses
 */

// Load application bootstrap (helpers, plugins)
require_once __DIR__ . '/bootstrap.php';

// Load versioned API routes
require_once __DIR__ . '/api.php';

// ─────────────────────────────────────────────────────────────
// Authentication Routes (Public)
// ─────────────────────────────────────────────────────────────

Route::get('/login', function(Request $req, Response $res) {
    $handler = new LoginHandler();
    return $handler->show($req, $res);
});

Route::post('/login', function(Request $req, Response $res) {
    $handler = new LoginHandler();
    return $handler->authenticate($req, $res);
});

Route::post('/logout', function(Request $req, Response $res) {
    $handler = new LogoutHandler();
    return $handler->handle($req, $res);
});

// ─────────────────────────────────────────────────────────────
// Health Check (Public)
// ─────────────────────────────────────────────────────────────

Route::get('/health', fn(Request $req, Response $res) => 
    $res->json([
        'status' => 'ok',
        'uptime' => round((microtime(true) - INTENT_START) * 1000, 2) . 'ms',
    ])
);

// ─────────────────────────────────────────────────────────────
// SPA Catch-All Route (DEPRECATED - Now using Multi-Page PHP)
// ─────────────────────────────────────────────────────────────
// These routes are commented out because we now serve actual PHP files
// from public/admin/*.php instead of a single SPA index.html

// Route::get('/admin', function(Request $req, Response $res) { ... });
// Route::get('/admin/{path}', function(Request $req, Response $res, array $params) { ... });

// ─────────────────────────────────────────────────────────────
// Public API Routes (No Auth Required)
// ─────────────────────────────────────────────────────────────

// CSRF token must be public so the frontend can load it before any authenticated request
Route::get('/api/csrf-token', function(Request $req, Response $res) {
    return $res->json(['token' => csrf_token()]);
});

// ─────────────────────────────────────────────────────────────
// Protected API Routes (Require Authentication)
// ─────────────────────────────────────────────────────────────

Route::group(['prefix' => '/api', 'middleware' => [AuthMiddleware::class]], function () {
    
    // ─────────────────────────────────────────────────────────
    // User & Auth Endpoints
    // ─────────────────────────────────────────────────────────
    
    // Get current authenticated user
    Route::get('/user', function(Request $req, Response $res) {
        $user = Auth::user();
        if (!$user) {
            return $res->json(['error' => 'Not authenticated'], 401);
        }
        // Remove sensitive fields
        unset($user['password'], $user['api_token']);
        return $res->json($user);
    });
    
    // Get CSRF token for forms (MOVED TO PUBLIC - see above)
    // Route::get('/csrf-token', ...);
    
    // ─────────────────────────────────────────────────────────
    // Dashboard Stats
    // ─────────────────────────────────────────────────────────
    
    Route::get('/dashboard/stats', function(Request $req, Response $res) {
        // Fetch counts from database
        $posts = 0;
        $pages = 0;
        $users = 0;
        $media = 0;
        
        try {
            $result = db()->raw('SELECT COUNT(*) as count FROM cms_content WHERE type = ?', ['post']);
            $val = $result[0]['count'] ?? 0;
            $posts = is_numeric($val) ? (int) $val : 0;
        } catch (\Throwable $e) {}
        
        try {
            $result = db()->raw('SELECT COUNT(*) as count FROM cms_content WHERE type = ?', ['page']);
            $val = $result[0]['count'] ?? 0;
            $pages = is_numeric($val) ? (int) $val : 0;
        } catch (\Throwable $e) {}
        
        try {
            $result = db()->raw('SELECT COUNT(*) as count FROM users');
            $val = $result[0]['count'] ?? 0;
            $users = is_numeric($val) ? (int) $val : 0;
        } catch (\Throwable $e) {}
        
        try {
            $result = db()->raw('SELECT COUNT(*) as count FROM cms_media');
            $val = $result[0]['count'] ?? 0;
            $media = is_numeric($val) ? (int) $val : 0;
        } catch (\Throwable $e) {}
        
        return $res->json([
            'posts' => $posts,
            'pages' => $pages,
            'users' => $users,
            'media' => $media,
        ]);
    });
    
    // ─────────────────────────────────────────────────────────
    // Posts CRUD
    // ─────────────────────────────────────────────────────────
    
    Route::get('/posts', [\App\Handlers\Content\PostHandler::class, 'index']);
    Route::post('/posts', [\App\Handlers\Content\PostHandler::class, 'store']);
    Route::get('/posts/{id}', [\App\Handlers\Content\PostHandler::class, 'show']);
    Route::put('/posts/{id}', [\App\Handlers\Content\PostHandler::class, 'update']);
    Route::delete('/posts/{id}', [\App\Handlers\Content\PostHandler::class, 'destroy']);
    
    // ─────────────────────────────────────────────────────────
    // Pages CRUD
    // ─────────────────────────────────────────────────────────
    
    Route::get('/pages', function(Request $req, Response $res) {
        try {
            $pages = db()->raw('SELECT id, title, slug, status, created_at FROM cms_content WHERE type = ? ORDER BY created_at DESC LIMIT 50', ['page']);
            return $res->json($pages);
        } catch (\Throwable $e) {
            return $res->json([]);
        }
    });
    
    // ─────────────────────────────────────────────────────────
    // Media
    // ─────────────────────────────────────────────────────────
    
    Route::get('/media', [\App\Handlers\Media\MediaHandler::class, 'index']);
    Route::post('/media/upload', [\App\Handlers\Media\MediaHandler::class, 'upload']);
    Route::post('/media/bulk-delete', [\App\Handlers\Media\MediaHandler::class, 'bulkDelete']);
    Route::delete('/media/{id}', [\App\Handlers\Media\MediaHandler::class, 'delete']);
    Route::put('/media/{id}', [\App\Handlers\Media\MediaHandler::class, 'update']);
    
    // Media Folders
    Route::get('/media/folders', [\App\Handlers\Media\MediaHandler::class, 'getFolders']);
    Route::post('/media/folders', [\App\Handlers\Media\MediaHandler::class, 'createFolder']);
    Route::delete('/media/folders/{id}', [\App\Handlers\Media\MediaHandler::class, 'deleteFolder']);
    Route::post('/media/move', [\App\Handlers\Media\MediaHandler::class, 'moveFiles']);
    Route::put('/media/folders/{id}', [\App\Handlers\Media\MediaHandler::class, 'updateFolder']);
    
    // ─────────────────────────────────────────────────────────
    // Categories
    // ─────────────────────────────────────────────────────────
    
    Route::get('/categories', function(Request $req, Response $res) {
        try {
            $categories = db()->raw('SELECT * FROM cms_taxonomies WHERE type = ? ORDER BY name ASC', ['category']);
            return $res->json($categories);
        } catch (\Throwable $e) {
            return $res->json([]);
        }
    });
    
    // ─────────────────────────────────────────────────────────
    // Users
    // ─────────────────────────────────────────────────────────
    
    Route::get('/users', function(Request $req, Response $res) {
        try {
            $users = db()->raw('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 50');
            return $res->json($users);
        } catch (\Throwable $e) {
            return $res->json([]);
        }
    });
    
    // ─────────────────────────────────────────────────────────
    // Settings
    // ─────────────────────────────────────────────────────────
    
    Route::get('/settings', [\App\Handlers\Settings\SettingsHandler::class, 'index']);
    Route::put('/settings', [\App\Handlers\Settings\SettingsHandler::class, 'update']);

});

// ─────────────────────────────────────────────────────────────
// Public Frontend Routes
// ─────────────────────────────────────────────────────────────

// Homepage
Route::get('/', function(Request $req, Response $res) {
    // Check if this is an admin user trying to access admin
    if (Auth::check() && isset($_GET['admin'])) {
        return $res->redirect('/admin/dashboard.php');
    }
    // Serve public homepage
    require BASE_PATH . '/public/home.php';
    exit;
});

// Blog listing (shows all posts)
Route::get('/blog', function(Request $req, Response $res) {
    require BASE_PATH . '/public/blog.php';
    exit;
});

// Single post/page (catch-all for slugs)
Route::get('/{slug}', function(Request $req, Response $res, array $params) {
    // Skip admin, api, assets, login, logout paths
    $slug = $params['slug'] ?? '';
    $reserved = ['admin', 'api', 'assets', 'login', 'logout', 'health'];
    
    if (in_array($slug, $reserved) || str_starts_with($slug, 'admin/') || str_starts_with($slug, 'api/')) {
        return $res->json(['error' => 'Not found'], 404);
    }
    
    require BASE_PATH . '/public/single.php';
    exit;
});

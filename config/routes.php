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
    
    Route::get('/posts', fn(Request $req, Response $res) => (new \App\Handlers\Content\PostHandler())->index($req, $res));
    Route::post('/posts', fn(Request $req, Response $res) => (new \App\Handlers\Content\PostHandler())->store($req, $res));
    Route::get('/posts/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Content\PostHandler())->show($req, $res, $params));
    Route::put('/posts/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Content\PostHandler())->update($req, $res, $params));
    Route::delete('/posts/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Content\PostHandler())->destroy($req, $res, $params));
    
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
    
    Route::get('/pages/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Content\PageHandler())->show($req, $res, $params));
    Route::post('/pages', fn(Request $req, Response $res) => (new \App\Handlers\Content\PageHandler())->store($req, $res));
    Route::put('/pages/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Content\PageHandler())->update($req, $res, $params));
    Route::delete('/pages/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Content\PageHandler())->destroy($req, $res, $params));
    
    // ─────────────────────────────────────────────────────────
    // Media
    // ─────────────────────────────────────────────────────────
    
    Route::get('/media', fn(Request $req, Response $res) => (new \App\Handlers\Media\MediaHandler())->index($req, $res));
    Route::post('/media/upload', fn(Request $req, Response $res) => (new \App\Handlers\Media\MediaHandler())->upload($req, $res));
    Route::post('/media/bulk-delete', fn(Request $req, Response $res) => (new \App\Handlers\Media\MediaHandler())->bulkDelete($req, $res));
    Route::delete('/media/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Media\MediaHandler())->delete($req, $res, $params));
    Route::put('/media/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Media\MediaHandler())->update($req, $res, $params));
    
    // Media Folders
    Route::get('/media/folders', fn(Request $req, Response $res) => (new \App\Handlers\Media\MediaHandler())->getFolders($req, $res));
    Route::post('/media/folders', fn(Request $req, Response $res) => (new \App\Handlers\Media\MediaHandler())->createFolder($req, $res));
    Route::delete('/media/folders/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Media\MediaHandler())->deleteFolder($req, $res, $params));
    Route::post('/media/move', fn(Request $req, Response $res) => (new \App\Handlers\Media\MediaHandler())->moveFiles($req, $res));
    Route::put('/media/folders/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Media\MediaHandler())->updateFolder($req, $res, $params));
    
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
    // Tags CRUD
    // ─────────────────────────────────────────────────────────
    
    Route::get('/tags', fn(Request $req, Response $res) => \App\Handlers\Content\TagHandler::index($req, $res));
    Route::post('/tags', fn(Request $req, Response $res) => \App\Handlers\Content\TagHandler::store($req, $res));
    Route::get('/tags/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\TagHandler::show($req, $res, $params));
    Route::put('/tags/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\TagHandler::update($req, $res, $params));
    Route::delete('/tags/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\TagHandler::destroy($req, $res, $params));
    Route::post('/tags/bulk-delete', fn(Request $req, Response $res) => \App\Handlers\Content\TagHandler::bulkDelete($req, $res));
    Route::post('/tags/merge', fn(Request $req, Response $res) => \App\Handlers\Content\TagHandler::merge($req, $res));
    
    // ─────────────────────────────────────────────────────────
    // Comments Management
    // ─────────────────────────────────────────────────────────
    
    Route::get('/comments', fn(Request $req, Response $res) => \App\Handlers\Content\CommentHandler::index($req, $res));
    Route::post('/comments', fn(Request $req, Response $res) => \App\Handlers\Content\CommentHandler::store($req, $res));
    Route::get('/comments/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\CommentHandler::show($req, $res, $params));
    Route::put('/comments/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\CommentHandler::update($req, $res, $params));
    Route::put('/comments/{id}/status', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\CommentHandler::updateStatus($req, $res, $params));
    Route::delete('/comments/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\CommentHandler::destroy($req, $res, $params));
    Route::post('/comments/bulk', fn(Request $req, Response $res) => \App\Handlers\Content\CommentHandler::bulkAction($req, $res));
    Route::post('/comments/{id}/reply', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\CommentHandler::reply($req, $res, $params));
    Route::get('/posts/{id}/comments', fn(Request $req, Response $res, array $params) => \App\Handlers\Content\CommentHandler::forPost($req, $res, $params));
    
    // ─────────────────────────────────────────────────────────
    // Users CRUD
    // ─────────────────────────────────────────────────────────
    
    Route::get('/users', fn(Request $req, Response $res) => \App\Handlers\Settings\UserHandler::index($req, $res));
    Route::get('/users/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Settings\UserHandler::show($req, $res, $params));
    Route::post('/users', fn(Request $req, Response $res) => \App\Handlers\Settings\UserHandler::store($req, $res));
    Route::put('/users/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Settings\UserHandler::update($req, $res, $params));
    Route::delete('/users/{id}', fn(Request $req, Response $res, array $params) => \App\Handlers\Settings\UserHandler::destroy($req, $res, $params));
    
    // Settings
    Route::get('/settings', fn(Request $req, Response $res) => (new \App\Handlers\Settings\SettingsHandler())->index($req, $res));
    Route::put('/settings', fn(Request $req, Response $res) => (new \App\Handlers\Settings\SettingsHandler())->update($req, $res));
    
    // Plugins
    Route::get('/plugins', fn(Request $req, Response $res) => (new \App\Handlers\Settings\PluginHandler())->index($req, $res));
    Route::get('/plugins/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Settings\PluginHandler())->show($req, $res, $params));
    Route::post('/plugins/activate', fn(Request $req, Response $res) => (new \App\Handlers\Settings\PluginHandler())->activate($req, $res));
    Route::post('/plugins/deactivate', fn(Request $req, Response $res) => (new \App\Handlers\Settings\PluginHandler())->deactivate($req, $res));
    Route::get('/plugins/{id}/settings', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Settings\PluginHandler())->getSettings($req, $res, $params));
    Route::put('/plugins/{id}/settings', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Settings\PluginHandler())->updateSettings($req, $res, $params));
    Route::post('/plugins/upload', fn(Request $req, Response $res) => (new \App\Handlers\Settings\PluginUploadHandler())->upload($req, $res));
    Route::delete('/plugins/{id}', fn(Request $req, Response $res, array $params) => (new \App\Handlers\Settings\PluginUploadHandler())->delete($req, $res, $params));

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

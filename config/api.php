<?php

declare(strict_types=1);

/**
 * Intent CMS Pro - Versioned API Routes (v1)
 * 
 * Clean, versioned REST API structure.
 * All routes require authentication via AuthMiddleware.
 * 
 * Base URL: /api/v1/
 * 
 * Why versioned APIs are better:
 * - Breaking changes don't affect existing clients
 * - Clear deprecation path for old versions
 * - Multiple API versions can coexist
 */

use Core\Route;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Middleware\AuthMiddleware;

// ─────────────────────────────────────────────────────────────────
// API Version 1 Routes
// ─────────────────────────────────────────────────────────────────

Route::group(['prefix' => '/api/v1', 'middleware' => [AuthMiddleware::class]], function () {

    // ─────────────────────────────────────────────────────────────
    // Posts API
    // ─────────────────────────────────────────────────────────────
    
    Route::get('/posts', function (Request $req, Response $res) {
        $limitRaw = $req->get('limit');
        $offsetRaw = $req->get('offset');
        $limit = is_numeric($limitRaw) ? (int) $limitRaw : 20;
        $offset = is_numeric($offsetRaw) ? (int) $offsetRaw : 0;
        $status = $req->get('status');
        
        $query = db()->table('cms_content')->where('type', 'post');
        
        if ($status && in_array($status, ['draft', 'published'], true)) {
            $query->where('status', $status);
        }
        
        $posts = $query->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get();
        
        // Apply filters
        if (function_exists('apply_filters')) {
            $posts = apply_filters('cms.api.posts', $posts);
        }
        
        return $res->json([
            'data' => $posts,
            'meta' => [
                'limit' => $limit,
                'offset' => $offset,
                'version' => 'v1'
            ]
        ]);
    });

    Route::get('/posts/{id}', function (Request $req, Response $res, array $params) {
        $id = (int) ($params['id'] ?? 0);
        
        $post = db()->table('cms_content')
            ->where('id', $id)
            ->where('type', 'post')
            ->first();
        
        if (!$post) {
            return $res->json(['error' => 'Post not found'], 404);
        }
        
        // Apply content filter
        if (function_exists('apply_filters') && !empty($post['content'])) {
            $post['content'] = apply_filters('cms.the_content', $post['content'], $id);
        }
        
        return $res->json(['data' => $post]);
    });

    Route::post('/posts', function (Request $req, Response $res) {
        $data = $req->json();
        
        if (!is_array($data)) {
            return $res->json(['error' => 'Invalid request body'], 400);
        }
        
        $title = isset($data['title']) && is_string($data['title']) ? trim($data['title']) : '';
        $slug = isset($data['slug']) && is_string($data['slug']) ? trim($data['slug']) : '';
        $content = isset($data['content']) && is_string($data['content']) ? $data['content'] : '';
        $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : 'draft';
        $excerpt = isset($data['excerpt']) && is_string($data['excerpt']) ? $data['excerpt'] : '';
        $featured_image = isset($data['featured_image']) && is_string($data['featured_image']) ? $data['featured_image'] : '';
        
        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '');
        }
        
        $user = Auth::user();
        $authorId = is_array($user) && isset($user['id']) ? (int) $user['id'] : null;
        
        // insert() returns the last insert ID
        $postId = db()->table('cms_content')->insert([
            'type' => 'post',
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'status' => $status,
            'excerpt' => $excerpt,
            'featured_image' => $featured_image,
            'author_id' => $authorId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $postId = (int) ($postId ?: 0);
        
        // Fire action hook
        if (function_exists('do_action')) {
            do_action('cms.post.saved', $postId, $data);
            if ($status === 'published') {
                do_action('cms.post.published', $postId, $title);
            }
        }
        
        return $res->json(['id' => $postId, 'message' => 'Post created'], 201);
    });

    Route::put('/posts/{id}', function (Request $req, Response $res, array $params) {
        $id = (int) ($params['id'] ?? 0);
        $data = $req->json();
        
        if (!is_array($data)) {
            return $res->json(['error' => 'Invalid request body'], 400);
        }
        
        $existing = db()->table('cms_content')->where('id', $id)->first();
        if (!$existing) {
            return $res->json(['error' => 'Post not found'], 404);
        }
        
        $updates = ['updated_at' => date('Y-m-d H:i:s')];
        
        if (isset($data['title']) && is_string($data['title'])) {
            $updates['title'] = trim($data['title']);
        }
        if (isset($data['slug']) && is_string($data['slug'])) {
            $updates['slug'] = trim($data['slug']);
        }
        if (isset($data['content']) && is_string($data['content'])) {
            $updates['content'] = $data['content'];
        }
        if (isset($data['status']) && is_string($data['status'])) {
            $updates['status'] = $data['status'];
        }
        if (isset($data['excerpt']) && is_string($data['excerpt'])) {
            $updates['excerpt'] = $data['excerpt'];
        }
        if (isset($data['featured_image']) && is_string($data['featured_image'])) {
            $updates['featured_image'] = $data['featured_image'];
        }
        
        db()->table('cms_content')->where('id', $id)->update($updates);
        
        // Fire action hook
        if (function_exists('do_action')) {
            do_action('cms.post.saved', $id, array_merge($existing, $updates));
            
            $wasPublished = ($existing['status'] ?? '') !== 'published';
            $isPublished = ($updates['status'] ?? $existing['status'] ?? '') === 'published';
            if ($wasPublished && $isPublished) {
                do_action('cms.post.published', $id, $updates['title'] ?? $existing['title'] ?? '');
            }
        }
        
        return $res->json(['message' => 'Post updated']);
    });

    Route::delete('/posts/{id}', function (Request $req, Response $res, array $params) {
        $id = (int) ($params['id'] ?? 0);
        
        $existing = db()->table('cms_content')->where('id', $id)->first();
        if (!$existing) {
            return $res->json(['error' => 'Post not found'], 404);
        }
        
        db()->table('cms_content')->where('id', $id)->delete();
        
        // Fire action hook
        if (function_exists('do_action')) {
            do_action('cms.post.deleted', $id, $existing);
        }
        
        return $res->json(['message' => 'Post deleted']);
    });

    // ─────────────────────────────────────────────────────────────
    // Pages API
    // ─────────────────────────────────────────────────────────────
    
    Route::get('/pages', function (Request $req, Response $res) {
        $pages = db()->table('cms_content')
            ->where('type', 'page')
            ->orderBy('title', 'ASC')
            ->get();
        
        return $res->json(['data' => $pages]);
    });

    Route::get('/pages/{id}', function (Request $req, Response $res, array $params) {
        $id = (int) ($params['id'] ?? 0);
        
        $page = db()->table('cms_content')
            ->where('id', $id)
            ->where('type', 'page')
            ->first();
        
        if (!$page) {
            return $res->json(['error' => 'Page not found'], 404);
        }
        
        return $res->json(['data' => $page]);
    });

    // ─────────────────────────────────────────────────────────────
    // Media API
    // ─────────────────────────────────────────────────────────────
    
    Route::get('/media', function (Request $req, Response $res) {
        $limitRaw = $req->get('limit');
        $limit = is_numeric($limitRaw) ? (int) $limitRaw : 50;
        $folderId = $req->get('folder_id');
        
        $query = db()->table('cms_media');
        
        if ($folderId !== null && $folderId !== '' && is_numeric($folderId)) {
            $query->where('folder_id', (int) $folderId);
        }
        
        $media = $query->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
        
        return $res->json(['data' => $media]);
    });

    // ─────────────────────────────────────────────────────────────
    // Users API (Admin only)
    // ─────────────────────────────────────────────────────────────
    
    Route::get('/users', function (Request $req, Response $res) {
        $user = Auth::user();
        if (!is_array($user) || ($user['role'] ?? '') !== 'admin') {
            return $res->json(['error' => 'Forbidden'], 403);
        }
        
        $users = db()->table('users')
            ->select(['id', 'name', 'email', 'role', 'created_at'])
            ->get();
        
        return $res->json(['data' => $users]);
    });

    // ─────────────────────────────────────────────────────────────
    // System API
    // ─────────────────────────────────────────────────────────────
    
    Route::get('/system/info', function (Request $req, Response $res) {
        return $res->json([
            'name' => 'Intent CMS Pro',
            'version' => '1.0.0',
            'api_version' => 'v1',
            'php_version' => PHP_VERSION,
            'hooks_registered' => [
                'actions' => function_exists('hooks') ? hooks()->countActions('cms.post.saved') : 0,
            ]
        ]);
    });
});

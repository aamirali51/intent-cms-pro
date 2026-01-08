<?php
declare(strict_types=1);

namespace App\Handlers\Content;

use Core\Request;
use Core\Response;
use Core\Auth;
use Core\DB;

class PostHandler
{
    public static function index(Request $req, Response $res): Response
    {
        try {
            // Pagination parameters
            $pageParam = $req->get('page');
            $page = is_numeric($pageParam) ? (int) $pageParam : 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $posts = db()->raw(
                "SELECT id, title, slug, status, created_at, updated_at, author_id 
                 FROM cms_content 
                 WHERE type = ? 
                 ORDER BY created_at DESC 
                 LIMIT $limit OFFSET $offset", 
                ['post']
            );

            // Get total count
            $countResult = db()->raw('SELECT COUNT(*) as total FROM cms_content WHERE type = ?', ['post']);
            $total = $countResult[0]['total'] ?? 0;

            return $res->json([
                'data' => $posts,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'last_page' => ceil($total / $limit)
                ]
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to fetch posts: ' . $e->getMessage()], 500);
        }
    }

    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $id = (int) $params['id'];
            $result = db()->raw('SELECT * FROM cms_content WHERE id = ? AND type = ?', [$id, 'post']);
            
            if (empty($result)) {
                return $res->json(['error' => 'Post not found'], 404);
            }
            
            return $res->json($result[0]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Database error'], 500);
        }
    }

    public static function store(Request $req, Response $res): Response
    {
        try {
            $data = $req->json();
            
            // Basic validation
            if (empty($data['title'])) {
                return $res->json(['error' => 'Title is required'], 422);
            }

            $title = $data['title'];
            $slug = !empty($data['slug']) ? $data['slug'] : self::slugify($title);
            
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (self::slugExists($slug)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            db()->raw(
                'INSERT INTO cms_content (type, title, slug, content, excerpt, status, featured_image, author_id, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
                [
                    'post',
                    $title,
                    $slug,
                    is_array($data['content'] ?? '') ? json_encode($data['content']) : ($data['content'] ?? ''),
                    $data['excerpt'] ?? '',
                    $data['status'] ?? 'draft',
                    $data['featured_image'] ?? null,
                    Auth::id()
                ]
            );

            // Get last insert ID directly from PDO connection
            $id = DB::connection()->lastInsertId();
            
            return $res->json(['id' => $id, 'message' => 'Post created successfully'], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to create post: ' . $e->getMessage()], 500);
        }
    }

    public static function update(Request $req, Response $res, array $params): Response
    {
        try {
            $id = (int) $params['id'];
            $data = $req->json();

            // Check existence
            $exists = db()->raw('SELECT id FROM cms_content WHERE id = ? AND type = ?', [$id, 'post']);
            if (empty($exists)) {
                return $res->json(['error' => 'Post not found'], 404);
            }

            // Update fields
            $fields = [];
            $values = [];

            if (isset($data['title'])) {
                $fields[] = 'title = ?';
                $values[] = $data['title'];
            }
            
            if (isset($data['slug']) && !empty($data['slug'])) {
                $slug = $data['slug'];
                // Check if slug changed and is unique
                if ($slug !== self::getSlugById($id)) {
                    $originalSlug = $slug;
                    $counter = 1;
                    while (self::slugExists($slug, $id)) {
                        $slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                }
                $fields[] = 'slug = ?';
                $values[] = $slug;
            }

            if (isset($data['content'])) {
                $fields[] = 'content = ?';
                // Ensure content is string (Editor.js output might be array if parsed, but we expect serialized JSON string or array to handle)
                $content = is_array($data['content']) ? json_encode($data['content']) : $data['content'];
                $values[] = $content;
            }

            if (isset($data['excerpt'])) {
                $fields[] = 'excerpt = ?';
                $values[] = $data['excerpt'];
            }

            if (isset($data['status'])) {
                $fields[] = 'status = ?';
                $values[] = $data['status'];
            }

            if (array_key_exists('featured_image', $data)) {
                $fields[] = 'featured_image = ?';
                $values[] = $data['featured_image'];
            }

            $fields[] = 'updated_at = NOW()';

            if (empty($fields)) {
                return $res->json(['message' => 'No changes']);
            }

            $values[] = $id; // For WHERE clause

            db()->raw(
                'UPDATE cms_content SET ' . implode(', ', $fields) . ' WHERE id = ?',
                $values
            );

            return $res->json(['message' => 'Post updated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update post: ' . $e->getMessage()], 500);
        }
    }

    public static function destroy(Request $req, Response $res, array $params): Response
    {
        try {
            $id = (int) $params['id'];
            
            // Check existence
            $exists = db()->raw('SELECT id FROM cms_content WHERE id = ? AND type = ?', [$id, 'post']);
            if (empty($exists)) {
                return $res->json(['error' => 'Post not found'], 404);
            }

            db()->raw('DELETE FROM cms_content WHERE id = ?', [$id]);
            
            return $res->json(['message' => 'Post deleted successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to delete post'], 500);
        }
    }

    private static function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text) ?: 'post-' . time();
    }

    private static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM cms_content WHERE slug = ?';
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        
        $result = db()->raw($sql, $params);
        return !empty($result);
    }
    
    private static function getSlugById(int $id): ?string
    {
        $result = db()->raw('SELECT slug FROM cms_content WHERE id = ?', [$id]);
        return $result[0]['slug'] ?? null;
    }
}

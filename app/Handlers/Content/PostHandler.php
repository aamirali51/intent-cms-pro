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
            $totalRaw = $countResult[0]['total'] ?? 0;
            $total = is_numeric($totalRaw) ? (int) $totalRaw : 0;

            return $res->json([
                'data' => $posts,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'last_page' => (int) ceil($total / $limit)
                ]
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to fetch posts: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @param array<string, string> $params
     */
    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            $result = db()->raw('SELECT * FROM cms_content WHERE id = ? AND type = ?', [$id, 'post']);
            
            if (empty($result)) {
                return $res->json(['error' => 'Post not found'], 404);
            }
            
            $post = $result[0];
            
            // Load tags for this post
            $tags = db()->raw('
                SELECT t.id, t.name, t.slug, t.color 
                FROM cms_tags t 
                INNER JOIN cms_content_tags ct ON t.id = ct.tag_id 
                WHERE ct.content_id = ?
            ', [$id]);
            
            $post['tags'] = $tags;
            
            return $res->json($post);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Database error'], 500);
        }
    }

    public static function store(Request $req, Response $res): Response
    {
        try {
            $data = $req->json();
            
            if (!is_array($data)) {
                return $res->json(['error' => 'Invalid request body'], 400);
            }
            
            // Basic validation
            $title = isset($data['title']) && is_string($data['title']) ? trim($data['title']) : '';
            if (empty($title)) {
                return $res->json(['error' => 'Title is required'], 422);
            }

            $slugInput = isset($data['slug']) && is_string($data['slug']) ? $data['slug'] : '';
            $slug = !empty($slugInput) ? $slugInput : self::slugify($title);
            
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (self::slugExists($slug)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $content = isset($data['content']) ? (is_array($data['content']) ? json_encode($data['content']) : (string) $data['content']) : '';
            $excerpt = isset($data['excerpt']) && is_string($data['excerpt']) ? $data['excerpt'] : '';
            $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : 'draft';
            $featuredImage = isset($data['featured_image']) && is_string($data['featured_image']) ? $data['featured_image'] : null;

            db()->raw(
                'INSERT INTO cms_content (type, title, slug, content, excerpt, status, featured_image, author_id, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
                [
                    'post',
                    $title,
                    $slug,
                    $content,
                    $excerpt,
                    $status,
                    $featuredImage,
                    Auth::id()
                ]
            );

            // Get last insert ID directly from PDO connection
            $id = DB::connection()->lastInsertId();
            
            // Sync tags
            $rawTagIds = isset($data['tag_ids']) && is_array($data['tag_ids']) ? $data['tag_ids'] : [];
            /** @var array<int|string> $tagIds */
            $tagIds = array_values($rawTagIds);
            self::syncTags((int) $id, $tagIds);
            
            return $res->json(['id' => $id, 'message' => 'Post created successfully'], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to create post: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @param array<string, string> $params
     */
    public static function update(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            $data = $req->json();

            if (!is_array($data)) {
                return $res->json(['error' => 'Invalid request body'], 400);
            }

            // Check existence
            $exists = db()->raw('SELECT id FROM cms_content WHERE id = ? AND type = ?', [$id, 'post']);
            if (empty($exists)) {
                return $res->json(['error' => 'Post not found'], 404);
            }

            // Update fields
            $fields = [];
            $values = [];

            if (isset($data['title']) && is_string($data['title'])) {
                $fields[] = 'title = ?';
                $values[] = trim($data['title']);
            }
            
            if (isset($data['slug']) && is_string($data['slug']) && !empty($data['slug'])) {
                $slug = $data['slug'];
                // Check if slug changed and is unique
                $currentSlug = self::getSlugById($id);
                if ($slug !== $currentSlug) {
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
                $content = is_array($data['content']) ? json_encode($data['content']) : (string) $data['content'];
                $values[] = $content;
            }

            if (isset($data['excerpt']) && is_string($data['excerpt'])) {
                $fields[] = 'excerpt = ?';
                $values[] = $data['excerpt'];
            }

            if (isset($data['status']) && is_string($data['status'])) {
                $fields[] = 'status = ?';
                $values[] = $data['status'];
            }

            if (isset($data['featured_image'])) {
                $fields[] = 'featured_image = ?';
                $values[] = is_string($data['featured_image']) ? $data['featured_image'] : null;
            }

            $fields[] = 'updated_at = NOW()';

            if (count($fields) === 1) {
                return $res->json(['message' => 'No changes']);
            }

            $values[] = $id; // For WHERE clause

            db()->raw(
                'UPDATE cms_content SET ' . implode(', ', $fields) . ' WHERE id = ?',
                $values
            );
            
            // Sync tags if provided
            if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
                /** @var array<int|string> $tagIds */
                $tagIds = array_values($data['tag_ids']);
                self::syncTags($id, $tagIds);
            }

            return $res->json(['message' => 'Post updated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update post: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @param array<string, string> $params
     */
    public static function destroy(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
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
        $result = preg_replace('~[^\pL\d]+~u', '-', $text);
        $result = $result !== null ? $result : $text;
        
        $converted = iconv('utf-8', 'us-ascii//TRANSLIT', $result);
        $result = $converted !== false ? $converted : $result;
        
        $result = preg_replace('~[^-\w]+~', '', $result);
        $result = $result !== null ? $result : '';
        
        $result = trim($result, '-');
        
        $result = preg_replace('~-+~', '-', $result);
        $result = $result !== null ? $result : '';
        
        $lowered = strtolower($result);
        return $lowered !== '' ? $lowered : 'post-' . time();
    }

    private static function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $sql = 'SELECT id FROM cms_content WHERE slug = ?';
        $params = [$slug];
        
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        
        $result = db()->raw($sql, $params);
        return !empty($result);
    }
    
    private static function getSlugById(int $id): ?string
    {
        $result = db()->raw('SELECT slug FROM cms_content WHERE id = ?', [$id]);
        if (!empty($result) && isset($result[0]['slug']) && is_string($result[0]['slug'])) {
            return $result[0]['slug'];
        }
        return null;
    }
    
    /**
     * Sync tags for a post
     * 
     * @param int $postId
     * @param array<int|string> $tagIds
     */
    private static function syncTags(int $postId, array $tagIds): void
    {
        // Delete existing tag associations
        db()->raw('DELETE FROM cms_content_tags WHERE content_id = ?', [$postId]);
        
        // Insert new associations
        $validTagIds = array_filter(
            array_map(fn($id): int => is_numeric($id) ? (int) $id : 0, $tagIds),
            fn(int $id): bool => $id > 0
        );
        
        foreach ($validTagIds as $tagId) {
            db()->raw(
                'INSERT INTO cms_content_tags (content_id, tag_id) VALUES (?, ?)',
                [$postId, $tagId]
            );
        }
        
        // Update tag counts
        if (!empty($validTagIds)) {
            $placeholders = implode(',', array_fill(0, count($validTagIds), '?'));
            db()->raw("
                UPDATE cms_tags 
                SET count = (SELECT COUNT(*) FROM cms_content_tags WHERE tag_id = cms_tags.id)
                WHERE id IN ({$placeholders})
            ", $validTagIds);
        }
    }
}


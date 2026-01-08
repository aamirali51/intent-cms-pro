<?php
declare(strict_types=1);

namespace App\Handlers\Content;

use Core\Request;
use Core\Response;
use Core\Auth;
use Core\DB;

class PageHandler
{
    public static function index(Request $req, Response $res): Response
    {
        try {
            $pages = db()->raw(
                'SELECT id, title, slug, status, created_at, updated_at, author_id 
                 FROM cms_content 
                 WHERE type = ? 
                 ORDER BY title ASC', 
                ['page']
            );

            // Filter: Allow plugins to modify pages list
            if (function_exists('apply_filters')) {
                $pages = apply_filters('cms.api.pages', $pages);
            }

            return $res->json($pages);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to fetch pages: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @param array<string, string> $params
     */
    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            $result = db()->raw('SELECT * FROM cms_content WHERE id = ? AND type = ?', [$id, 'page']);
            
            if (empty($result)) {
                return $res->json(['error' => 'Page not found'], 404);
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
                    'page',
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
            
            // Fire hooks
            if (function_exists('do_action')) {
                do_action('cms.page.saved', (int) $id, $data);
                if ($status === 'published') {
                    do_action('cms.page.published', (int) $id, $title);
                }
            }
            
            return $res->json(['id' => $id, 'message' => 'Page created successfully'], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to create page: ' . $e->getMessage()], 500);
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
            $exists = db()->raw('SELECT id FROM cms_content WHERE id = ? AND type = ?', [$id, 'page']);
            if (empty($exists)) {
                return $res->json(['error' => 'Page not found'], 404);
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

            // Fire hooks
            if (function_exists('do_action')) {
                do_action('cms.page.saved', $id, $data);
                $statusValue = isset($data['status']) && is_string($data['status']) ? $data['status'] : '';
                if ($statusValue === 'published') {
                    $titleValue = isset($data['title']) && is_string($data['title']) ? $data['title'] : '';
                    do_action('cms.page.published', $id, $titleValue);
                }
            }

            return $res->json(['message' => 'Page updated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update page: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @param array<string, string> $params
     */
    public static function destroy(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            // Check existence and get data for hook
            $existing = db()->raw('SELECT * FROM cms_content WHERE id = ? AND type = ?', [$id, 'page']);
            if (empty($existing)) {
                return $res->json(['error' => 'Page not found'], 404);
            }

            db()->raw('DELETE FROM cms_content WHERE id = ?', [$id]);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.page.deleted', $id, $existing[0]);
            }
            
            return $res->json(['success' => true, 'message' => 'Page deleted successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to delete page'], 500);
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
        return $lowered !== '' ? $lowered : 'page-' . time();
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
}


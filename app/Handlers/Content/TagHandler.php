<?php declare(strict_types=1);

namespace App\Handlers\Content;

use Core\Request;
use Core\Response;
use Core\Auth;

/**
 * Tag Management Handler
 * CRUD operations for tags with colored badges and usage counts
 */
class TagHandler
{
    /**
     * List all tags with usage counts
     */
    public static function index(Request $req, Response $res): Response
    {
        try {
            $search = $req->get('search');
            $sortBy = $req->get('sort', 'name');
            $sortDir = $req->get('dir', 'asc');
            
            // Validate sort direction
            $sortDir = is_string($sortDir) && strtolower($sortDir) === 'desc' ? 'DESC' : 'ASC';
            
            // Validate sort column
            $allowedSorts = ['name', 'slug', 'count', 'created_at'];
            $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'name';
            
            $query = "SELECT * FROM cms_tags";
            $params = [];
            
            if (is_string($search) && $search !== '') {
                $query .= " WHERE name LIKE ? OR slug LIKE ?";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $query .= " ORDER BY {$sortBy} {$sortDir}";
            
            $tags = db()->raw($query, $params);
            
            // Apply filter hook
            if (function_exists('apply_filters')) {
                $tags = apply_filters('cms.api.tags', $tags);
            }
            
            return $res->json(['data' => $tags]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load tags: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get single tag with associated posts
     * 
     * @param array<string, string> $params
     */
    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            $tags = db()->raw('SELECT * FROM cms_tags WHERE id = ?', [$id]);
            if (empty($tags)) {
                return $res->json(['error' => 'Tag not found'], 404);
            }
            
            $tag = $tags[0];
            
            // Get posts with this tag
            $posts = db()->raw('
                SELECT c.id, c.title, c.slug, c.status, c.created_at 
                FROM cms_content c
                INNER JOIN cms_content_tags ct ON c.id = ct.content_id
                WHERE ct.tag_id = ?
                ORDER BY c.created_at DESC
            ', [$id]);
            
            $tag['posts'] = $posts;
            
            // Apply filter hook
            if (function_exists('apply_filters')) {
                $tag = apply_filters('cms.api.tag', $tag, $id);
            }
            
            return $res->json($tag);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load tag'], 500);
        }
    }

    /**
     * Create new tag
     */
    public static function store(Request $req, Response $res): Response
    {
        try {
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            
            // Validate required fields
            $name = isset($data['name']) && is_string($data['name']) ? trim($data['name']) : '';
            if ($name === '') {
                return $res->json(['error' => 'Tag name is required'], 400);
            }
            
            // Generate slug if not provided
            $slug = isset($data['slug']) && is_string($data['slug']) && $data['slug'] !== '' 
                ? self::slugify($data['slug']) 
                : self::slugify($name);
            
            // Check for duplicate slug
            $existing = db()->raw('SELECT id FROM cms_tags WHERE slug = ?', [$slug]);
            if (!empty($existing)) {
                return $res->json(['error' => 'A tag with this slug already exists'], 400);
            }
            
            // Get optional fields
            $color = isset($data['color']) && is_string($data['color']) ? $data['color'] : '#6b7280';
            $description = isset($data['description']) && is_string($data['description']) ? $data['description'] : null;
            
            $now = date('Y-m-d H:i:s');
            
            db()->raw('
                INSERT INTO cms_tags (name, slug, color, description, count, created_at, updated_at) 
                VALUES (?, ?, ?, ?, 0, ?, ?)
            ', [$name, $slug, $color, $description, $now, $now]);
            
            $result = db()->raw('SELECT LAST_INSERT_ID() as id');
            $idValue = isset($result[0]['id']) && is_numeric($result[0]['id']) ? $result[0]['id'] : 0;
            $id = (int) $idValue;
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.tag.created', $id, [
                    'name' => $name,
                    'slug' => $slug,
                    'color' => $color,
                    'description' => $description
                ]);
            }
            
            return $res->json([
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'color' => $color,
                'message' => 'Tag created successfully'
            ], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to create tag: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update tag
     * 
     * @param array<string, string> $params
     */
    public static function update(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            // Check tag exists
            $existing = db()->raw('SELECT * FROM cms_tags WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'Tag not found'], 404);
            }
            
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            $oldData = $existing[0];
            
            // Build update fields
            $fields = [];
            $values = [];
            
            if (isset($data['name']) && is_string($data['name']) && trim($data['name']) !== '') {
                $fields[] = 'name = ?';
                $values[] = trim($data['name']);
            }
            
            if (isset($data['slug']) && is_string($data['slug']) && $data['slug'] !== '') {
                $newSlug = self::slugify($data['slug']);
                // Check for duplicate slug (excluding current tag)
                $slugCheck = db()->raw('SELECT id FROM cms_tags WHERE slug = ? AND id != ?', [$newSlug, $id]);
                if (!empty($slugCheck)) {
                    return $res->json(['error' => 'A tag with this slug already exists'], 400);
                }
                $fields[] = 'slug = ?';
                $values[] = $newSlug;
            }
            
            if (isset($data['color']) && is_string($data['color'])) {
                $fields[] = 'color = ?';
                $values[] = $data['color'];
            }
            
            if (array_key_exists('description', $data)) {
                $fields[] = 'description = ?';
                $values[] = is_string($data['description']) ? $data['description'] : null;
            }
            
            if (empty($fields)) {
                return $res->json(['error' => 'No valid fields to update'], 400);
            }
            
            $fields[] = 'updated_at = ?';
            $values[] = date('Y-m-d H:i:s');
            $values[] = $id;
            
            db()->raw('UPDATE cms_tags SET ' . implode(', ', $fields) . ' WHERE id = ?', $values);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.tag.updated', $id, $data, $oldData);
            }
            
            return $res->json(['success' => true, 'message' => 'Tag updated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update tag: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete tag
     * 
     * @param array<string, string> $params
     */
    public static function destroy(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            // Check tag exists
            $existing = db()->raw('SELECT * FROM cms_tags WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'Tag not found'], 404);
            }
            
            $tagData = $existing[0];
            
            // Delete tag (cascade will remove content_tags entries)
            db()->raw('DELETE FROM cms_tags WHERE id = ?', [$id]);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.tag.deleted', $id, $tagData);
            }
            
            return $res->json(['success' => true, 'message' => 'Tag deleted successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to delete tag'], 500);
        }
    }

    /**
     * Bulk delete tags
     */
    public static function bulkDelete(Request $req, Response $res): Response
    {
        try {
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            $ids = isset($data['ids']) && is_array($data['ids']) ? $data['ids'] : [];
            
            if (empty($ids)) {
                return $res->json(['error' => 'No tag IDs provided'], 400);
            }
            
            // Filter to only integers
            $ids = array_filter(
                array_map(fn($val): int => is_numeric($val) ? (int) $val : 0, $ids), 
                fn(int $id): bool => $id > 0
            );
            
            if (empty($ids)) {
                return $res->json(['error' => 'No valid tag IDs provided'], 400);
            }
            
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            db()->raw("DELETE FROM cms_tags WHERE id IN ({$placeholders})", $ids);
            
            return $res->json([
                'success' => true,
                'message' => count($ids) . ' tag(s) deleted successfully'
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to delete tags'], 500);
        }
    }

    /**
     * Merge multiple tags into one
     */
    public static function merge(Request $req, Response $res): Response
    {
        try {
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            $sourceIds = isset($data['source_ids']) && is_array($data['source_ids']) ? $data['source_ids'] : [];
            $targetId = isset($data['target_id']) && is_numeric($data['target_id']) ? (int) $data['target_id'] : 0;
            
            if (empty($sourceIds) || $targetId === 0) {
                return $res->json(['error' => 'Source tag IDs and target tag ID are required'], 400);
            }
            
            // Check target exists
            $target = db()->raw('SELECT * FROM cms_tags WHERE id = ?', [$targetId]);
            if (empty($target)) {
                return $res->json(['error' => 'Target tag not found'], 404);
            }
            
            // Filter source IDs (exclude target)
            $sourceIds = array_filter(
                array_map(fn($val): int => is_numeric($val) ? (int) $val : 0, $sourceIds), 
                fn(int $id): bool => $id > 0 && $id !== $targetId
            );
            
            if (empty($sourceIds)) {
                return $res->json(['error' => 'No valid source tags to merge'], 400);
            }
            
            $placeholders = implode(',', array_fill(0, count($sourceIds), '?'));
            
            // Move all content associations to target tag
            db()->raw("
                INSERT IGNORE INTO cms_content_tags (content_id, tag_id)
                SELECT content_id, ? FROM cms_content_tags WHERE tag_id IN ({$placeholders})
            ", array_merge([$targetId], $sourceIds));
            
            // Delete source tags (cascade removes old associations)
            db()->raw("DELETE FROM cms_tags WHERE id IN ({$placeholders})", $sourceIds);
            
            // Update target tag count
            self::updateTagCount($targetId);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.tag.merged', $targetId, $sourceIds);
            }
            
            return $res->json([
                'success' => true,
                'message' => count($sourceIds) . ' tag(s) merged into target tag'
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to merge tags: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update tag usage count
     */
    public static function updateTagCount(int $tagId): void
    {
        db()->raw('
            UPDATE cms_tags 
            SET count = (SELECT COUNT(*) FROM cms_content_tags WHERE tag_id = ?)
            WHERE id = ?
        ', [$tagId, $tagId]);
    }

    /**
     * Update all tag counts
     */
    public static function updateAllCounts(): void
    {
        db()->raw('
            UPDATE cms_tags t
            SET t.count = (SELECT COUNT(*) FROM cms_content_tags ct WHERE ct.tag_id = t.id)
        ');
    }

    /**
     * Create URL-friendly slug from string
     */
    private static function slugify(string $text): string
    {
        // Convert to lowercase
        $text = strtolower($text);
        // Replace non-alphanumeric characters with hyphens
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);
        // Remove leading/trailing hyphens
        $text = trim($text ?? '', '-');
        // Replace multiple consecutive hyphens with single
        $text = preg_replace('/-+/', '-', $text);
        
        return $text ?? '';
    }
}

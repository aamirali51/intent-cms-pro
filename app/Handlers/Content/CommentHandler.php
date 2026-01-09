<?php declare(strict_types=1);

namespace App\Handlers\Content;

use Core\Request;
use Core\Response;
use Core\Auth;

/**
 * Comment Management Handler
 * Threaded comments with moderation workflow (pending/approved/spam/trash)
 */
class CommentHandler
{
    /**
     * List comments with filtering
     */
    public static function index(Request $req, Response $res): Response
    {
        try {
            $status = $req->get('status');
            $contentId = $req->get('content_id');
            $pageVal = $req->get('page');
            $perPageVal = $req->get('per_page');
            $page = is_numeric($pageVal) ? (int) $pageVal : 1;
            $perPage = is_numeric($perPageVal) ? (int) $perPageVal : 20;
            $offset = ($page - 1) * $perPage;
            
            // Build query with filters
            $conditions = [];
            $params = [];
            
            if (is_string($status) && in_array($status, ['pending', 'approved', 'spam', 'trash'], true)) {
                $conditions[] = 'c.status = ?';
                $params[] = $status;
            }
            
            if (is_numeric($contentId)) {
                $conditions[] = 'c.content_id = ?';
                $params[] = (int) $contentId;
            }
            
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            // Get total count
            $countResult = db()->raw("SELECT COUNT(*) as total FROM cms_comments c {$whereClause}", $params);
            $totalItems = isset($countResult[0]['total']) && is_numeric($countResult[0]['total']) 
                ? (int) $countResult[0]['total'] 
                : 0;
            
            // Get comments with post title
            $comments = db()->raw("
                SELECT c.*, 
                       p.title as post_title,
                       p.slug as post_slug,
                       u.name as user_name
                FROM cms_comments c
                LEFT JOIN cms_content p ON c.content_id = p.id
                LEFT JOIN users u ON c.user_id = u.id
                {$whereClause}
                ORDER BY c.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ", $params);
            
            // Get status counts
            $statusCounts = [
                'all' => 0,
                'pending' => 0,
                'approved' => 0,
                'spam' => 0,
                'trash' => 0
            ];
            
            $counts = db()->raw('
                SELECT status, COUNT(*) as count 
                FROM cms_comments 
                GROUP BY status
            ');
            
            $total = 0;
            foreach ($counts as $row) {
                $st = is_string($row['status']) ? $row['status'] : '';
                $cnt = is_numeric($row['count']) ? (int) $row['count'] : 0;
                if (isset($statusCounts[$st])) {
                    $statusCounts[$st] = $cnt;
                }
                $total += $cnt;
            }
            $statusCounts['all'] = $total;
            
            // Apply filter hook
            if (function_exists('apply_filters')) {
                $comments = apply_filters('cms.api.comments', $comments);
            }
            
            return $res->json([
                'data' => $comments,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $totalItems,
                    'pages' => (int) ceil($totalItems / $perPage)
                ],
                'counts' => $statusCounts
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load comments: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get single comment
     * 
     * @param array<string, string> $params
     */
    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            $comments = db()->raw('
                SELECT c.*, p.title as post_title 
                FROM cms_comments c
                LEFT JOIN cms_content p ON c.content_id = p.id
                WHERE c.id = ?
            ', [$id]);
            
            if (empty($comments)) {
                return $res->json(['error' => 'Comment not found'], 404);
            }
            
            return $res->json($comments[0]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load comment'], 500);
        }
    }

    /**
     * Create new comment (for frontend submission)
     */
    public static function store(Request $req, Response $res): Response
    {
        try {
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            
            // Validate required fields
            $contentId = isset($data['content_id']) && is_numeric($data['content_id']) ? (int) $data['content_id'] : 0;
            $content = isset($data['content']) && is_string($data['content']) ? trim($data['content']) : '';
            
            if ($contentId === 0) {
                return $res->json(['error' => 'Content ID is required'], 400);
            }
            
            if ($content === '') {
                return $res->json(['error' => 'Comment content is required'], 400);
            }
            
            // Check if post exists
            $post = db()->raw('SELECT id FROM cms_content WHERE id = ?', [$contentId]);
            if (empty($post)) {
                return $res->json(['error' => 'Post not found'], 404);
            }
            
            // Get author info - from logged-in user or submitted data
            $user = Auth::user();
            $userId = null;
            $authorName = '';
            $authorEmail = '';
            
            if ($user !== null) {
                $userId = isset($user['id']) && is_numeric($user['id']) ? (int) $user['id'] : null;
                $authorName = isset($user['name']) && is_string($user['name']) ? $user['name'] : 'Anonymous';
                $authorEmail = isset($user['email']) && is_string($user['email']) ? $user['email'] : '';
            } else {
                $authorName = isset($data['author_name']) && is_string($data['author_name']) ? trim($data['author_name']) : '';
                $authorEmail = isset($data['author_email']) && is_string($data['author_email']) ? trim($data['author_email']) : '';
                
                if ($authorName === '' || $authorEmail === '') {
                    return $res->json(['error' => 'Author name and email are required'], 400);
                }
            }
            
            $authorUrl = isset($data['author_url']) && is_string($data['author_url']) ? trim($data['author_url']) : null;
            $parentId = isset($data['parent_id']) && is_numeric($data['parent_id']) ? (int) $data['parent_id'] : null;
            $authorIp = $_SERVER['REMOTE_ADDR'] ?? null;
            
            // Default status: pending (unless logged-in admin)
            $status = 'pending';
            if ($user !== null && isset($user['role']) && $user['role'] === 'admin') {
                $status = 'approved';
            }
            
            $now = date('Y-m-d H:i:s');
            
            db()->raw('
                INSERT INTO cms_comments 
                (content_id, parent_id, author_name, author_email, author_url, author_ip, content, status, user_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ', [$contentId, $parentId, $authorName, $authorEmail, $authorUrl, $authorIp, $content, $status, $userId, $now, $now]);
            
            $result = db()->raw('SELECT LAST_INSERT_ID() as id');
            $idValue = isset($result[0]['id']) && is_numeric($result[0]['id']) ? $result[0]['id'] : 0;
            $id = (int) $idValue;
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.comment.created', $id, [
                    'content_id' => $contentId,
                    'author_name' => $authorName,
                    'status' => $status
                ]);
            }
            
            return $res->json([
                'id' => $id,
                'status' => $status,
                'message' => $status === 'approved' 
                    ? 'Comment posted successfully!' 
                    : 'Comment submitted and awaiting moderation.'
            ], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to submit comment: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update comment content
     * 
     * @param array<string, string> $params
     */
    public static function update(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            $existing = db()->raw('SELECT * FROM cms_comments WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'Comment not found'], 404);
            }
            
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            
            $content = isset($data['content']) && is_string($data['content']) ? trim($data['content']) : null;
            
            if ($content === null || $content === '') {
                return $res->json(['error' => 'Comment content is required'], 400);
            }
            
            $now = date('Y-m-d H:i:s');
            db()->raw('UPDATE cms_comments SET content = ?, updated_at = ? WHERE id = ?', [$content, $now, $id]);
            
            return $res->json(['success' => true, 'message' => 'Comment updated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update comment'], 500);
        }
    }

    /**
     * Update comment status (moderation)
     * 
     * @param array<string, string> $params
     */
    public static function updateStatus(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            $existing = db()->raw('SELECT * FROM cms_comments WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'Comment not found'], 404);
            }
            
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            
            $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : '';
            
            if (!in_array($status, ['pending', 'approved', 'spam', 'trash'], true)) {
                return $res->json(['error' => 'Invalid status. Must be: pending, approved, spam, or trash'], 400);
            }
            
            $now = date('Y-m-d H:i:s');
            db()->raw('UPDATE cms_comments SET status = ?, updated_at = ? WHERE id = ?', [$status, $now, $id]);
            
            // Fire status-specific hooks
            if (function_exists('do_action')) {
                $oldStatus = is_string($existing[0]['status']) ? $existing[0]['status'] : '';
                do_action('cms.comment.status_changed', $id, $status, $oldStatus);
                
                if ($status === 'approved') {
                    do_action('cms.comment.approved', $id);
                } elseif ($status === 'spam') {
                    do_action('cms.comment.spam', $id);
                }
            }
            
            return $res->json([
                'success' => true, 
                'message' => 'Comment marked as ' . $status
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update comment status'], 500);
        }
    }

    /**
     * Delete comment (permanently)
     * 
     * @param array<string, string> $params
     */
    public static function destroy(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            $existing = db()->raw('SELECT * FROM cms_comments WHERE id = ?', [$id]);
            if (empty($existing)) {
                return $res->json(['error' => 'Comment not found'], 404);
            }
            
            $commentData = $existing[0];
            
            // Delete comment (also deletes child comments via CASCADE)
            db()->raw('DELETE FROM cms_comments WHERE id = ?', [$id]);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.comment.deleted', $id, $commentData);
            }
            
            return $res->json(['success' => true, 'message' => 'Comment deleted successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to delete comment'], 500);
        }
    }

    /**
     * Bulk action on multiple comments
     */
    public static function bulkAction(Request $req, Response $res): Response
    {
        try {
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            
            $ids = isset($data['ids']) && is_array($data['ids']) ? $data['ids'] : [];
            $action = isset($data['action']) && is_string($data['action']) ? $data['action'] : '';
            
            if (empty($ids)) {
                return $res->json(['error' => 'No comment IDs provided'], 400);
            }
            
            // Filter to only integers
            $ids = array_filter(
                array_map(fn($val): int => is_numeric($val) ? (int) $val : 0, $ids), 
                fn(int $id): bool => $id > 0
            );
            
            if (empty($ids)) {
                return $res->json(['error' => 'No valid comment IDs provided'], 400);
            }
            
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $count = count($ids);
            
            switch ($action) {
                case 'approve':
                    db()->raw("UPDATE cms_comments SET status = 'approved', updated_at = NOW() WHERE id IN ({$placeholders})", $ids);
                    return $res->json(['success' => true, 'message' => "{$count} comment(s) approved"]);
                    
                case 'spam':
                    db()->raw("UPDATE cms_comments SET status = 'spam', updated_at = NOW() WHERE id IN ({$placeholders})", $ids);
                    return $res->json(['success' => true, 'message' => "{$count} comment(s) marked as spam"]);
                    
                case 'trash':
                    db()->raw("UPDATE cms_comments SET status = 'trash', updated_at = NOW() WHERE id IN ({$placeholders})", $ids);
                    return $res->json(['success' => true, 'message' => "{$count} comment(s) moved to trash"]);
                    
                case 'delete':
                    db()->raw("DELETE FROM cms_comments WHERE id IN ({$placeholders})", $ids);
                    return $res->json(['success' => true, 'message' => "{$count} comment(s) permanently deleted"]);
                    
                case 'restore':
                    db()->raw("UPDATE cms_comments SET status = 'pending', updated_at = NOW() WHERE id IN ({$placeholders})", $ids);
                    return $res->json(['success' => true, 'message' => "{$count} comment(s) restored to pending"]);
                    
                default:
                    return $res->json(['error' => 'Invalid action. Must be: approve, spam, trash, delete, or restore'], 400);
            }
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to perform bulk action: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get comments for a specific post
     * 
     * @param array<string, string> $params
     */
    public static function forPost(Request $req, Response $res, array $params): Response
    {
        try {
            $postId = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            // For public API, only show approved comments
            $showAll = $req->get('show_all') === 'true' && Auth::check();
            $statusFilter = $showAll ? '' : "AND c.status = 'approved'";
            
            $comments = db()->raw("
                SELECT c.id, c.parent_id, c.author_name, c.content, c.created_at, c.status,
                       u.name as user_name, u.avatar as user_avatar
                FROM cms_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.content_id = ? {$statusFilter}
                ORDER BY c.created_at ASC
            ", [$postId]);
            
            // Build threaded structure
            $threaded = self::buildCommentTree($comments);
            
            return $res->json([
                'post_id' => $postId,
                'count' => count($comments),
                'comments' => $threaded
            ]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load comments'], 500);
        }
    }

    /**
     * Admin reply to a comment
     * 
     * @param array<string, string> $params
     */
    public static function reply(Request $req, Response $res, array $params): Response
    {
        try {
            $parentId = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            // Get parent comment
            $parent = db()->raw('SELECT * FROM cms_comments WHERE id = ?', [$parentId]);
            if (empty($parent)) {
                return $res->json(['error' => 'Parent comment not found'], 404);
            }
            
            $jsonData = $req->json();
            $data = is_array($jsonData) ? $jsonData : [];
            
            $content = isset($data['content']) && is_string($data['content']) ? trim($data['content']) : '';
            
            if ($content === '') {
                return $res->json(['error' => 'Reply content is required'], 400);
            }
            
            $user = Auth::user();
            if ($user === null) {
                return $res->json(['error' => 'You must be logged in to reply'], 401);
            }
            
            $userId = isset($user['id']) && is_numeric($user['id']) ? (int) $user['id'] : null;
            $authorName = isset($user['name']) && is_string($user['name']) ? $user['name'] : 'Admin';
            $authorEmail = isset($user['email']) && is_string($user['email']) ? $user['email'] : '';
            $contentId = isset($parent[0]['content_id']) && is_numeric($parent[0]['content_id']) ? (int) $parent[0]['content_id'] : 0;
            
            $now = date('Y-m-d H:i:s');
            
            db()->raw('
                INSERT INTO cms_comments 
                (content_id, parent_id, author_name, author_email, content, status, user_id, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, \'approved\', ?, ?, ?)
            ', [$contentId, $parentId, $authorName, $authorEmail, $content, $userId, $now, $now]);
            
            $result = db()->raw('SELECT LAST_INSERT_ID() as id');
            $idValue = isset($result[0]['id']) && is_numeric($result[0]['id']) ? $result[0]['id'] : 0;
            $id = (int) $idValue;
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.comment.reply', $id, $parentId, $userId);
            }
            
            return $res->json([
                'id' => $id,
                'message' => 'Reply posted successfully!'
            ], 201);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to post reply: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Build threaded comment tree from flat array
     * 
     * @param array<int, array<string, mixed>> $comments
     * @return array<int, array<string, mixed>>
     */
    private static function buildCommentTree(array $comments): array
    {
        $tree = [];
        $childrenMap = [];
        
        // First pass: group children by parent_id
        foreach ($comments as $comment) {
            $parentId = isset($comment['parent_id']) && is_numeric($comment['parent_id']) ? (int) $comment['parent_id'] : 0;
            
            if ($parentId === 0) {
                $tree[] = $comment;
            } else {
                if (!isset($childrenMap[$parentId])) {
                    $childrenMap[$parentId] = [];
                }
                $childrenMap[$parentId][] = $comment;
            }
        }
        
        // Second pass: attach children to parents
        foreach ($tree as &$comment) {
            $commentId = isset($comment['id']) && is_numeric($comment['id']) ? (int) $comment['id'] : 0;
            if (isset($childrenMap[$commentId])) {
                $comment['replies'] = $childrenMap[$commentId];
            }
        }
        
        return $tree;
    }
}

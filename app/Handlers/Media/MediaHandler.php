<?php

declare(strict_types=1);

namespace App\Handlers\Media;

use Core\Request;
use Core\Response;
use Core\Upload;
use Core\Auth;

class MediaHandler
{
    /**
     * List all media files.
     */
    public static function index(Request $req, Response $res): Response
    {
        try {
            $type = $req->get('type');
            $search = $req->get('search');
            $page = max(1, (int) ($req->get('page') ?? 1));
            $limit = max(1, min(100, (int) ($req->get('limit') ?? 50)));
            $offset = ($page - 1) * $limit;
            
            // Base SQL
            $where = "WHERE 1=1";
            $params = [];

            // Folder Filter
            $folderId = $req->get('folder_id');
            if ($folderId !== null) {
                 if ($folderId === 'root' || $folderId === '' || $folderId === 'null') {
                      $where .= " AND folder_id IS NULL";
                 } else {
                      $where .= " AND folder_id = ?";
                      $params[] = $folderId;
                 }
            }

            if (!empty($type) && is_string($type) && $type !== 'all') {
                $where .= " AND mime_type LIKE ?";
                $params[] = $type . '%';
            }

            if (!empty($search) && is_string($search)) {
                $where .= " AND (filename LIKE ? OR alt_text LIKE ?)";
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }

            // Count Query
            $countSql = "SELECT COUNT(*) as total FROM cms_media $where";
            $countParams = $params; // Copy params for count
            $countResult = db()->raw($countSql, $countParams);
            $total = !empty($countResult) ? (int) ($countResult[0]['total'] ?? 0) : 0;
            $lastPage = ceil($total / $limit);

            // Sort parameters
            $sortColumn = $req->get('sort') ?? 'created_at';
            $sortOrder = strtoupper($req->get('order') ?? 'DESC');
            
            // Whitelist allowed sort columns and orders
            $allowedColumns = ['created_at', 'filename', 'size'];
            $allowedOrders = ['ASC', 'DESC'];
            
            if (!in_array($sortColumn, $allowedColumns, true)) {
                $sortColumn = 'created_at';
            }
            if (!in_array($sortOrder, $allowedOrders, true)) {
                $sortOrder = 'DESC';
            }

            // Fetch Query with dynamic sorting
            // Select mime_type as type for frontend compatibility
            $sql = "SELECT *, mime_type as type FROM cms_media $where ORDER BY $sortColumn $sortOrder LIMIT $limit OFFSET $offset";
            
            // Execute
            $files = db()->raw($sql, $params);
            
            return $res->json([
                'data' => $files,
                'meta' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'last_page' => $lastPage
                ]
            ]);
        } catch (\Throwable $e) {
            error_log('MediaHandler Index Error: ' . $e->getMessage());
            return $res->json(['error' => 'Failed to fetch media list'], 500);
        }
    }

    /**
     * Handle file upload.
     */
    public static function upload(Request $req, Response $res): Response
    {
        // Safe defaults
        $path = '';

        try {
            // Check auth
            $user = Auth::user();
            if (!$user || !isset($user['id'])) {
                return $res->json(['error' => 'Unauthorized'], 401);
            }

            // Init upload
            Upload::setBasePath(BASE_PATH . '/public/uploads'); // @phpstan-ignore-line BASE_PATH defined
            $file = Upload::file('file');

            // Validate
            $file->allowTypes([
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp', 'image/tiff',
                'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain',
                'application/zip', 'application/x-zip-compressed'
            ]);

            if (!$file->isValid()) {
                return $res->json(['error' => $file->firstError()], 400);
            }

            // Store
            $path = $file->store(date('Y/m'));
            if ($path === false) {
                error_log("MediaHandler: Store failed. Errors: " . implode(', ', $file->errors()));
                return $res->json(['error' => 'Failed to save file to disk'], 500);
            }

            // DB Insert
            $rawUserId = $user['id'] ?? 0;
            $userId = is_numeric($rawUserId) ? (int) $rawUserId : 0;
            
            // Generate Thumbnails
            $thumbs = [];
            $absPath = BASE_PATH . '/public/uploads/' . $path;
            if (file_exists($absPath)) {
                $thumbs = self::generateThumbnails($absPath);
            }

            try {
                db()->table('cms_media')->insert([
                    'filename' => $file->getOriginalName(),
                    'path' => '/uploads/' . $path,
                    'mime_type' => self::detectMimeType($file),
                    'size' => $file->getSize(),
                    'user_id' => $userId,
                    'alt_text' => '',
                    'thumbnails' => !empty($thumbs) ? json_encode($thumbs) : null,
                    'folder_id' => $req->post('folder_id') ?: null,
                ]);
            } catch (\Throwable $e) {
                // If DB fails, try to cleanup file and thumbs
                if (file_exists($absPath)) {
                    @unlink($absPath);
                }
                foreach ($thumbs as $thumbPath) {
                    $thumbAbsPath = BASE_PATH . '/public' . $thumbPath;
                     if (file_exists($thumbAbsPath)) @unlink($thumbAbsPath);
                }
                
                error_log('MediaHandler DB Insert Error: ' . $e->getMessage());
                return $res->json(['error' => 'Database error: ' . $e->getMessage()], 500);
            }

            // Retrieve new record
            $lastId = db()->connection()->lastInsertId();
            if (!$lastId) {
                return $res->json(['error' => 'Upload verified but ID missing'], 500);
            }

            $newFile = db()->table('cms_media')->where('id', $lastId)->first();
            
            // Add compatibility alias
            if ($newFile) {
                $newFile['type'] = $newFile['mime_type'];
                if (isset($newFile['thumbnails']) && is_string($newFile['thumbnails'])) {
                    $newFile['thumbnails'] = json_decode($newFile['thumbnails'], true);
                }
            }
            
            return $res->json($newFile, 201);

        } catch (\Throwable $e) {
            error_log('MediaHandler Critical Upload Error: ' . $e->getMessage());
            return $res->json(['error' => 'Server Error during upload'], 500);
        }
    }

    /**
     * Delete media.
     * 
     * @param array<string, string> $params
     */
    public static function delete(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            
            if ($id <= 0) {
                return $res->json(['error' => 'Invalid ID'], 400);
            }

            $file = db()->table('cms_media')->where('id', $id)->first();
            
            if (!$file) {
                return $res->json(['error' => 'File not found'], 404);
            }

            // Delete from disk
            if (!empty($file['path'])) {
                $diskPath = BASE_PATH . '/public' . $file['path'];
                if (file_exists($diskPath)) {
                    if (unlink($diskPath)) {
                        error_log("MediaHandler: Successfully deleted file at $diskPath");
                    } else {
                        error_log("MediaHandler: Failed to delete file at $diskPath");
                    }
                } else {
                    error_log("MediaHandler: File not found on disk at $diskPath");
                }
            }

            // Delete from DB
            db()->table('cms_media')->where('id', $id)->delete();

            return $res->json(['success' => true]);
        } catch (\Throwable $e) {
            error_log('MediaHandler Delete Error: ' . $e->getMessage());
            return $res->json(['error' => 'Failed to delete file'], 500);
        }
    }

    /**
     * Bulk delete media.
     */
    public static function bulkDelete(Request $req, Response $res): Response
    {
        try {
            $data = $req->json();
            $ids = $data['ids'] ?? [];

            if (!is_array($ids) || empty($ids)) {
                return $res->json(['error' => 'No IDs provided'], 400);
            }

            // Sanitize IDs
            $ids = array_filter(array_map('intval', $ids));
            if (empty($ids)) {
                return $res->json(['error' => 'Invalid IDs'], 400);
            }

            // Get files to delete from disk
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $files = db()->raw("SELECT * FROM cms_media WHERE id IN ($placeholders)", $ids);

            $deletedCount = 0;
            $failedCount = 0;

            foreach ($files as $file) {
                if (!empty($file['path'])) {
                    $diskPath = BASE_PATH . '/public' . $file['path'];
                    if (file_exists($diskPath)) {
                        @unlink($diskPath);
                    }
                }
            }

            // Delete from DB
            db()->raw("DELETE FROM cms_media WHERE id IN ($placeholders)", $ids);

            return $res->json(['success' => true, 'count' => count($files)]);

        } catch (\Throwable $e) {
            error_log('MediaHandler Bulk Delete Error: ' . $e->getMessage());
            return $res->json(['error' => 'Failed to delete files'], 500);
        }
    }

    /**
     * Update media.
     * 
     * @param array<string, string> $params
     */
    public static function update(Request $req, Response $res, array $params): Response
    {
        try {
            $id = isset($params['id']) && is_numeric($params['id']) ? (int) $params['id'] : 0;
            $data = $req->json();

            if (!is_array($data)) {
                 return $res->json(['error' => 'Invalid payload'], 400);
            }
            
            if (isset($data['alt_text']) && is_string($data['alt_text'])) {
                db()->table('cms_media')->where('id', $id)->update([
                    'alt_text' => $data['alt_text']
                ]);
            }

            // Support renaming filename
            if (isset($data['filename']) && is_string($data['filename'])) {
                $newFilename = trim($data['filename']);
                if (!empty($newFilename)) {
                    db()->table('cms_media')->where('id', $id)->update([
                        'filename' => $newFilename
                    ]);
                }
            }

            $file = db()->table('cms_media')->where('id', $id)->first();
            
            // Add compatibility alias
            if ($file) {
                $file['type'] = $file['mime_type'];
            }

            return $res->json($file);

        } catch (\Throwable $e) {
             error_log('MediaHandler Update Error: ' . $e->getMessage());
             return $res->json(['error' => 'Failed to update file'], 500);
        }
    }

    /**
     * Helper to reliably detect mime type, especially for WebP/JPG on Windows.
     */
    private static function detectMimeType(Upload $file): string
    {
        $mime = $file->getMimeType();
        $ext = strtolower(pathinfo($file->getOriginalName(), PATHINFO_EXTENSION));

        // Map of extensions to mime types for fallback
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
        ];

        if (($mime === 'application/octet-stream' || empty($mime)) && isset($map[$ext])) {
            return $map[$ext];
        }
        
        return $mime;
    }

    /**
     * Generate thumbnails for image files.
     * 
     * @param string $fullPath Absolute path to source image
     * @return array<string, string> Map of size name => relative path
     */
    private static function generateThumbnails(string $fullPath): array
    {
        $thumbs = [];
        
        // Get sizes from settings (fall back to defaults)
        $mediumWidth = (int) site_setting('medium_size_w', '300');
        $largeWidth = (int) site_setting('large_size_w', '1024');
        
        // Ensure minimum sizes
        if ($mediumWidth < 100) $mediumWidth = 300;
        if ($largeWidth < 300) $largeWidth = 1024;
        
        $sizes = [
            'small' => $mediumWidth,  // Use medium setting for small thumbs
            'medium' => $largeWidth    // Use large setting for medium thumbs
        ];
        
        if (!extension_loaded('gd') || !file_exists($fullPath)) return [];
        
        $info = getimagesize($fullPath);
        if (!$info) return [];
        
        list($width, $height, $type) = $info;
        
        // Load
        switch ($type) {
            case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($fullPath); break;
            case IMAGETYPE_PNG: $src = imagecreatefrompng($fullPath); break;
            case IMAGETYPE_GIF: $src = imagecreatefromgif($fullPath); break;
            case IMAGETYPE_WEBP: $src = imagecreatefromwebp($fullPath); break;
            default: return [];
        }
        
        if (!$src) return [];

        $dir = dirname($fullPath);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        $filename = pathinfo($fullPath, PATHINFO_FILENAME);

        foreach ($sizes as $name => $targetWidth) {
            if ($width <= $targetWidth) continue;
            
            $targetHeight = (int) floor($height * ($targetWidth / $width));
            $dst = imagecreatetruecolor($targetWidth, $targetHeight);
            
            // Transparency
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP || $type == IMAGETYPE_GIF) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                if ($type == IMAGETYPE_GIF) {
                     // GIF resize often tricky with frames, simple resize only gets first frame
                     // For CMS thumbs this is usually acceptable
                }
            }
            
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
            
            $thumbName = "{$filename}_{$name}.{$ext}";
            $thumbPath = "{$dir}/{$thumbName}";
            
            // Save
            switch ($type) {
                case IMAGETYPE_JPEG: imagejpeg($dst, $thumbPath, 85); break;
                case IMAGETYPE_PNG: imagepng($dst, $thumbPath); break;
                case IMAGETYPE_GIF: imagegif($dst, $thumbPath); break;
                case IMAGETYPE_WEBP: imagewebp($dst, $thumbPath, 85); break;
            }
            
            imagedestroy($dst);
            
            // Store relative path (convert abs path back to relative URI)
            // Assumes $fullPath is inside BASE_PATH . '/public'
            $rel = str_replace(BASE_PATH . '/public', '', $thumbPath);
            $rel = str_replace('\\', '/', $rel); // normalize win paths
            $thumbs[$name] = $rel;
        }

        imagedestroy($src);
        
        return $thumbs;
    }
    /**
     * Get Folders
     */
    public static function getFolders(Request $req, Response $res): Response
    {
        error_log('MediaHandler::getFolders called');
        $id = $req->get('id');
        $parentId = $req->get('parent_id');
        
        $where = "WHERE parent_id IS NULL";
        $params = [];
        
        if ($id) {
            $where = "WHERE id = ?";
            $params = [$id];
        } elseif ($parentId !== null && $parentId !== 'root' && $parentId !== '' && $parentId !== 'null') {
            $where = "WHERE parent_id = ?";
            $params = [$parentId];
        } else {
             $where = "WHERE parent_id IS NULL";
        }
        
        try {
             $folders = db()->raw("SELECT * FROM cms_media_folders $where ORDER BY name ASC", $params);
             return $res->json($folders);
        } catch (\Throwable $e) {
             error_log('MediaHandler::getFolders Error: ' . $e->getMessage());
             return $res->json([]);
        }
    }

    /**
     * Create Folder
     */
    public static function createFolder(Request $req, Response $res): Response
    {
        $data = $req->json() ?? [];
        $name = trim($data['name'] ?? '');
        $parentId = $data['parent_id'] ?? null;
        if ($parentId === 'null' || $parentId === '') $parentId = null;

        if (empty($name)) return $res->json(['error' => 'Folder name is required'], 400);

        try {
            db()->table('cms_media_folders')->insert([
                'name' => $name,
                'parent_id' => $parentId
            ]);
            return $res->json(['success' => true]);
        } catch (\Throwable $e) {
            error_log('MediaHandler::createFolder Error: ' . $e->getMessage());
            return $res->json(['error' => 'Failed to create folder: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete Folder
     */
    public static function deleteFolder(Request $req, Response $res, array $params): Response
    {
        $id = $params['id'] ?? null;
        if (!$id) return $res->json(['error' => 'ID required'], 400);

        try {
           db()->table('cms_media_folders')->delete($id);
           return $res->json(['success' => true]);
        } catch (\Throwable $e) {
            error_log('MediaHandler::deleteFolder Error: ' . $e->getMessage());
            return $res->json(['error' => 'Failed to delete folder'], 500);
        }
    }

    /**
     * Move Files
     */
    public static function moveFiles(Request $req, Response $res): Response
    {
        $data = $req->json() ?? [];
        $ids = $data['ids'] ?? [];
        $folderId = $data['folder_id'] ?? null;
        
        if (empty($ids) || !is_array($ids)) return $res->json(['error' => 'No files selected'], 400);
        
        if ($folderId === 'root' || $folderId === '' || $folderId === 'null') $folderId = null;

        try {
             $placeholders = implode(',', array_fill(0, count($ids), '?'));
             $params = array_merge([$folderId], $ids);
             
             db()->raw("UPDATE cms_media SET folder_id = ? WHERE id IN ($placeholders)", $params);
             return $res->json(['success' => true]);
        } catch (\Throwable $e) {
             error_log('MediaHandler::moveFiles Error: ' . $e->getMessage());
             return $res->json(['error' => 'Move failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update Folder (Rename)
     */
    public static function updateFolder(Request $req, Response $res, array $params): Response
    {
        $id = $params['id'] ?? null;
        $data = $req->json() ?? [];
        $name = trim($data['name'] ?? '');

        if (!$id) return $res->json(['error' => 'ID required'], 400);
        if (empty($name)) return $res->json(['error' => 'Name required'], 400);

        try {
             db()->table('cms_media_folders')->where('id', $id)->update(['name' => $name]);
             return $res->json(['success' => true]);
        } catch (\Throwable $e) {
             error_log('MediaHandler::updateFolder Error: ' . $e->getMessage());
             return $res->json(['error' => 'Update failed: ' . $e->getMessage()], 500);
        }
    }
}

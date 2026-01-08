<?php

declare(strict_types=1);

namespace App\Handlers\Settings;

use Core\Request;
use Core\Response;
use ZipArchive;

/**
 * Plugin Upload Handler
 * 
 * Handles plugin ZIP file uploads and extraction.
 */
class PluginUploadHandler
{
    private string $pluginsDir;

    public function __construct()
    {
        $this->pluginsDir = dirname(__DIR__, 2) . '/plugins';
    }

    /**
     * Handle plugin upload
     */
    public function upload(Request $req, Response $res): Response
    {
        // Check for uploaded file
        $pluginFile = $_FILES['plugin'] ?? null;
        if (!is_array($pluginFile)) {
            return $res->json(['error' => 'No file uploaded'], 400);
        }
        
        $errorCode = isset($pluginFile['error']) && is_int($pluginFile['error']) ? $pluginFile['error'] : UPLOAD_ERR_NO_FILE;
        if ($errorCode !== UPLOAD_ERR_OK) {
            return $res->json(['error' => 'Upload error'], 400);
        }

        $filename = isset($pluginFile['name']) && is_string($pluginFile['name']) ? $pluginFile['name'] : '';
        $tmpPath = isset($pluginFile['tmp_name']) && is_string($pluginFile['tmp_name']) ? $pluginFile['tmp_name'] : '';
        $fileSize = isset($pluginFile['size']) && is_int($pluginFile['size']) ? $pluginFile['size'] : 0;

        if ($filename === '' || $tmpPath === '') {
            return $res->json(['error' => 'Invalid file upload'], 400);
        }

        // Validate ZIP file
        if (!str_ends_with(strtolower($filename), '.zip')) {
            return $res->json(['error' => 'Only ZIP files are allowed'], 400);
        }

        // Check file size (max 10MB)
        if ($fileSize > 10 * 1024 * 1024) {
            return $res->json(['error' => 'File too large. Maximum 10MB allowed'], 400);
        }

        // Open ZIP file
        $zip = new ZipArchive();
        if ($zip->open($tmpPath) !== true) {
            return $res->json(['error' => 'Invalid ZIP file'], 400);
        }

        // Find Plugin.php in the archive
        $pluginFileInZip = null;
        $rootFolder = null;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false) {
                continue;
            }
            
            if (preg_match('#^([^/]+)/Plugin\.php$#i', $name, $matches)) {
                $pluginFileInZip = $name;
                $rootFolder = $matches[1];
                break;
            }
            
            $baseName = basename($name);
            if (strcasecmp($baseName, 'Plugin.php') === 0 && substr_count($name, '/') <= 1) {
                $pluginFileInZip = $name;
                $dirName = dirname($name);
                if ($dirName === '.') {
                    // Plugin.php at root - use filename without .zip as folder
                    $pathInfo = pathinfo($filename, PATHINFO_FILENAME);
                    $rootFolder = is_string($pathInfo) ? $pathInfo : 'plugin-' . time();
                } else {
                    $rootFolder = $dirName;
                }
                break;
            }
        }

        if ($pluginFileInZip === null || $rootFolder === null) {
            $zip->close();
            return $res->json(['error' => 'Invalid plugin: Plugin.php not found'], 400);
        }

        // Sanitize folder name
        $pluginId = $this->sanitizeFolderName($rootFolder);
        $targetDir = $this->pluginsDir . '/' . $pluginId;

        // Check if plugin already exists
        if (is_dir($targetDir)) {
            $zip->close();
            return $res->json(['error' => "Plugin '{$pluginId}' already exists. Please deactivate and remove it first."], 400);
        }

        // Create plugins directory if not exists
        if (!is_dir($this->pluginsDir)) {
            mkdir($this->pluginsDir, 0755, true);
        }

        // Extract to target directory
        if (!mkdir($targetDir, 0755, true)) {
            $zip->close();
            return $res->json(['error' => 'Failed to create plugin directory'], 500);
        }

        // Extract files
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false) {
                continue;
            }
            
            // Handle root folder prefix
            $prefix = $rootFolder . '/';
            if (str_starts_with($name, $prefix)) {
                $relativePath = substr($name, strlen($prefix));
            } else {
                $relativePath = $name;
            }

            if ($relativePath === '' || $relativePath === false) {
                continue;
            }

            $targetPath = $targetDir . '/' . $relativePath;

            // Create directories
            if (str_ends_with($name, '/')) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
                continue;
            }

            // Extract file
            $dir = dirname($targetPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $content = $zip->getFromIndex($i);
            if ($content !== false) {
                file_put_contents($targetPath, $content);
            }
        }

        $zip->close();

        // Validate the extracted plugin
        $extractedPluginFile = $targetDir . '/Plugin.php';
        if (!file_exists($extractedPluginFile)) {
            // Try lowercase
            $extractedPluginFile = $targetDir . '/plugin.php';
        }

        if (!file_exists($extractedPluginFile)) {
            // Cleanup on failure
            $this->removeDirectory($targetDir);
            return $res->json(['error' => 'Extraction failed: Plugin.php not found'], 500);
        }

        return $res->json([
            'message' => 'Plugin installed successfully',
            'plugin_id' => $pluginId,
        ]);
    }

    /**
     * Delete a plugin
     * 
     * @param array<string, string> $params
     */
    public function delete(Request $req, Response $res, array $params): Response
    {
        $pluginId = isset($params['id']) && is_string($params['id']) ? $params['id'] : '';

        if (empty($pluginId)) {
            return $res->json(['error' => 'Plugin ID required'], 400);
        }

        // Validate plugin ID (prevent path traversal)
        if (!preg_match('/^[a-z0-9\-_]+$/i', $pluginId)) {
            return $res->json(['error' => 'Invalid plugin ID'], 400);
        }

        $targetDir = $this->pluginsDir . '/' . $pluginId;

        if (!is_dir($targetDir)) {
            return $res->json(['error' => 'Plugin not found'], 404);
        }

        // Check if plugin is active
        $manager = \App\Services\PluginManager::getInstance();
        if ($manager->isActive($pluginId)) {
            return $res->json(['error' => 'Please deactivate the plugin before deleting'], 400);
        }

        // Remove the directory
        if (!$this->removeDirectory($targetDir)) {
            return $res->json(['error' => 'Failed to delete plugin'], 500);
        }

        return $res->json(['message' => 'Plugin deleted successfully']);
    }

    /**
     * Sanitize folder name for plugin ID
     */
    private function sanitizeFolderName(string $name): string
    {
        // Remove extension if present
        $pathResult = pathinfo($name, PATHINFO_FILENAME);
        $name = is_string($pathResult) && $pathResult !== '' ? $pathResult : $name;
        
        // Convert to lowercase, replace spaces/underscores with dashes
        $name = strtolower($name);
        $replaced = preg_replace('/[\s_]+/', '-', $name);
        $name = $replaced !== null ? $replaced : $name;
        $replaced = preg_replace('/[^a-z0-9\-]/', '', $name);
        $name = $replaced !== null ? $replaced : $name;
        $name = trim($name, '-');
        
        return $name !== '' ? $name : 'plugin-' . time();
    }

    /**
     * Recursively remove a directory
     */
    private function removeDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $items = scandir($dir);
        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }
}


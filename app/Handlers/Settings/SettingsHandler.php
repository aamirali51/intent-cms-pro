<?php
declare(strict_types=1);

namespace App\Handlers\Settings;

use Core\Request;
use Core\Response;

class SettingsHandler
{
    /**
     * Get all settings as key-value pairs.
     */
    public static function index(Request $req, Response $res): Response
    {
        try {
            $settings = db()->raw('SELECT `key`, `value` FROM cms_settings ORDER BY `key`');
            
            /** @var array<string, string|null> $result */
            $result = [];
            
            foreach ($settings as $setting) {
                if (is_array($setting) && isset($setting['key']) && is_string($setting['key'])) {
                    $value = $setting['value'] ?? null;
                    $result[$setting['key']] = is_string($value) ? $value : null;
                }
            }
            
            return $res->json($result);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load settings: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get a single setting value.
     *
     * @param array<string, string> $params Route parameters
     */
    public static function show(Request $req, Response $res, array $params): Response
    {
        try {
            $key = $params['key'] ?? '';
            
            if (empty($key)) {
                return $res->json(['error' => 'Setting key is required'], 400);
            }
            
            $result = db()->raw('SELECT `value` FROM cms_settings WHERE `key` = ?', [$key]);
            
            if (empty($result)) {
                return $res->json(['error' => 'Setting not found'], 404);
            }
            
            $value = $result[0]['value'] ?? null;
            
            return $res->json(['key' => $key, 'value' => $value]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to load setting'], 500);
        }
    }

    /**
     * Bulk update settings.
     * Expects JSON body: { "key1": "value1", "key2": "value2", ... }
     */
    public static function update(Request $req, Response $res): Response
    {
        try {
            $data = $req->json();
            
            if (!is_array($data) || empty($data)) {
                return $res->json(['error' => 'No settings provided'], 400);
            }
            
            $updated = 0;
            
            foreach ($data as $key => $value) {
                if (!is_string($key) || $key === '') {
                    continue;
                }
                
                // Sanitize value - convert non-strings to string
                $valueStr = is_scalar($value) ? (string) $value : '';
                
                // Check if setting exists
                $exists = db()->raw('SELECT id FROM cms_settings WHERE `key` = ?', [$key]);
                
                if (!empty($exists)) {
                    // Update existing
                    db()->raw(
                        'UPDATE cms_settings SET `value` = ?, updated_at = NOW() WHERE `key` = ?',
                        [$valueStr, $key]
                    );
                } else {
                    // Insert new (only allow known keys in a production system, but for flexibility we allow any)
                    db()->raw(
                        'INSERT INTO cms_settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())',
                        [$key, $valueStr]
                    );
                }
                
                $updated++;
            }
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.settings.saved', $data, $updated);
            }
            
            return $res->json(['message' => "Updated $updated settings", 'count' => $updated]);
        } catch (\Throwable $e) {
            return $res->json(['error' => 'Failed to update settings: ' . $e->getMessage()], 500);
        }
    }
}

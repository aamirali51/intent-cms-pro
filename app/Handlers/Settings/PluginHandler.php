<?php

declare(strict_types=1);

namespace App\Handlers\Settings;

use App\Services\PluginManager;
use Core\Request;
use Core\Response;

/**
 * Plugin Handler
 * 
 * API endpoints for plugin management.
 */
class PluginHandler
{
    /**
     * List all plugins (active and inactive)
     */
    public function index(Request $req, Response $res): Response
    {
        $manager = PluginManager::getInstance();
        $manager->discoverPlugins();
        
        $plugins = $manager->getAll();
        $output = [];

        foreach ($plugins as $id => $info) {
            $output[] = [
                'id' => $id,
                'name' => $info->metadata->name,
                'version' => $info->metadata->version,
                'author' => $info->metadata->author,
                'author_uri' => $info->metadata->authorUri,
                'description' => $info->metadata->description,
                'icon' => $info->metadata->icon,
                'is_active' => $info->isActive,
                'has_settings' => $info->hasSettings(),
                'error' => $info->error,
                'requires' => $info->metadata->requires,
                'conflicts' => $info->metadata->conflicts,
                'tags' => $info->metadata->tags,
                'min_php' => $info->metadata->minPhpVersion,
                'min_cms' => $info->metadata->minCmsVersion,
            ];
        }

        return $res->json($output);
    }

    /**
     * Get single plugin details
     * 
     * @param array<string, string> $params
     */
    public function show(Request $req, Response $res, array $params): Response
    {
        $id = isset($params['id']) && is_string($params['id']) ? $params['id'] : '';
        $manager = PluginManager::getInstance();
        $info = $manager->get($id);

        if ($info === null) {
            return $res->json(['error' => 'Plugin not found'], 404);
        }

        return $res->json([
            'id' => $id,
            'name' => $info->metadata->name,
            'version' => $info->metadata->version,
            'author' => $info->metadata->author,
            'description' => $info->metadata->description,
            'is_active' => $info->isActive,
            'has_settings' => $info->hasSettings(),
            'settings' => array_map(fn($s) => [
                'key' => $s->key,
                'type' => $s->type,
                'label' => $s->label,
                'description' => $s->description,
                'default' => $s->default,
                'required' => $s->required,
                'options' => $s->options,
                'group' => $s->group,
                'value' => $manager->getSetting($id, $s->key, $s->default),
            ], $info->settings),
        ]);
    }

    /**
     * Activate a plugin
     */
    public function activate(Request $req, Response $res): Response
    {
        $data = $req->json();
        $id = is_array($data) && isset($data['id']) ? (string) $data['id'] : '';

        if (empty($id)) {
            return $res->json(['error' => 'Plugin ID required'], 400);
        }

        try {
            $manager = PluginManager::getInstance();
            $manager->activate($id);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.plugin.activated', $id);
            }
            
            return $res->json(['message' => 'Plugin activated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Deactivate a plugin
     */
    public function deactivate(Request $req, Response $res): Response
    {
        $data = $req->json();
        $id = is_array($data) && isset($data['id']) ? (string) $data['id'] : '';

        if (empty($id)) {
            return $res->json(['error' => 'Plugin ID required'], 400);
        }

        try {
            $manager = PluginManager::getInstance();
            $manager->deactivate($id);
            
            // Fire hook
            if (function_exists('do_action')) {
                do_action('cms.plugin.deactivated', $id);
            }
            
            return $res->json(['message' => 'Plugin deactivated successfully']);
        } catch (\Throwable $e) {
            return $res->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get plugin settings
     * 
     * @param array<string, string> $params
     */
    public function getSettings(Request $req, Response $res, array $params): Response
    {
        $id = isset($params['id']) && is_string($params['id']) ? $params['id'] : '';
        $manager = PluginManager::getInstance();
        $info = $manager->get($id);

        if ($info === null) {
            return $res->json(['error' => 'Plugin not found'], 404);
        }

        $settings = [];
        foreach ($info->settings as $setting) {
            $settings[$setting->key] = [
                'type' => $setting->type,
                'label' => $setting->label ?: $setting->key,
                'description' => $setting->description,
                'default' => $setting->default,
                'required' => $setting->required,
                'options' => $setting->options,
                'group' => $setting->group,
                'value' => $manager->getSetting($id, $setting->key, $setting->default),
            ];
        }

        return $res->json(['settings' => $settings]);
    }

    /**
     * Update plugin settings
     * 
     * @param array<string, string> $params
     */
    public function updateSettings(Request $req, Response $res, array $params): Response
    {
        $id = isset($params['id']) && is_string($params['id']) ? $params['id'] : '';
        $manager = PluginManager::getInstance();
        $info = $manager->get($id);

        if ($info === null) {
            return $res->json(['error' => 'Plugin not found'], 404);
        }

        $data = $req->json();
        if (!is_array($data)) {
            return $res->json(['error' => 'Invalid request body'], 400);
        }

        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $manager->setSetting($id, $key, $value);
            }
        }

        // Fire hook
        if (function_exists('do_action')) {
            do_action('cms.plugin.settings_saved', $id, $data);
        }

        return $res->json(['message' => 'Settings saved']);
    }
}

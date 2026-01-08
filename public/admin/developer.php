<?php
/**
 * Developer Documentation - Hooks & API Reference
 * 
 * In-admin documentation for plugin developers.
 */

// Load autoloader and bootstrap
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/bootstrap.php';

use App\Services\Hooks;
use App\Services\PluginLoader;

$title = 'Developer Documentation';
ob_start();

// Get plugin data
$plugins = PluginLoader::getPlugins();
$registeredHooks = PluginLoader::getRegisteredHooks();
?>

<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Developer Documentation</h1>
        <p class="text-gray-500 dark:text-gray-400">Hook system, API reference, and plugin development guide</p>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
        <nav class="flex gap-6" id="doc-tabs">
            <button class="doc-tab active pb-3 border-b-2 border-primary text-primary font-medium text-sm" data-tab="hooks">
                <span class="material-icons-round text-lg align-middle mr-1">webhook</span>
                Hooks Reference
            </button>
            <button class="doc-tab pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium text-sm" data-tab="api">
                <span class="material-icons-round text-lg align-middle mr-1">api</span>
                REST API
            </button>
            <button class="doc-tab pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium text-sm" data-tab="plugins">
                <span class="material-icons-round text-lg align-middle mr-1">extension</span>
                Plugin Development
            </button>
            <button class="doc-tab pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-medium text-sm" data-tab="status">
                <span class="material-icons-round text-lg align-middle mr-1">insights</span>
                System Status
            </button>
        </nav>
    </div>

    <!-- Hooks Reference Tab -->
    <div id="tab-hooks" class="doc-content">
        <div class="grid gap-6">
            <!-- Actions Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-blue-500">bolt</span>
                        Actions
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Actions perform side effects without returning values</p>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <!-- cms.post.saved -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.post.saved</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a post is created or updated.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.post.saved', function(int $postId, array $data): void {
    // $postId - The saved post ID
    // $data - Array of post data (title, content, status, etc.)
    error_log("Post {$postId} was saved");
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.post.published -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.post.published</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired when a post status changes to 'published'.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.post.published', function(int $postId, string $title): void {
    // Send notification, ping search engines, etc.
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.post.deleted -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.post.deleted</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a post is deleted.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.post.deleted', function(int $postId, array $data): void {
    // Clean up related data, remove from sitemap, etc.
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.init -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.init</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired when CMS is fully initialized. Use for plugin setup.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.init', function(): void {
    // Initialize plugin resources
}, 5);</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-purple-500">filter_alt</span>
                        Filters
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Filters modify and return values through a pipeline</p>
                </div>
                <div class="p-5">
                    <div class="space-y-4">
                        <!-- cms.the_content -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-purple-600">cms.the_content</code>
                                <span class="text-xs text-gray-400">Filter</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Filters post/page content before display.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_filter('cms.the_content', function(string $content, int $postId): string {
    // Modify content - add share buttons, process shortcodes, etc.
    return $content . '&lt;div class="share-buttons"&gt;...&lt;/div&gt;';
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.api.posts -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-purple-600">cms.api.posts</code>
                                <span class="text-xs text-gray-400">Filter</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Filters the posts array in API responses.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_filter('cms.api.posts', function(array $posts): array {
    // Add custom fields, filter sensitive data, etc.
    return array_map(fn($p) => [...$p, 'custom' => 'value'], $posts);
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.post.meta -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-purple-600">cms.post.meta</code>
                                <span class="text-xs text-gray-400">Filter</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Filters post metadata (reading time, categories, etc.).</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_filter('cms.post.meta', function(array $meta, int $postId, string $content): array {
    $meta['reading_time'] = ceil(str_word_count($content) / 200);
    return $meta;
}, 10);</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REST API Tab -->
    <div id="tab-api" class="doc-content hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">REST API v1</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Base URL: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">/api/v1</code></p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <!-- Posts Endpoints -->
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Posts
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/posts</code>
                            <span class="text-gray-400 text-xs">List all posts</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/posts/{id}</code>
                            <span class="text-gray-400 text-xs">Get single post</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded text-xs font-mono">POST</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/posts</code>
                            <span class="text-gray-400 text-xs">Create post</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded text-xs font-mono">PUT</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/posts/{id}</code>
                            <span class="text-gray-400 text-xs">Update post</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded text-xs font-mono">DELETE</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/posts/{id}</code>
                            <span class="text-gray-400 text-xs">Delete post</span>
                        </div>
                    </div>
                </div>

                <!-- Pages Endpoints -->
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Pages
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/pages</code>
                            <span class="text-gray-400 text-xs">List all pages</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/pages/{id}</code>
                            <span class="text-gray-400 text-xs">Get single page</span>
                        </div>
                    </div>
                </div>

                <!-- Media Endpoints -->
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        Media
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/media</code>
                            <span class="text-gray-400 text-xs">List media files</span>
                        </div>
                    </div>
                </div>

                <!-- System Endpoints -->
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                        System
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/system/info</code>
                            <span class="text-gray-400 text-xs">System information</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/v1/users</code>
                            <span class="text-gray-400 text-xs">List users (admin only)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Example Request -->
            <div class="p-5 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Example Request</h3>
                <pre class="text-xs bg-gray-800 text-gray-100 rounded-lg p-4 overflow-x-auto"><code>curl -X GET "/api/v1/posts?limit=10" \
  -H "Accept: application/json" \
  -H "Cookie: PHPSESSID=your_session_id"</code></pre>
            </div>
        </div>
    </div>

    <!-- Plugin Development Tab -->
    <div id="tab-plugins" class="doc-content hidden">
        <div class="grid gap-6">
            <!-- Modern Plugin (Attributes) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-green-500">auto_awesome</span>
                        Modern Plugin (PHP 8 Attributes)
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Recommended approach for new plugins</p>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        Create <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">plugins/my-plugin/Plugin.php</code>:
                    </p>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-100"><code>&lt;?php
declare(strict_types=1);

namespace Plugins\MyPlugin;

use App\Attributes\Plugin;
use App\Attributes\Action;
use App\Attributes\Filter;

#[Plugin(
    name: 'My Awesome Plugin',
    version: '1.0.0',
    author: 'Your Name',
    description: 'Does awesome things'
)]
class MyAwesomePlugin
{
    #[Filter('cms.the_content', priority: 10)]
    public function modifyContent(string $content, int $postId): string
    {
        return $content . '&lt;!-- Modified by My Plugin --&gt;';
    }

    #[Action('cms.post.saved', priority: 20)]
    public function onPostSaved(int $postId, array $data): void
    {
        error_log("Post {$postId} saved!");
    }
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Legacy Plugin (Procedural) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-gray-500">code</span>
                        Legacy Plugin (Procedural)
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Simple approach for quick plugins</p>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                        Create <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">plugins/my-plugin/plugin.php</code>:
                    </p>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-100"><code>&lt;?php
// plugins/my-plugin/plugin.php

add_filter('cms.the_content', function(string $content, int $postId): string {
    return $content . '&lt;!-- Modified --&gt;';
}, 10);

add_action('cms.post.saved', function(int $postId, array $data): void {
    error_log("Post saved: {$postId}");
}, 10);</code></pre>
                    </div>
                </div>
            </div>

            <!-- Plugin Structure -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Plugin Directory Structure</h2>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-700 dark:text-gray-300">
<pre>plugins/
├── my-plugin/
│   ├── Plugin.php      ← Modern (PHP 8 Attributes)
│   ├── plugin.php      ← Legacy (Procedural)
│   ├── assets/
│   │   ├── css/
│   │   └── js/
│   └── src/
│       └── MyService.php
└── another-plugin/
    └── Plugin.php</pre>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Tab -->
    <div id="tab-status" class="doc-content hidden">
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Loaded Plugins -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-primary">extension</span>
                        Loaded Plugins
                    </h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <?php if (empty($plugins)): ?>
                    <div class="p-5 text-center text-gray-500">
                        <span class="material-icons-round text-4xl text-gray-300 mb-2 block">extension_off</span>
                        No attribute-based plugins loaded
                    </div>
                    <?php else: foreach ($plugins as $plugin): ?>
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($plugin['metadata']->name) ?></div>
                            <div class="text-xs text-gray-500">
                                v<?= htmlspecialchars($plugin['metadata']->version) ?>
                                <?php if ($plugin['metadata']->author): ?>
                                    • <?= htmlspecialchars($plugin['metadata']->author) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs">Active</span>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Registered Hooks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-primary">webhook</span>
                        Active Hooks
                    </h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-80 overflow-y-auto">
                    <?php if (empty($registeredHooks)): ?>
                    <div class="p-5 text-center text-gray-500">
                        <span class="material-icons-round text-4xl text-gray-300 mb-2 block">link_off</span>
                        No hooks registered via attributes
                    </div>
                    <?php else: foreach ($registeredHooks as $hook): ?>
                    <div class="p-3 flex items-center justify-between text-sm">
                        <div>
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 px-1 rounded <?= $hook['type'] === 'filter' ? 'text-purple-600' : 'text-blue-600' ?>"><?= htmlspecialchars($hook['hook']) ?></code>
                            <span class="text-xs text-gray-400 ml-2"><?= htmlspecialchars($hook['source']) ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">P:<?= $hook['priority'] ?></span>
                            <span class="px-1.5 py-0.5 rounded text-xs <?= $hook['type'] === 'filter' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' ?>">
                                <?= ucfirst($hook['type']) ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 md:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-primary"><?= PHP_VERSION ?></div>
                        <div class="text-xs text-gray-500">PHP Version</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-primary"><?= count($plugins) ?></div>
                        <div class="text-xs text-gray-500">Plugins Loaded</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-primary"><?= count($registeredHooks) ?></div>
                        <div class="text-xs text-gray-500">Hooks Registered</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="text-2xl font-bold text-primary">v1</div>
                        <div class="text-xs text-gray-500">API Version</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.doc-tab.active {
    border-color: #8b5cf6;
    color: #8b5cf6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.doc-tab');
    const contents = document.querySelectorAll('.doc-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetId = 'tab-' + this.dataset.tab;

            // Update tab styles
            tabs.forEach(t => {
                t.classList.remove('active', 'border-primary', 'text-primary');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.add('active', 'border-primary', 'text-primary');
            this.classList.remove('border-transparent', 'text-gray-500');

            // Show target content
            contents.forEach(c => c.classList.add('hidden'));
            document.getElementById(targetId)?.classList.remove('hidden');
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>

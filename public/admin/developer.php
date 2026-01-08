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

                        <!-- cms.page.saved -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.page.saved</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a page is created or updated.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.page.saved', function(int $pageId, array $data): void {
    error_log("Page {$pageId} was saved");
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.page.deleted -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.page.deleted</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a page is deleted.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.page.deleted', function(int $pageId, array $data): void {
    // $data contains the deleted page's data
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.media.uploaded -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.media.uploaded</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a media file is uploaded.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.media.uploaded', function(int $mediaId, array $file): void {
    // $file contains path, mime_type, filename, etc.
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.media.deleted -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.media.deleted</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a media file is deleted.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.media.deleted', function(int $mediaId, array $file): void {
    // Clean up related data
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.user.login -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.user.login</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after successful user login.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.user.login', function(?array $user): void {
    // Log login, update last_login timestamp
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.user.logout -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.user.logout</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after user logs out.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.user.logout', function(?array $user): void {
    // Clear user cache, log activity
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.user.created -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.user.created</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a new user is created.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.user.created', function(int $userId, array $data): void {
    // $data contains name, email, role
    // Send welcome email, log activity
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.user.updated -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.user.updated</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a user profile is updated.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.user.updated', function(int $userId, array $data): void {
    // Profile changed, clear caches
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.user.deleted -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.user.deleted</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after a user is deleted.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.user.deleted', function(int $userId, array $userData): void {
    // Clean up user data, reassign content
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.settings.saved -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.settings.saved</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired after site settings are saved.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.settings.saved', function(array $data, int $count): void {
    // Clear caches, regenerate files
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.plugin.activated -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.plugin.activated</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired when a plugin is activated.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.plugin.activated', function(string $pluginId): void {
    // Log, send notification
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.plugin.deactivated -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-primary">cms.plugin.deactivated</code>
                                <span class="text-xs text-gray-400">Action</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Fired when a plugin is deactivated.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_action('cms.plugin.deactivated', function(string $pluginId): void {
    // Clean up plugin caches
}, 10);</code></pre>
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

                        <!-- cms.api.pages -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-purple-600">cms.api.pages</code>
                                <span class="text-xs text-gray-400">Filter</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Filters the pages array in API responses.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_filter('cms.api.pages', function(array $pages): array {
    // Add custom fields or filter sensitive data
    return $pages;
}, 10);</code></pre>
                            </div>
                        </div>

                        <!-- cms.api.media -->
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <code class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-purple-600">cms.api.media</code>
                                <span class="text-xs text-gray-400">Filter</span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Filters the media array in API responses.</p>
                            <div class="mt-3 bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                                <pre class="text-xs text-gray-700 dark:text-gray-300 overflow-x-auto"><code>add_filter('cms.api.media', function(array $media): array {
    // Add CDN URLs, watermarks, etc.
    return $media;
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

                <!-- Users Endpoints -->
                <div class="p-5">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        Users
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/users</code>
                            <span class="text-gray-400 text-xs">List all users with roles</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded text-xs font-mono">GET</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/users/{id}</code>
                            <span class="text-gray-400 text-xs">Get single user</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded text-xs font-mono">POST</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/users</code>
                            <span class="text-gray-400 text-xs">Create user (name, email, password, role)</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded text-xs font-mono">PUT</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/users/{id}</code>
                            <span class="text-gray-400 text-xs">Update user</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded text-xs font-mono">DELETE</span>
                            <code class="text-gray-600 dark:text-gray-300">/api/users/{id}</code>
                            <span class="text-gray-400 text-xs">Delete user (cannot delete self)</span>
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
            <!-- Introduction -->
            <div class="bg-gradient-to-r from-primary/5 to-purple-500/5 border border-primary/20 rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">🚀 Attribute-Based Plugin System</h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Intent CMS Pro uses PHP 8 attributes for zero-configuration plugins. Simply annotate your class and methods—no XML, YAML, or JSON config files needed.
                </p>
            </div>

            <!-- Plugin Interface -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-blue-500">integration_instructions</span>
                        PluginInterface & Lifecycle
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Implement <code class="bg-blue-100 dark:bg-blue-900 px-1 rounded">App\Contracts\PluginInterface</code> for lifecycle hooks</p>
                </div>
                <div class="p-5">
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <code class="text-sm font-semibold">boot()</code>
                            </div>
                            <p class="text-xs text-gray-500">Called on every request when plugin is active. Register routes, hooks, services.</p>
                        </div>
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <code class="text-sm font-semibold">activate()</code>
                            </div>
                            <p class="text-xs text-gray-500">Called once when activated. Create tables, set defaults.</p>
                        </div>
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <code class="text-sm font-semibold">deactivate()</code>
                            </div>
                            <p class="text-xs text-gray-500">Called when deactivated. Clean temp state, keep user data.</p>
                        </div>
                        <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                <code class="text-sm font-semibold">uninstall()</code>
                            </div>
                            <p class="text-xs text-gray-500">Called when deleted. Remove all data and tables.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- #[Plugin] Attribute -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-green-500">auto_awesome</span>
                        #[Plugin] Attribute
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Define plugin metadata on your main class</p>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-100"><code>&lt;?php declare(strict_types=1);

namespace Plugins\MyPlugin;

use App\Attributes\Plugin;
use App\Contracts\PluginInterface;

<span class="text-yellow-300">#[Plugin(</span>
    <span class="text-purple-300">name:</span> <span class="text-green-300">'My Plugin'</span>,
    <span class="text-purple-300">version:</span> <span class="text-green-300">'1.0.0'</span>,
    <span class="text-purple-300">description:</span> <span class="text-green-300">'A great plugin'</span>,
    <span class="text-purple-300">author:</span> <span class="text-green-300">'Your Name'</span>,
    <span class="text-purple-300">website:</span> <span class="text-green-300">'https://example.com'</span>,
    <span class="text-purple-300">minPhpVersion:</span> <span class="text-green-300">'8.2'</span>,
    <span class="text-purple-300">minCmsVersion:</span> <span class="text-green-300">'0.8.0'</span>,
    <span class="text-purple-300">tags:</span> [<span class="text-green-300">'seo'</span>, <span class="text-green-300">'analytics'</span>]
<span class="text-yellow-300">)]</span>
class Plugin implements PluginInterface
{
    public function boot(): void { }
    public function activate(): void { }
    public function deactivate(): void { }
    public function uninstall(): void { }
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- #[PluginSetting] Attribute -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-purple-500">tune</span>
                        #[PluginSetting] Attribute
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Auto-generate settings UI—no forms needed!</p>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto mb-4">
                        <pre class="text-xs text-gray-100"><code><span class="text-yellow-300">#[PluginSetting(</span>
    <span class="text-purple-300">name:</span> <span class="text-green-300">'api_key'</span>,
    <span class="text-purple-300">label:</span> <span class="text-green-300">'API Key'</span>,
    <span class="text-purple-300">type:</span> <span class="text-green-300">'text'</span>,
    <span class="text-purple-300">description:</span> <span class="text-green-300">'Your service API key'</span>,
    <span class="text-purple-300">required:</span> <span class="text-blue-300">true</span>,
    <span class="text-purple-300">group:</span> <span class="text-green-300">'API Configuration'</span>
<span class="text-yellow-300">)]</span>
<span class="text-yellow-300">#[PluginSetting(</span>
    <span class="text-purple-300">name:</span> <span class="text-green-300">'enable_tracking'</span>,
    <span class="text-purple-300">label:</span> <span class="text-green-300">'Enable Tracking'</span>,
    <span class="text-purple-300">type:</span> <span class="text-green-300">'boolean'</span>,
    <span class="text-purple-300">default:</span> <span class="text-blue-300">true</span>
<span class="text-yellow-300">)]</span>
class Plugin implements PluginInterface { ... }</code></pre>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">text</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">textarea</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">number</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">boolean</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">select</span>
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">color</span>
                    </div>
                </div>
            </div>

            <!-- #[AdminMenuItem] Attribute -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-orange-500">menu</span>
                        #[AdminMenuItem] Attribute
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add pages to the admin sidebar automatically</p>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-100"><code><span class="text-yellow-300">#[AdminMenuItem(</span>
    <span class="text-purple-300">label:</span> <span class="text-green-300">'Analytics Dashboard'</span>,
    <span class="text-purple-300">route:</span> <span class="text-green-300">'/admin/my-plugin/analytics.php'</span>,
    <span class="text-purple-300">icon:</span> <span class="text-green-300">'insights'</span>,        <span class="text-gray-500">// Material Icons name</span>
    <span class="text-purple-300">position:</span> <span class="text-blue-300">50</span>,              <span class="text-gray-500">// Sort order</span>
    <span class="text-purple-300">badge:</span> <span class="text-green-300">'NEW'</span>              <span class="text-gray-500">// Optional badge text</span>
<span class="text-yellow-300">)]</span>
class Plugin implements PluginInterface { ... }</code></pre>
                    </div>
                </div>
            </div>

            <!-- #[PluginAsset] Attribute -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-cyan-50 to-teal-50 dark:from-cyan-900/20 dark:to-teal-900/20">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-cyan-500">style</span>
                        #[PluginAsset] Attribute
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Register CSS and JavaScript assets</p>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-100"><code><span class="text-yellow-300">#[PluginAsset(</span>
    <span class="text-purple-300">type:</span> <span class="text-green-300">'css'</span>,
    <span class="text-purple-300">path:</span> <span class="text-green-300">'assets/style.css'</span>,
    <span class="text-purple-300">location:</span> <span class="text-green-300">'admin'</span>  <span class="text-gray-500">// 'admin', 'frontend', or 'both'</span>
<span class="text-yellow-300">)]</span>
<span class="text-yellow-300">#[PluginAsset(</span>
    <span class="text-purple-300">type:</span> <span class="text-green-300">'js'</span>,
    <span class="text-purple-300">path:</span> <span class="text-green-300">'assets/script.js'</span>,
    <span class="text-purple-300">defer:</span> <span class="text-blue-300">true</span>,
    <span class="text-purple-300">location:</span> <span class="text-green-300">'frontend'</span>
<span class="text-yellow-300">)]</span>
class Plugin implements PluginInterface { ... }</code></pre>
                    </div>
                </div>
            </div>

            <!-- Plugin Directory Structure -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <span class="material-icons-round text-gray-500">folder</span>
                    Plugin Directory Structure
                </h2>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 font-mono text-sm text-gray-700 dark:text-gray-300">
<pre>plugins/
└── my-awesome-plugin/
    ├── Plugin.php          <span class="text-green-500">← Required entry point</span>
    ├── assets/
    │   ├── style.css       <span class="text-gray-500">← Register with #[PluginAsset]</span>
    │   └── script.js
    └── src/                <span class="text-gray-500">← Optional: Additional classes</span>
        └── MyService.php</pre>
                </div>
            </div>

            <!-- Using Hooks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-icons-round text-primary">webhook</span>
                        Using Actions & Filters
                    </h2>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-xs text-gray-100"><code>public function boot(): void
{
    <span class="text-gray-500">// Register an action</span>
    add_action(<span class="text-green-300">'cms.post.saved'</span>, function(int $id, array $data): void {
        <span class="text-gray-500">// Do something when a post is saved</span>
    }, priority: <span class="text-blue-300">10</span>);

    <span class="text-gray-500">// Register a filter</span>
    add_filter(<span class="text-green-300">'cms.the_content'</span>, function(string $content): string {
        return $content . <span class="text-green-300">'&lt;!-- Plugin was here --&gt;'</span>;
    }, priority: <span class="text-blue-300">10</span>);

    <span class="text-gray-500">// Access your plugin settings</span>
    $apiKey = $this->getSetting(<span class="text-green-300">'api_key'</span>);
}</code></pre>
                    </div>
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

<?php
/**
 * Single Post/Page View
 * Renders any published content based on URL slug
 */
declare(strict_types=1);

// Bootstrap the framework
require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Initialize app for database access
$app = new Core\App();

// Get the current slug from URL
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);
$slug = trim($path, '/');

// Handle /blog prefix
if (str_starts_with($slug, 'blog/')) {
    $slug = substr($slug, 5);
}

// Default to 'home' if empty
if (empty($slug) || $slug === 'blog') {
    $slug = 'home';
}

// Query for the content
$content = db()->table('cms_content')
    ->where('slug', $slug)
    ->where('status', 'published')
    ->first();

// 404 if not found
if (!$content) {
    http_response_code(404);
    $pageTitle = '404 - Page Not Found';
    include __DIR__ . '/header.php';
    ?>
    <main class="flex-1 flex items-center justify-center">
        <div class="text-center py-20">
            <h1 class="text-6xl font-bold text-gray-200 mb-4">404</h1>
            <p class="text-xl text-gray-600 mb-8">Page not found</p>
            <a href="/" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primaryHover text-white font-medium rounded-lg transition-colors">
                ← Back to Home
            </a>
        </div>
    </main>
    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// Set page meta
$pageTitle = htmlspecialchars($content['title'] ?? 'Untitled') . ' - ' . site_title();
$pageDescription = htmlspecialchars($content['excerpt'] ?? '');

// Parse content JSON
$contentData = null;
if (!empty($content['content'])) {
    $contentData = json_decode($content['content'], true);
}

// Get author info
$author = null;
if (!empty($content['author_id'])) {
    $author = db()->table('users')->where('id', $content['author_id'])->first();
}

include __DIR__ . '/header.php';
?>

<main class="flex-1">
    <!-- Hero Section -->
    <?php if (!empty($content['featured_image'])): ?>
    <div class="relative h-96 md:h-[500px] overflow-hidden">
        <img src="<?= htmlspecialchars($content['featured_image']) ?>" 
             alt="<?= htmlspecialchars($content['title'] ?? '') ?>" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4"><?= htmlspecialchars($content['title'] ?? 'Untitled') ?></h1>
                <?php if (!empty($content['excerpt'])): ?>
                <p class="text-lg text-white/80"><?= htmlspecialchars($content['excerpt']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 py-16">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($content['title'] ?? 'Untitled') ?></h1>
            <?php if (!empty($content['excerpt'])): ?>
            <p class="text-xl text-gray-600"><?= htmlspecialchars($content['excerpt']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Meta Info -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <?php if ($author): ?>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                        <span class="text-primary font-medium text-xs"><?= strtoupper(substr($author['name'] ?? 'A', 0, 2)) ?></span>
                    </div>
                    <span><?= htmlspecialchars($author['name'] ?? 'Anonymous') ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($content['created_at'])): ?>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span><?= site_date($content['created_at']) ?></span>
                </div>
                <?php endif; ?>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="capitalize"><?= htmlspecialchars($content['type'] ?? 'post') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <article class="max-w-4xl mx-auto px-4 py-12">
        <div id="content-body" 
             class="prose prose-lg max-w-none"
             data-editorjs-content='<?= htmlspecialchars(json_encode($contentData), ENT_QUOTES, 'UTF-8') ?>'>
            <!-- Content will be rendered by JS -->
            <noscript>
                <p class="text-gray-500">Please enable JavaScript to view this content.</p>
            </noscript>
        </div>
    </article>

    <!-- Navigation -->
    <div class="bg-gray-50 border-t border-gray-100">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <a href="/" class="inline-flex items-center text-primary hover:text-primaryHover font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>

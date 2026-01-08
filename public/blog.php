<?php
/**
 * Blog Listing Page
 * Shows all published posts with pagination
 */
declare(strict_types=1);

// Bootstrap the framework
require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Initialize app for database access
$app = new Core\App();

// Get posts_per_page from settings
$postsPerPage = (int) site_setting('posts_per_page', '10');
if ($postsPerPage < 1) $postsPerPage = 10;

// Get all published posts
$posts = db()->table('cms_content')
    ->where('type', 'post')
    ->where('status', 'published')
    ->orderBy('created_at', 'DESC')
    ->limit($postsPerPage)
    ->get();

$pageTitle = 'Blog - ' . site_title();
$pageDescription = 'Latest articles and updates';

include __DIR__ . '/header.php';
?>

<main class="flex-1">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">
        <div class="max-w-6xl mx-auto px-4 py-16 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Blog</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">Discover our latest articles, tutorials, and insights</p>
        </div>
    </section>

    <!-- Posts Grid -->
    <section class="max-w-6xl mx-auto px-4 py-12">
        <?php if (empty($posts)): ?>
        <div class="text-center py-16 bg-gray-50 rounded-2xl">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No posts yet</h3>
            <p class="text-gray-500">Check back soon for new content!</p>
        </div>
        <?php else: ?>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                <?php if (!empty($post['featured_image'])): ?>
                <a href="/<?= htmlspecialchars($post['slug']) ?>" class="block aspect-video overflow-hidden">
                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" 
                         alt="<?= htmlspecialchars($post['title'] ?? '') ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
                <?php else: ?>
                <a href="/<?= htmlspecialchars($post['slug']) ?>" class="block aspect-video bg-gradient-to-br from-primary/10 to-purple-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </a>
                <?php endif; ?>
                
                <div class="p-6">
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <?= site_date($post['created_at'] ?? 'now') ?>
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary transition-colors line-clamp-2">
                        <a href="/<?= htmlspecialchars($post['slug']) ?>">
                            <?= htmlspecialchars($post['title'] ?? 'Untitled') ?>
                        </a>
                    </h2>
                    
                    <?php if (!empty($post['excerpt'])): ?>
                    <p class="text-gray-500 text-sm line-clamp-3 mb-4"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <?php endif; ?>
                    
                    <a href="/<?= htmlspecialchars($post['slug']) ?>" class="inline-flex items-center text-primary text-sm font-semibold hover:text-primaryHover transition-colors group/link">
                        Read article
                        <svg class="w-4 h-4 ml-1 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>

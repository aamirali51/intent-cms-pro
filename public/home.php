<?php
/**
 * Public Frontend Homepage
 * Shows homepage content or latest posts
 */
declare(strict_types=1);

// Bootstrap the framework
require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Initialize app for database access
$app = new Core\App();

// Check if there's a published 'home' page
$homePage = db()->table('cms_content')
    ->where('slug', 'home')
    ->where('status', 'published')
    ->first();

// If home page exists, render it using single.php logic
if ($homePage) {
    $content = $homePage;
    $pageTitle = htmlspecialchars($content['title'] ?? 'Home') . ' - ' . site_title();
    $pageDescription = htmlspecialchars($content['excerpt'] ?? '');
    
    // Parse content JSON
    $contentData = null;
    if (!empty($content['content'])) {
        $contentData = json_decode($content['content'], true);
    }
    
    include __DIR__ . '/header.php';
    ?>
    <main class="flex-1">
        <?php if (!empty($content['featured_image'])): ?>
        <div class="relative h-[500px] overflow-hidden">
            <img src="<?= htmlspecialchars($content['featured_image']) ?>" 
                 alt="<?= htmlspecialchars($content['title'] ?? '') ?>" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="max-w-4xl mx-auto text-center">
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-4"><?= htmlspecialchars($content['title'] ?? 'Welcome') ?></h1>
                    <?php if (!empty($content['excerpt'])): ?>
                    <p class="text-xl text-white/80 max-w-2xl mx-auto"><?= htmlspecialchars($content['excerpt']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <article class="max-w-4xl mx-auto px-4 py-12">
            <div id="content-body" 
                 class="prose prose-lg max-w-none"
                 data-editorjs-content='<?= htmlspecialchars(json_encode($contentData), ENT_QUOTES, 'UTF-8') ?>'>
            </div>
        </article>
    </main>
    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// Get posts_per_page from settings
$postsPerPage = (int) site_setting('posts_per_page', '10');
if ($postsPerPage < 1) $postsPerPage = 10;

// Otherwise, show latest posts
$posts = db()->table('cms_content')
    ->where('type', 'post')
    ->where('status', 'published')
    ->orderBy('created_at', 'DESC')
    ->limit($postsPerPage)
    ->get();

$pageTitle = site_title() . ' - ' . site_tagline();
$pageDescription = site_tagline();

include __DIR__ . '/header.php';
?>

<main class="flex-1">
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary via-purple-600 to-indigo-700 text-white">
        <div class="max-w-6xl mx-auto px-4 py-20 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6">Welcome to <?= htmlspecialchars(site_title()) ?></h1>
            <p class="text-xl text-white/80 max-w-2xl mx-auto mb-8"><?= htmlspecialchars(site_tagline()) ?></p>
            <a href="/blog" class="inline-flex items-center px-8 py-4 bg-white text-primary font-semibold rounded-lg hover:bg-gray-100 transition-colors shadow-lg">
                Explore the Blog
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </section>

    <!-- Latest Posts -->
    <section class="max-w-6xl mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Latest Posts</h2>
                <p class="text-gray-500 mt-1">Fresh content from our blog</p>
            </div>
            <a href="/blog" class="text-primary hover:text-primaryHover font-medium flex items-center gap-1 transition-colors">
                View all
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

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
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow group">
                <?php if (!empty($post['featured_image'])): ?>
                <a href="/<?= htmlspecialchars($post['slug']) ?>" class="block aspect-video overflow-hidden">
                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" 
                         alt="<?= htmlspecialchars($post['title'] ?? '') ?>"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </a>
                <?php else: ?>
                <a href="/<?= htmlspecialchars($post['slug']) ?>" class="block aspect-video bg-gradient-to-br from-primary/10 to-purple-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </a>
                <?php endif; ?>
                
                <div class="p-6">
                    <div class="text-xs text-gray-400 mb-2">
                        <?= site_date($post['created_at'] ?? 'now') ?>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                        <a href="/<?= htmlspecialchars($post['slug']) ?>">
                            <?= htmlspecialchars($post['title'] ?? 'Untitled') ?>
                        </a>
                    </h3>
                    <?php if (!empty($post['excerpt'])): ?>
                    <p class="text-gray-500 text-sm line-clamp-2"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <?php endif; ?>
                    
                    <a href="/<?= htmlspecialchars($post['slug']) ?>" class="inline-flex items-center text-primary text-sm font-medium mt-4 hover:text-primaryHover transition-colors">
                        Read more
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

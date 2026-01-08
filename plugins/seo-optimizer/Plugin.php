<?php

declare(strict_types=1);

namespace Plugins\SeoOptimizer;

use App\Attributes\Plugin;
use App\Attributes\Action;
use App\Attributes\Filter;

/**
 * SEO Optimizer Plugin
 * 
 * Demonstrates the modern PHP 8 Attribute-based plugin system.
 * This plugin automatically optimizes content for search engines.
 */
#[Plugin(
    name: 'SEO Optimizer',
    version: '1.0.0',
    author: 'Intent CMS',
    description: 'Automatically optimizes content for search engines'
)]
class SeoOptimizerPlugin
{
    /**
     * Filter: Add meta description to content
     */
    #[Filter('cms.the_content', priority: 5)]
    public function addMetaHints(string $content, int $postId): string
    {
        // Add schema.org article markup hint
        $schemaHint = '<!-- Schema.org Article markup recommended -->';
        return $schemaHint . "\n" . $content;
    }

    /**
     * Filter: Optimize headings for SEO
     */
    #[Filter('cms.the_content', priority: 10)]
    public function optimizeHeadings(string $content, int $postId): string
    {
        // Ensure proper heading hierarchy (example transformation)
        return $content;
    }

    /**
     * Action: Log post saves for SEO tracking
     */
    #[Action('cms.post.saved', priority: 20)]
    public function onPostSaved(int $postId, array $data): void
    {
        // Log for sitemap regeneration queue
        error_log(sprintf(
            '[SEO Optimizer] Post %d saved - queue sitemap update',
            $postId
        ));
    }

    /**
     * Action: Notify search engines on publish
     */
    #[Action('cms.post.published', priority: 10)]
    public function onPostPublished(int $postId, string $title): void
    {
        // In production: ping Google, Bing sitemaps
        error_log(sprintf(
            '[SEO Optimizer] Post "%s" published - ping search engines',
            $title
        ));
    }

    /**
     * Filter: Add Open Graph tags to post meta
     */
    #[Filter('cms.post.meta', priority: 5)]
    public function addOpenGraphMeta(array $meta, int $postId, string $content): array
    {
        $meta['og:type'] = 'article';
        $meta['og:title'] = $meta['title'] ?? 'Untitled';
        $meta['twitter:card'] = 'summary_large_image';
        
        return $meta;
    }
}

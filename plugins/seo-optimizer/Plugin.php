<?php

declare(strict_types=1);

namespace Plugins\SeoOptimizer;

use App\Attributes\Plugin;
use App\Attributes\Action;
use App\Attributes\Filter;
use App\Attributes\PluginSetting;
use App\Contracts\PluginInterface;

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
    authorUri: 'https://intentcms.com',
    description: 'Automatically optimizes content for search engines',
    icon: 'trending_up',
    tags: ['seo', 'meta', 'schema', 'optimization']
)]
#[PluginSetting(
    key: 'enable_schema',
    type: 'toggle',
    label: 'Enable Schema.org Markup',
    description: 'Add structured data to posts for rich snippets',
    default: true,
    group: 'general'
)]
#[PluginSetting(
    key: 'enable_og',
    type: 'toggle',
    label: 'Enable Open Graph Tags',
    description: 'Add Facebook/social media meta tags',
    default: true,
    group: 'general'
)]
#[PluginSetting(
    key: 'twitter_handle',
    type: 'text',
    label: 'Twitter Handle',
    description: 'Your Twitter username (without @)',
    default: '',
    group: 'social'
)]
#[PluginSetting(
    key: 'default_og_image',
    type: 'text',
    label: 'Default OG Image URL',
    description: 'Default image for social sharing',
    default: '',
    group: 'social'
)]
class SeoOptimizerPlugin implements PluginInterface
{
    /**
     * Boot the plugin - called on every request
     */
    public function boot(): void
    {
        // Hooks are registered via #[Action] and #[Filter] attributes
        // But we can also add dynamic hooks here
        add_action('cms.admin.head', [$this, 'addAdminStyles']);
    }

    /**
     * Activate the plugin
     */
    public function activate(): void
    {
        // Initialize default settings if needed
    }

    /**
     * Deactivate the plugin
     */
    public function deactivate(): void
    {
        // Cleanup temporary data
    }

    /**
     * Uninstall the plugin
     */
    public function uninstall(): void
    {
        // Remove all plugin data from database
    }

    /**
     * Add admin panel styles
     */
    public function addAdminStyles(): void
    {
        echo '<style>.seo-hint { color: #059669; font-size: 12px; }</style>';
    }

    /**
     * Filter: Add meta description to content
     */
    #[Filter('cms.the_content', priority: 5)]
    public function addMetaHints(string $content, int $postId): string
    {
        $manager = \App\Services\PluginManager::getInstance();
        $enableSchema = $manager->getSetting('seo-optimizer', 'enable_schema', true);
        
        if (!$enableSchema) {
            return $content;
        }
        
        $schemaHint = '<!-- Schema.org Article markup recommended -->';
        return $schemaHint . "\n" . $content;
    }

    /**
     * Filter: Optimize headings for SEO
     */
    #[Filter('cms.the_content', priority: 10)]
    public function optimizeHeadings(string $content, int $postId): string
    {
        return $content;
    }

    /**
     * Action: Log post saves for SEO tracking
     */
    #[Action('cms.post.saved', priority: 20)]
    public function onPostSaved(int $postId, array $data): void
    {
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
        $manager = \App\Services\PluginManager::getInstance();
        $enableOg = $manager->getSetting('seo-optimizer', 'enable_og', true);
        
        if (!$enableOg) {
            return $meta;
        }
        
        $twitterHandle = $manager->getSetting('seo-optimizer', 'twitter_handle', '');
        $defaultImage = $manager->getSetting('seo-optimizer', 'default_og_image', '');
        
        $meta['og:type'] = 'article';
        $meta['og:title'] = $meta['title'] ?? 'Untitled';
        $meta['twitter:card'] = 'summary_large_image';
        
        if ($twitterHandle) {
            $meta['twitter:site'] = '@' . $twitterHandle;
        }
        
        if ($defaultImage && empty($meta['og:image'])) {
            $meta['og:image'] = $defaultImage;
        }
        
        return $meta;
    }
}

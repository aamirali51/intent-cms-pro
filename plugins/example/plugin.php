<?php

declare(strict_types=1);

/**
 * Intent CMS Pro - Example Plugin
 * 
 * Demonstrates the advanced hook system:
 * - Actions: React to events (logging, notifications)
 * - Filters: Modify content in the pipeline
 * 
 * Plugin Hook Naming Convention:
 *   {namespace}.{area}.{event}@{plugin_name}
 *   
 * To avoid conflicts, use the @plugin suffix for plugin-specific hooks.
 */

// Ensure helpers are loaded
if (!function_exists('add_action')) {
    return;
}

/**
 * ACTION EXAMPLE: Log when a post is saved
 * 
 * Hook: cms.post.saved
 * Fired after a post is created or updated in PostHandler
 */
add_action('cms.post.saved', function (int $postId, array $data): void {
    // Log the event (replace with your logging implementation)
    error_log(sprintf(
        '[ExamplePlugin] Post saved: ID=%d, Title="%s"',
        $postId,
        $data['title'] ?? 'Untitled'
    ));
}, 10);

/**
 * ACTION EXAMPLE: Send notification on post publish
 * 
 * Hook: cms.post.published
 * Only fires when status changes to 'published'
 */
add_action('cms.post.published', function (int $postId, string $title): void {
    // Example: Send email notification
    error_log(sprintf(
        '[ExamplePlugin] Post published! ID=%d, Title="%s"',
        $postId,
        $title
    ));
}, 20);

/**
 * FILTER EXAMPLE: Modify post content before display
 * 
 * Hook: cms.the_content
 * Modifies the rendered content for the frontend
 */
add_filter('cms.the_content', function (string $content, int $postId): string {
    // Example: Add social sharing buttons after content
    $shareButtons = '
        <div class="social-share mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-3">Share this post:</p>
            <div class="flex gap-3">
                <a href="https://twitter.com/intent/tweet?url=' . urlencode($_SERVER['REQUEST_URI'] ?? '') . '" 
                   target="_blank" rel="noopener"
                   class="px-4 py-2 bg-blue-400 text-white text-sm rounded-lg hover:bg-blue-500 transition-colors">
                    Twitter
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode($_SERVER['REQUEST_URI'] ?? '') . '" 
                   target="_blank" rel="noopener"
                   class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                    Facebook
                </a>
            </div>
        </div>
    ';
    
    return $content . $shareButtons;
}, 99); // Low priority = runs late (after other filters)

/**
 * FILTER EXAMPLE: Auto-link URLs in content
 * 
 * Hook: cms.the_content
 * Runs before social buttons (higher priority = earlier)
 */
add_filter('cms.the_content', function (string $content, int $postId): string {
    // Simple URL auto-linking (basic example)
    $pattern = '/(https?:\/\/[^\s<>"]+)/i';
    $replacement = '<a href="$1" target="_blank" rel="noopener" class="text-primary hover:underline">$1</a>';
    
    /** @var string $result */
    $result = preg_replace($pattern, $replacement, $content);
    return $result;
}, 10);

/**
 * FILTER EXAMPLE: Add reading time estimate
 * 
 * Hook: cms.post.meta
 * Adds metadata to post display
 */
add_filter('cms.post.meta', function (array $meta, int $postId, string $content): array {
    // Calculate reading time (avg 200 words/min)
    $wordCount = str_word_count(strip_tags($content));
    $readingTime = max(1, (int) ceil($wordCount / 200));
    
    $meta['reading_time'] = $readingTime;
    $meta['reading_time_text'] = $readingTime . ' min read';
    
    return $meta;
}, 10);

/**
 * ACTION EXAMPLE: Plugin initialization
 * 
 * Hook: cms.init
 * Fires when the CMS is fully initialized
 */
add_action('cms.init', function (): void {
    error_log('[ExamplePlugin] Plugin initialized successfully');
}, 5);

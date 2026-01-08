<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Filter Attribute
 * 
 * Marks a method as a filter hook handler.
 * 
 * Usage:
 * #[Filter('cms.the_content', priority: 10)]
 * public function filterContent(string $content, int $postId): string { ... }
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Filter
{
    public function __construct(
        public string $hook,
        public int $priority = 10
    ) {}
}

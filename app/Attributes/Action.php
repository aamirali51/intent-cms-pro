<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Action Attribute
 * 
 * Marks a method as an action hook handler.
 * 
 * Usage:
 * #[Action('cms.post.saved', priority: 10)]
 * public function onPostSaved(int $postId, array $data): void { ... }
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Action
{
    public function __construct(
        public string $hook,
        public int $priority = 10
    ) {}
}

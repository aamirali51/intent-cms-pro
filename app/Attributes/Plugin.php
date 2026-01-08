<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Plugin Attribute
 * 
 * Marks a class as a plugin with metadata.
 * 
 * Usage:
 * #[Plugin(name: 'My SEO Plugin', version: '1.0.0', author: 'John Doe')]
 * class MySeoPlugin { ... }
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Plugin
{
    public function __construct(
        public string $name,
        public string $version = '1.0.0',
        public string $author = '',
        public string $description = '',
        public bool $enabled = true
    ) {}
}

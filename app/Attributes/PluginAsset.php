<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Plugin Asset Attribute
 * 
 * Register CSS or JavaScript assets to be loaded in admin or frontend.
 * 
 * @example
 * ```php
 * #[PluginAsset(type: 'css', path: 'assets/style.css', location: 'admin')]
 * #[PluginAsset(type: 'js', path: 'assets/script.js', location: 'admin', defer: true)]
 * #[PluginAsset(type: 'css', path: 'assets/frontend.css', location: 'frontend')]
 * class MyPlugin { }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class PluginAsset
{
    /**
     * @param string $type     Asset type: 'css' or 'js'
     * @param string $path     Path relative to plugin directory
     * @param string $location Where to load: 'admin', 'frontend', or 'both'
     * @param bool   $defer    For JS: add defer attribute
     * @param bool   $async    For JS: add async attribute
     * @param int    $priority Load order (lower = earlier)
     * @param array<int, string> $dependencies Other assets this depends on
     */
    public function __construct(
        public readonly string $type,
        public readonly string $path,
        public readonly string $location = 'admin',
        public readonly bool $defer = false,
        public readonly bool $async = false,
        public readonly int $priority = 50,
        public readonly array $dependencies = [],
    ) {}
}

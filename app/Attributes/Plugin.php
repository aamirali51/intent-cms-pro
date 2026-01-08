<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Plugin Attribute
 * 
 * Mark a class as a CMS plugin. This is the main entry point for plugin discovery.
 * 
 * @example Minimal usage:
 * ```php
 * #[Plugin(name: 'My Plugin', version: '1.0.0')]
 * class MyPlugin { }
 * ```
 * 
 * @example Advanced usage with dependencies:
 * ```php
 * #[Plugin(
 *     name: 'Advanced Plugin',
 *     version: '2.0.0',
 *     description: 'A feature-rich plugin',
 *     author: 'Your Name',
 *     authorUri: 'https://yoursite.com',
 *     icon: 'extension',
 *     requires: ['core-utilities' => '^1.0', 'api-helper' => '>=2.0'],
 *     conflicts: ['old-legacy-plugin'],
 *     minPhpVersion: '8.2',
 *     minCmsVersion: '1.0.0'
 * )]
 * class AdvancedPlugin implements PluginInterface { }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Plugin
{
    /**
     * @param string $name           Display name of the plugin
     * @param string $version        Semantic version (e.g., '1.0.0')
     * @param string $description    Short description for admin UI
     * @param string $author         Author name
     * @param string $authorUri      Author website URL
     * @param string $icon           Material Icons Round icon name (default: 'extension')
     * @param string $minPhpVersion  Minimum PHP version required
     * @param string $minCmsVersion  Minimum Intent CMS version required
     * @param array<string, string> $requires  Dependencies: ['plugin-id' => 'version-constraint']
     * @param array<int, string>    $conflicts Incompatible plugins
     * @param array<int, string>    $provides  Virtual package names this plugin provides
     * @param array<int, string>    $tags      Searchable tags for admin UI
     */
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $description = '',
        public readonly string $author = '',
        public readonly string $authorUri = '',
        public readonly string $icon = 'extension',
        public readonly string $minPhpVersion = '8.1',
        public readonly string $minCmsVersion = '1.0.0',
        public readonly array $requires = [],
        public readonly array $conflicts = [],
        public readonly array $provides = [],
        public readonly array $tags = [],
    ) {}

    /**
     * Check if PHP version requirement is met
     */
    public function isPhpCompatible(): bool
    {
        return version_compare(PHP_VERSION, $this->minPhpVersion, '>=');
    }

    /**
     * Check if CMS version requirement is met
     */
    public function isCmsCompatible(string $cmsVersion): bool
    {
        return version_compare($cmsVersion, $this->minCmsVersion, '>=');
    }
}

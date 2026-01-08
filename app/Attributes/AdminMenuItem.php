<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Admin Menu Item Attribute
 * 
 * Add a sidebar menu item for your plugin in the admin panel.
 * 
 * @example
 * ```php
 * #[AdminMenuItem(
 *     label: 'My Plugin',
 *     icon: 'dashboard',
 *     route: '/admin/my-plugin.php',
 *     position: 50
 * )]
 * class MyPlugin { }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class AdminMenuItem
{
    /**
     * @param string $label     Menu item text
     * @param string $route     URL path (relative to admin)
     * @param string $icon      Material Icons Round icon name
     * @param int    $position  Sort order (lower = higher in menu)
     * @param string $parent    Parent menu ID for submenus
     * @param string $capability Required capability (future: role-based access)
     * @param string $badge     Badge text (e.g., count notification)
     */
    public function __construct(
        public readonly string $label,
        public readonly string $route,
        public readonly string $icon = 'extension',
        public readonly int $position = 100,
        public readonly string $parent = '',
        public readonly string $capability = 'admin',
        public readonly string $badge = '',
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Attributes;

use Attribute;

/**
 * Plugin Setting Attribute
 * 
 * Define a setting for your plugin that will be automatically managed.
 * Settings are stored in the database and accessible via the Plugin API.
 * 
 * @example
 * ```php
 * #[PluginSetting('api_key', type: 'password', label: 'API Key', required: true)]
 * #[PluginSetting('max_items', type: 'number', label: 'Max Items', default: 10)]
 * #[PluginSetting('enabled', type: 'toggle', label: 'Enable Feature', default: true)]
 * class MyPlugin { }
 * ```
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class PluginSetting
{
    /**
     * @param string $key         Unique setting key within the plugin
     * @param string $type        Input type: text, password, number, toggle, select, textarea
     * @param string $label       Human-readable label
     * @param string $description Help text shown below the input
     * @param mixed  $default     Default value
     * @param bool   $required    Whether the setting is required
     * @param array<string, string> $options For 'select' type: ['value' => 'Label']
     * @param string $group       Settings group for organization
     */
    public function __construct(
        public readonly string $key,
        public readonly string $type = 'text',
        public readonly string $label = '',
        public readonly string $description = '',
        public readonly mixed $default = null,
        public readonly bool $required = false,
        public readonly array $options = [],
        public readonly string $group = 'general',
    ) {}
}

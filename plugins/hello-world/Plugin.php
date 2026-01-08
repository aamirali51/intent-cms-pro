<?php

declare(strict_types=1);

use App\Attributes\Plugin;
use App\Attributes\PluginSetting;
use App\Attributes\AdminMenuItem;
use App\Contracts\PluginInterface;

/**
 * Hello World Plugin
 * 
 * A minimal example plugin demonstrating the Intent CMS plugin system.
 * 
 * This plugin shows:
 * - Basic #[Plugin] attribute usage
 * - Settings definition with #[PluginSetting]
 * - Hook registration in boot()
 * - Lifecycle hooks (activate/deactivate)
 */
#[Plugin(
    name: 'Hello World',
    version: '1.0.0',
    description: 'A simple example plugin to demonstrate the plugin system',
    author: 'Intent CMS',
    icon: 'waving_hand',
    tags: ['example', 'demo', 'starter']
)]
#[PluginSetting(
    key: 'greeting',
    type: 'text',
    label: 'Custom Greeting',
    description: 'The greeting message to display',
    default: 'Hello, World!'
)]
#[PluginSetting(
    key: 'show_in_footer',
    type: 'toggle',
    label: 'Show in Footer',
    description: 'Display the greeting in the site footer',
    default: false
)]
class HelloWorldPlugin implements PluginInterface
{
    /**
     * Called every request when plugin is active
     */
    public function boot(): void
    {
        // Register a hook to display greeting in footer
        add_action('cms.footer', [$this, 'displayGreeting']);
        
        // Register a filter to modify post titles (as an example)
        add_filter('cms.the_title', [$this, 'modifyTitle']);
    }

    /**
     * Called once when plugin is activated
     */
    public function activate(): void
    {
        // Initialization logic here
        // For example: create database tables, set default options, etc.
    }

    /**
     * Called once when plugin is deactivated
     */
    public function deactivate(): void
    {
        // Cleanup logic here (but preserve data for reactivation)
    }

    /**
     * Called when plugin is permanently deleted
     */
    public function uninstall(): void
    {
        // Remove all plugin data
    }

    /**
     * Display greeting in footer
     */
    public function displayGreeting(): void
    {
        $manager = \App\Services\PluginManager::getInstance();
        $showInFooter = $manager->getSetting('hello-world', 'show_in_footer', false);
        
        if ($showInFooter) {
            $greeting = $manager->getSetting('hello-world', 'greeting', 'Hello, World!');
            echo "<p class='hello-world-greeting'>" . htmlspecialchars($greeting) . "</p>";
        }
    }

    /**
     * Example filter - could modify post titles
     */
    public function modifyTitle(string $title): string
    {
        // This is just an example - doesn't actually modify anything
        return $title;
    }
}

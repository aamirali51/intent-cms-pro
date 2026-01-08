<?php
declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        db()->raw('
            CREATE TABLE IF NOT EXISTS cms_settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `key` VARCHAR(255) NOT NULL UNIQUE,
                `value` TEXT,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_settings_key (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // Seed default settings
        $defaults = [
            // Site Identity
            'site_title' => 'Intent CMS',
            'tagline' => 'Just another Intent CMS site',
            'admin_email' => 'admin@intentcms.com',
            
            // Regional Settings
            'language' => 'en_US',
            'timezone' => 'UTC',
            'date_format' => 'F j, Y',
            'week_starts_on' => 'monday',
            
            // Membership
            'allow_registration' => '0',
            'default_role' => 'subscriber',
            
            // Site Status
            'maintenance_mode' => '0',
            
            // Permalinks
            'permalink_structure' => '/%postname%/',
            
            // Reading
            'posts_per_page' => '10',
            'show_on_front' => 'posts',
            
            // Discussion
            'comments_enabled' => '1',
            'comment_moderation' => '1',
            
            // Media
            'thumbnail_size_w' => '150',
            'thumbnail_size_h' => '150',
            'medium_size_w' => '300',
            'medium_size_h' => '300',
            'large_size_w' => '1024',
            'large_size_h' => '1024',
        ];

        foreach ($defaults as $key => $value) {
            db()->raw(
                'INSERT INTO cms_settings (`key`, `value`, created_at, updated_at) VALUES (?, ?, NOW(), NOW())',
                [$key, $value]
            );
        }
    }

    public function down(): void
    {
        db()->raw('DROP TABLE IF EXISTS cms_settings');
    }
};

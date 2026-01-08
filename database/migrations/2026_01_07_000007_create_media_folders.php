<?php

declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Create cms_media_folders table
        db()->raw('
            CREATE TABLE IF NOT EXISTS cms_media_folders (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                parent_id INT UNSIGNED NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                FOREIGN KEY (parent_id) REFERENCES cms_media_folders(id) ON DELETE CASCADE
            )
        ');

        // Add folder_id column to cms_media table
        try {
            db()->raw('ALTER TABLE cms_media ADD COLUMN folder_id INT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // Column might already exist
        }
    }

    public function down(): void
    {
        try {
            db()->raw('ALTER TABLE cms_media DROP COLUMN folder_id');
        } catch (\Throwable $e) {
            // Column might not exist
        }
        
        db()->raw('DROP TABLE IF EXISTS cms_media_folders');
    }
};

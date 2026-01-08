<?php

declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Add thumbnail_path column if it doesn't exist
        try {
            db()->raw('ALTER TABLE cms_media ADD COLUMN thumbnail_path VARCHAR(512) NULL');
        } catch (\Throwable $e) {
            // Column might already exist, ignore
        }
    }

    public function down(): void
    {
        try {
            db()->raw('ALTER TABLE cms_media DROP COLUMN thumbnail_path');
        } catch (\Throwable $e) {
            // Column might not exist, ignore
        }
    }
};

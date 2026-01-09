<?php declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        db()->raw('
            CREATE TABLE IF NOT EXISTS cms_tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL UNIQUE,
                color VARCHAR(20) DEFAULT \'#6b7280\',
                description TEXT,
                count INT DEFAULT 0,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_slug (slug),
                INDEX idx_count (count DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        db()->raw('DROP TABLE IF EXISTS cms_tags');
    }
};

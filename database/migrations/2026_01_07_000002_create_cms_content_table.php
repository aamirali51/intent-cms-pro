<?php

declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS cms_content (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(50) NOT NULL DEFAULT 'post',
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                content LONGTEXT NULL,
                excerpt TEXT NULL,
                status VARCHAR(50) DEFAULT 'draft',
                author_id INT NULL,
                featured_image VARCHAR(255) NULL,
                meta_title VARCHAR(255) NULL,
                meta_description TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_type (type),
                INDEX idx_status (status),
                INDEX idx_slug (slug),
                INDEX idx_author (author_id),
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        $this->execute("DROP TABLE IF EXISTS cms_content");
    }
};

<?php declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        db()->raw("
            CREATE TABLE IF NOT EXISTS cms_comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                content_id INT NOT NULL,
                parent_id INT NULL,
                author_name VARCHAR(100) NOT NULL,
                author_email VARCHAR(255) NOT NULL,
                author_url VARCHAR(255) NULL,
                author_ip VARCHAR(45) NULL,
                content TEXT NOT NULL,
                status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'pending',
                user_id INT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                INDEX idx_content (content_id),
                INDEX idx_status (status),
                INDEX idx_parent (parent_id),
                INDEX idx_created (created_at DESC),
                CONSTRAINT fk_comments_content 
                    FOREIGN KEY (content_id) REFERENCES cms_content(id) ON DELETE CASCADE,
                CONSTRAINT fk_comments_user 
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        db()->raw('DROP TABLE IF EXISTS cms_comments');
    }
};

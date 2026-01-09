<?php declare(strict_types=1);

use Core\Migration;

return new class extends Migration
{
    public function up(): void
    {
        db()->raw('
            CREATE TABLE IF NOT EXISTS cms_content_tags (
                content_id INT NOT NULL,
                tag_id INT NOT NULL,
                PRIMARY KEY (content_id, tag_id),
                INDEX idx_tag (tag_id),
                CONSTRAINT fk_content_tags_content 
                    FOREIGN KEY (content_id) REFERENCES cms_content(id) ON DELETE CASCADE,
                CONSTRAINT fk_content_tags_tag 
                    FOREIGN KEY (tag_id) REFERENCES cms_tags(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');
    }

    public function down(): void
    {
        db()->raw('DROP TABLE IF EXISTS cms_content_tags');
    }
};

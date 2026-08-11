<?php
/**
 * 外掛清單資料模型
 */

declare(strict_types=1);

namespace App\Model;

final class Plugin extends BaseModel
{
    public function all(): array
    {
        return $this->db->all('SELECT * FROM plugins ORDER BY name ASC');
    }

    public function find(string $slug): ?array
    {
        return $this->db->one('SELECT * FROM plugins WHERE slug = :s', ['s' => $slug]);
    }

    public function setActive(string $slug, bool $active): void
    {
        $this->db->run(
            'UPDATE plugins SET is_active = :a WHERE slug = :s',
            ['a' => $active ? 1 : 0, 's' => $slug]
        );
    }

    public function countActive(): int
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM plugins WHERE is_active = 1');
    }
}

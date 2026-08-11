<?php
/**
 * 文章分類資料模型
 */

declare(strict_types=1);

namespace App\Model;

final class Category extends BaseModel
{
    public function all(): array
    {
        return $this->db->all('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
    }

    /** 各分類的文章篇數（後台列表用） */
    public function allWithCounts(): array
    {
        return $this->db->all(
            'SELECT c.*, COUNT(p.id) AS post_count
             FROM categories c
             LEFT JOIN posts p ON p.category_id = c.id
             GROUP BY c.id
             ORDER BY c.sort_order ASC, c.name ASC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->db->one('SELECT * FROM categories WHERE id = :id', ['id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->one('SELECT * FROM categories WHERE slug = :s', ['s' => $slug]);
    }

    public function create(array $data): int
    {
        $this->db->run(
            'INSERT INTO categories (name, slug, description, sort_order)
             VALUES (:name, :slug, :description, :sort_order)',
            $data
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->run(
            'UPDATE categories SET name = :name, slug = :slug, description = :description,
             sort_order = :sort_order WHERE id = :id',
            $data
        );
    }

    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM categories WHERE id = :id', ['id' => $id]);
    }

    public function count(): int
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM categories');
    }
}

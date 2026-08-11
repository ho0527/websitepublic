<?php
/**
 * 博物館頁面資料模型
 *
 * is_selected = 1 → 精選博物館，使用整頁背景版型並顯示該館新聞
 * is_selected = 0 → 一般博物館，使用大圖橫幅版型
 */

declare(strict_types=1);

namespace App\Model;

final class Museum extends BaseModel
{
    private const SELECT_LIST = 'm.*, c.name AS category_name, c.slug AS category_slug';

    /** 前台：已發佈的博物館 */
    public function published(?bool $selectedOnly = null): array
    {
        $sql = 'SELECT ' . self::SELECT_LIST . '
                FROM museums m
                LEFT JOIN categories c ON c.id = m.category_id
                WHERE m.status = :status';
        $params = ['status' => 'published'];

        if ($selectedOnly !== null) {
            $sql .= ' AND m.is_selected = :selected';
            $params['selected'] = $selectedOnly ? 1 : 0;
        }

        $sql .= ' ORDER BY m.sort_order ASC, m.title ASC';

        return $this->db->all($sql, $params);
    }

    /** 後台：全部博物館（含草稿） */
    public function all(): array
    {
        return $this->db->all(
            'SELECT ' . self::SELECT_LIST . '
             FROM museums m
             LEFT JOIN categories c ON c.id = m.category_id
             ORDER BY m.is_selected DESC, m.sort_order ASC, m.title ASC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->db->one('SELECT * FROM museums WHERE id = :id', ['id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->one(
            'SELECT ' . self::SELECT_LIST . '
             FROM museums m
             LEFT JOIN categories c ON c.id = m.category_id
             WHERE m.slug = :slug AND m.status = :status',
            ['slug' => $slug, 'status' => 'published']
        );
    }

    public function create(array $data): int
    {
        $this->db->run(
            'INSERT INTO museums
                (title, slug, excerpt, content, featured_image, gallery, address,
                 opening_hours, is_selected, status, sort_order, category_id)
             VALUES
                (:title, :slug, :excerpt, :content, :featured_image, :gallery, :address,
                 :opening_hours, :is_selected, :status, :sort_order, :category_id)',
            $data
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->run(
            'UPDATE museums SET
                title = :title, slug = :slug, excerpt = :excerpt, content = :content,
                featured_image = :featured_image, gallery = :gallery, address = :address,
                opening_hours = :opening_hours, is_selected = :is_selected, status = :status,
                sort_order = :sort_order, category_id = :category_id
             WHERE id = :id',
            $data
        );
    }

    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM museums WHERE id = :id', ['id' => $id]);
    }

    /** 檢查 slug 是否已被其他資料列使用 */
    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM museums WHERE slug = :slug';
        $params = ['slug' => $slug];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }

        return (int) $this->db->value($sql, $params) > 0;
    }

    public function count(): int
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM museums');
    }
}

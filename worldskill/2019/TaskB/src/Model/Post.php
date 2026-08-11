<?php
/**
 * 新聞文章資料模型
 */

declare(strict_types=1);

namespace App\Model;

final class Post extends BaseModel
{
    private const SELECT_LIST = 'p.*, c.name AS category_name, c.slug AS category_slug, u.display_name AS author_name';

    private const JOINS = 'FROM posts p
                           INNER JOIN categories c ON c.id = p.category_id
                           LEFT JOIN users u ON u.id = p.author_id';

    /** 前台：最新文章（可指定分類） */
    public function latest(int $limit = 6, ?int $categoryId = null): array
    {
        $sql    = 'SELECT ' . self::SELECT_LIST . ' ' . self::JOINS . ' WHERE p.status = :status';
        $params = ['status' => 'published'];

        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = :cat';
            $params['cat'] = $categoryId;
        }

        $sql .= ' ORDER BY p.published_at DESC, p.id DESC LIMIT ' . max(1, $limit);

        return $this->db->all($sql, $params);
    }

    /** 前台：分頁列表 */
    public function paginate(int $page, int $perPage, ?int $categoryId = null): array
    {
        $offset = max(0, ($page - 1) * $perPage);
        $sql    = 'SELECT ' . self::SELECT_LIST . ' ' . self::JOINS . ' WHERE p.status = :status';
        $params = ['status' => 'published'];

        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = :cat';
            $params['cat'] = $categoryId;
        }

        $sql .= ' ORDER BY p.published_at DESC, p.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset;

        return $this->db->all($sql, $params);
    }

    public function countPublished(?int $categoryId = null): int
    {
        $sql    = 'SELECT COUNT(*) FROM posts WHERE status = :status';
        $params = ['status' => 'published'];

        if ($categoryId !== null) {
            $sql .= ' AND category_id = :cat';
            $params['cat'] = $categoryId;
        }

        return (int) $this->db->value($sql, $params);
    }

    /** 後台：全部文章 */
    public function all(): array
    {
        return $this->db->all(
            'SELECT ' . self::SELECT_LIST . ' ' . self::JOINS . ' ORDER BY p.published_at DESC, p.id DESC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->db->one('SELECT * FROM posts WHERE id = :id', ['id' => $id]);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->one(
            'SELECT ' . self::SELECT_LIST . ' ' . self::JOINS . '
             WHERE p.slug = :slug AND p.status = :status',
            ['slug' => $slug, 'status' => 'published']
        );
    }

    /** 同分類的前後篇（文章頁導覽用） */
    public function siblings(array $post): array
    {
        $prev = $this->db->one(
            'SELECT title, slug FROM posts
             WHERE status = :status AND category_id = :cat AND published_at < :when
             ORDER BY published_at DESC LIMIT 1',
            ['status' => 'published', 'cat' => $post['category_id'], 'when' => $post['published_at']]
        );

        $next = $this->db->one(
            'SELECT title, slug FROM posts
             WHERE status = :status AND category_id = :cat AND published_at > :when
             ORDER BY published_at ASC LIMIT 1',
            ['status' => 'published', 'cat' => $post['category_id'], 'when' => $post['published_at']]
        );

        return ['prev' => $prev, 'next' => $next];
    }

    public function create(array $data): int
    {
        $this->db->run(
            'INSERT INTO posts
                (title, slug, excerpt, content, featured_image, category_id, author_id, status, published_at)
             VALUES
                (:title, :slug, :excerpt, :content, :featured_image, :category_id, :author_id, :status, :published_at)',
            $data
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->run(
            'UPDATE posts SET
                title = :title, slug = :slug, excerpt = :excerpt, content = :content,
                featured_image = :featured_image, category_id = :category_id,
                status = :status, published_at = :published_at
             WHERE id = :id',
            $data
        );
    }

    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM posts WHERE id = :id', ['id' => $id]);
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM posts WHERE slug = :slug';
        $params = ['slug' => $slug];

        if ($exceptId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $exceptId;
        }

        return (int) $this->db->value($sql, $params) > 0;
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM posts WHERE status = :s', ['s' => $status]);
    }

    /** 儀表板「動態」小工具：最近更新 */
    public function recentActivity(int $limit = 5): array
    {
        return $this->db->all(
            'SELECT ' . self::SELECT_LIST . ' ' . self::JOINS . '
             ORDER BY p.updated_at DESC LIMIT ' . max(1, $limit)
        );
    }
}

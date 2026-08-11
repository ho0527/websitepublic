<?php
/**
 * 書本管理商業邏輯（列表／新增／刪除）
 */
class BookService
{
    /**
     * 取得書籍列表，並附帶目前租借者（未被租借時為 null）
     */
    public static function all(): array
    {
        $rows = Database::select(
            'SELECT b.id, b.name, b.isbn, b.author, b.created_at,
                    u.id AS reader_id, u.email AS reader_email,
                    u.username AS reader_username, u.role AS reader_role
               FROM books AS b
               LEFT JOIN rents AS r ON r.book_id = b.id
               LEFT JOIN users AS u ON u.id = r.user_id
              ORDER BY b.id ASC'
        );

        return array_map(static function (array $row): array {
            return [
                'id'         => (int) $row['id'],
                'name'       => $row['name'],
                'isbn'       => $row['isbn'],
                'author'     => $row['author'],
                'created_at' => (int) $row['created_at'],
                'reader'     => $row['reader_id'] === null ? null : [
                    'id'       => (int) $row['reader_id'],
                    'email'    => $row['reader_email'],
                    'username' => $row['reader_username'],
                    'role'     => $row['reader_role'],
                ],
            ];
        }, $rows);
    }

    /** 依編號取得單一書本 */
    public static function find(int $id): ?array
    {
        $row = Database::selectOne('SELECT id, name, isbn, author, created_at FROM books WHERE id = ? LIMIT 1', [$id]);

        if ($row === null) {
            return null;
        }

        $row['id']         = (int) $row['id'];
        $row['created_at'] = (int) $row['created_at'];

        return $row;
    }

    /**
     * 新增書本（限管理者）
     *
     * 錯誤判斷順序：未認證 → 權限不足 → ISBN 格式 → 書本重複
     */
    public static function insert(string $name, string $isbn, string $author): array
    {
        AuthService::requireAdmin();

        if (!Isbn::isValid($isbn)) {
            throw new GraphQLError('invalid isbn');
        }

        $existing = Database::selectOne(
            'SELECT id FROM books WHERE isbn = ? OR name = ? LIMIT 1',
            [$isbn, $name]
        );

        if ($existing !== null) {
            throw new GraphQLError('book already exists');
        }

        Database::execute(
            'INSERT INTO books (name, isbn, author, created_at) VALUES (?, ?, ?, ?)',
            [$name, $isbn, $author, time()]
        );

        return ['id' => Database::lastInsertId()];
    }

    /**
     * 刪除書本（限管理者），租借中的書本不可刪除
     */
    public static function remove(int $id): array
    {
        AuthService::requireAdmin();

        if (self::find($id) === null) {
            throw new GraphQLError('book not exists');
        }

        $rent = Database::selectOne('SELECT id FROM rents WHERE book_id = ? LIMIT 1', [$id]);
        if ($rent !== null) {
            throw new GraphQLError('book is rental');
        }

        Database::execute('DELETE FROM books WHERE id = ?', [$id]);

        return ['message' => 'book delete success'];
    }
}

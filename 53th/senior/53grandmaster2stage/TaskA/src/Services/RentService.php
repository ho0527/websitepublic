<?php
/**
 * 書本租借商業邏輯（租借列表／租借／歸還）
 */
class RentService
{
    /**
     * 取得目前登入會員的租借列表
     */
    public static function currentUserRents(): array
    {
        $user = AuthService::requireUser();

        $rows = Database::select(
            'SELECT r.id, r.created_at,
                    b.id AS book_id, b.name AS book_name, b.isbn AS book_isbn,
                    b.author AS book_author, b.created_at AS book_created_at
               FROM rents AS r
               INNER JOIN books AS b ON b.id = r.book_id
              WHERE r.user_id = ?
              ORDER BY r.id ASC',
            [$user['id']]
        );

        return array_map(static function (array $row) use ($user): array {
            return [
                'id'         => (int) $row['id'],
                'created_at' => (int) $row['created_at'],
                'user'       => $user,
                'book'       => [
                    'id'         => (int) $row['book_id'],
                    'name'       => $row['book_name'],
                    'isbn'       => $row['book_isbn'],
                    'author'     => $row['book_author'],
                    'created_at' => (int) $row['book_created_at'],
                ],
            ];
        }, $rows);
    }

    /**
     * 租借書本：書本不存在或已被租借時拋出錯誤
     */
    public static function insert(int $bookId): array
    {
        $user = AuthService::requireUser();

        if (BookService::find($bookId) === null) {
            throw new GraphQLError('book not exists');
        }

        $rented = Database::selectOne('SELECT id FROM rents WHERE book_id = ? LIMIT 1', [$bookId]);
        if ($rented !== null) {
            throw new GraphQLError('book is rental');
        }

        Database::execute(
            'INSERT INTO rents (user_id, book_id, created_at) VALUES (?, ?, ?)',
            [$user['id'], $bookId, time()]
        );

        return ['id' => Database::lastInsertId()];
    }

    /**
     * 歸還書本：只能歸還自己借的書
     */
    public static function remove(int $id): array
    {
        $user = AuthService::requireUser();

        $rent = Database::selectOne('SELECT id, user_id FROM rents WHERE id = ? LIMIT 1', [$id]);
        if ($rent === null) {
            throw new GraphQLError('rent not exists');
        }

        if ((int) $rent['user_id'] !== (int) $user['id']) {
            throw new GraphQLError('permission denied');
        }

        Database::execute('DELETE FROM rents WHERE id = ?', [$id]);

        return ['message' => 'rent delete success'];
    }
}

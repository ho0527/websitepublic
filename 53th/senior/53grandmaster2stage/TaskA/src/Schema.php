<?php
/**
 * GraphQL Schema 定義
 *
 * 型別：
 *   User  { id, email, username, role }
 *   Book  { id, name, isbn, author, created_at, reader }
 *   Rent  { id, created_at, book, user }
 *
 * Query：    user、books、rents
 * Mutation： login、logout、register、insertBook、removeBook、insertRent、removeRent
 */
class Schema
{
    /**
     * 建立 Schema 陣列供執行器使用
     */
    public static function build(): array
    {
        return [
            'types' => [
                /* ---------- 進入點 ---------- */
                'Query' => [
                    // 取得會員自身資料（需登入）
                    'user' => [
                        'type'    => 'User',
                        'resolve' => static fn () => AuthService::requireUser(),
                    ],
                    // 取得書籍列表
                    'books' => [
                        'type'    => '[Book]',
                        'resolve' => static fn () => BookService::all(),
                    ],
                    // 取得會員當前租借列表（需登入）
                    'rents' => [
                        'type'    => '[Rent]',
                        'resolve' => static fn () => RentService::currentUserRents(),
                    ],
                ],

                'Mutation' => [
                    // 訪客登入
                    'login' => [
                        'type'    => 'LoginPayload',
                        'resolve' => static fn ($source, array $args) => AuthService::login(
                            self::requireString($args, 'email'),
                            self::requireString($args, 'password')
                        ),
                    ],
                    // 訪客登出
                    'logout' => [
                        'type'    => 'MessagePayload',
                        'resolve' => static fn () => AuthService::logout(),
                    ],
                    // 訪客註冊
                    'register' => [
                        'type'    => 'MessagePayload',
                        'resolve' => static fn ($source, array $args) => AuthService::register(
                            self::requireString($args, 'email'),
                            self::requireString($args, 'password'),
                            self::requireString($args, 'username')
                        ),
                    ],
                    // 新增書本（管理者）
                    'insertBook' => [
                        'type'    => 'IdPayload',
                        'resolve' => static fn ($source, array $args) => BookService::insert(
                            self::requireString($args, 'name'),
                            self::requireString($args, 'isbn'),
                            self::requireString($args, 'author')
                        ),
                    ],
                    // 刪除書本（管理者）
                    'removeBook' => [
                        'type'    => 'MessagePayload',
                        'resolve' => static fn ($source, array $args) => BookService::remove(
                            self::requireInt($args, 'id')
                        ),
                    ],
                    // 租借書本（會員）
                    'insertRent' => [
                        'type'    => 'IdPayload',
                        'resolve' => static fn ($source, array $args) => RentService::insert(
                            self::requireInt($args, 'bookId')
                        ),
                    ],
                    // 歸還書本（會員）
                    'removeRent' => [
                        'type'    => 'MessagePayload',
                        'resolve' => static fn ($source, array $args) => RentService::remove(
                            self::requireInt($args, 'id')
                        ),
                    ],
                ],

                /* ---------- 資料型別 ---------- */
                'User' => [
                    'id'       => ['type' => 'Int'],
                    'email'    => ['type' => 'String'],
                    'username' => ['type' => 'String'],
                    'role'     => ['type' => 'String'],
                ],

                'Book' => [
                    'id'         => ['type' => 'Int'],
                    'name'       => ['type' => 'String'],
                    'isbn'       => ['type' => 'String'],
                    'author'     => ['type' => 'String'],
                    'created_at' => ['type' => 'Int'],
                    'reader'     => ['type' => 'User'],
                ],

                'Rent' => [
                    'id'         => ['type' => 'Int'],
                    'created_at' => ['type' => 'Int'],
                    'book'       => ['type' => 'Book'],
                    'user'       => ['type' => 'User'],
                ],

                /* ---------- 回應型別 ---------- */
                'LoginPayload' => [
                    'user_token' => ['type' => 'String'],
                ],

                'MessagePayload' => [
                    'message' => ['type' => 'String'],
                ],

                'IdPayload' => [
                    'id' => ['type' => 'Int'],
                ],
            ],
        ];
    }

    /** 取得必填的字串參數 */
    private static function requireString(array $args, string $name): string
    {
        if (!array_key_exists($name, $args) || $args[$name] === null) {
            throw new GraphQLError('Field "' . $name . '" of required type "String!" was not provided.');
        }

        return (string) $args[$name];
    }

    /** 取得必填的整數參數 */
    private static function requireInt(array $args, string $name): int
    {
        if (!array_key_exists($name, $args) || $args[$name] === null || !is_numeric($args[$name])) {
            throw new GraphQLError('Field "' . $name . '" of required type "Int!" was not provided.');
        }

        return (int) $args[$name];
    }
}

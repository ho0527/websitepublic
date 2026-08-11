<?php
/**
 * 資料庫安裝與還原
 *
 * install()：建立資料庫與資料表，並寫入試題指定的內建帳號與範例資料。
 * 模組 B 的單元測試會在每個測試案例執行前呼叫 install()，把資料庫還原成初始狀態。
 */
class Installer
{
    /** @var string[] 內建帳號使用的網域 */
    private static array $accountDomains = ['localhost'];

    public static function configure(array $accountDomains): void
    {
        self::$accountDomains = $accountDomains;
    }

    /**
     * 建立（或重建）資料庫結構與種子資料
     */
    public static function install(bool $dropExisting = true): void
    {
        $database = Database::databaseName();
        $server   = Database::serverConnection();

        $server->exec(
            'CREATE DATABASE IF NOT EXISTS `' . $database . '` '
            . 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );

        if ($dropExisting) {
            // 依外鍵相依順序刪除
            Database::execute('DROP TABLE IF EXISTS rents');
            Database::execute('DROP TABLE IF EXISTS books');
            Database::execute('DROP TABLE IF EXISTS users');
        }

        self::createTables();
        self::seed();
    }

    /** 建立資料表 */
    private static function createTables(): void
    {
        Database::execute(
            'CREATE TABLE IF NOT EXISTS users (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                email      VARCHAR(191) NOT NULL,
                password   VARCHAR(255) NOT NULL,
                username   VARCHAR(191) NOT NULL,
                role       ENUM("ADMIN", "USER") NOT NULL DEFAULT "USER",
                user_token CHAR(64) DEFAULT NULL,
                created_at INT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY users_email_unique (email),
                UNIQUE KEY users_username_unique (username),
                KEY users_token_index (user_token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        Database::execute(
            'CREATE TABLE IF NOT EXISTS books (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name       VARCHAR(255) NOT NULL,
                isbn       VARCHAR(20) NOT NULL,
                author     VARCHAR(255) NOT NULL,
                created_at INT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY books_isbn_unique (isbn)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        Database::execute(
            'CREATE TABLE IF NOT EXISTS rents (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id    INT UNSIGNED NOT NULL,
                book_id    INT UNSIGNED NOT NULL,
                created_at INT UNSIGNED NOT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY rents_book_unique (book_id),
                KEY rents_user_index (user_id),
                CONSTRAINT rents_user_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                CONSTRAINT rents_book_foreign FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }

    /** 寫入內建帳號與範例書籍、租借紀錄 */
    private static function seed(): void
    {
        $seedTime = 1693317403; // 與試題範例回應相同的時間戳

        // 內建帳號：admin（ADMIN）與 user1（USER）
        foreach (self::$accountDomains as $index => $domain) {
            $suffix = $index === 0 ? '' : (string) ($index + 1);

            self::insertUser('admin@' . $domain, 'adminpass', 'admin' . $suffix, 'ADMIN', $seedTime);
            self::insertUser('user1@' . $domain, 'user1pass', 'user1' . $suffix, 'USER', $seedTime);
        }

        // 範例書籍
        self::insertBook('The Pragmatic Programmer：From Journeyman to Master', '978-0135957059', 'Andrew Hunt & David Thomas', $seedTime);
        self::insertBook('Clean Code', '978-0132350884', 'Robert Martin', $seedTime);

        // 範例租借紀錄：admin 借走第 1 本書
        Database::execute(
            'INSERT INTO rents (id, user_id, book_id, created_at) VALUES (1, 1, 1, ?)',
            [$seedTime]
        );
    }

    private static function insertUser(string $email, string $password, string $username, string $role, int $createdAt): void
    {
        Database::execute(
            'INSERT INTO users (email, password, username, role, user_token, created_at) VALUES (?, ?, ?, ?, NULL, ?)',
            [$email, password_hash($password, PASSWORD_DEFAULT), $username, $role, $createdAt]
        );
    }

    private static function insertBook(string $name, string $isbn, string $author, int $createdAt): void
    {
        Database::execute(
            'INSERT INTO books (name, isbn, author, created_at) VALUES (?, ?, ?, ?)',
            [$name, $isbn, $author, $createdAt]
        );
    }
}

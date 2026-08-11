<?php
/**
 * 資料庫連線（PDO 單例）
 * 一律使用 prepared statement，避免 SQL injection
 */
class Database
{
    /** @var PDO|null 共用連線 */
    private static ?PDO $connection = null;

    /**
     * 取得 PDO 連線
     *
     * @param array $config config.php 中的 db 區段
     */
    public static function connection(array $config): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        self::$connection = new PDO($dsn, $config['user'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 關閉模擬預處理，讓資料型別由驅動端處理，更貼近真正的 prepared statement
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$connection;
    }
}

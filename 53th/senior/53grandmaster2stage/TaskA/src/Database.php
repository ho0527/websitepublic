<?php
/**
 * 資料庫存取（PDO 封裝）
 * 所有查詢一律使用 prepared statement，避免 SQL Injection。
 */

class Database
{
    /** @var PDO|null 單一連線實例 */
    private static ?PDO $connection = null;

    /** @var array 資料庫設定 */
    private static array $config = [];

    /**
     * 設定資料庫參數（由 bootstrap.php 呼叫）
     */
    public static function configure(array $config): void
    {
        self::$config     = $config;
        self::$connection = null;
    }

    /**
     * 取得 PDO 連線（延遲建立）
     */
    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = self::$config;
        $dsn    = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        self::$connection = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$connection;
    }

    /**
     * 取得不指定資料庫的連線，用於建立資料庫本身
     */
    public static function serverConnection(): PDO
    {
        $config = self::$config;
        $dsn    = sprintf('mysql:host=%s;port=%d;charset=%s', $config['host'], $config['port'], $config['charset']);

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    /** 取得資料庫名稱 */
    public static function databaseName(): string
    {
        return self::$config['database'];
    }

    /**
     * 執行語句並回傳 PDOStatement
     */
    public static function run(string $sql, array $bindings = []): PDOStatement
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($bindings);

        return $statement;
    }

    /** 取得單筆資料，查無資料時回傳 null */
    public static function selectOne(string $sql, array $bindings = []): ?array
    {
        $row = self::run($sql, $bindings)->fetch();

        return $row === false ? null : $row;
    }

    /** 取得多筆資料 */
    public static function select(string $sql, array $bindings = []): array
    {
        return self::run($sql, $bindings)->fetchAll();
    }

    /** 執行寫入語句並回傳影響筆數 */
    public static function execute(string $sql, array $bindings = []): int
    {
        return self::run($sql, $bindings)->rowCount();
    }

    /** 取得最後新增的自動編號 */
    public static function lastInsertId(): int
    {
        return (int) self::connection()->lastInsertId();
    }
}

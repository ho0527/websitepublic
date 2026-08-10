<?php
/**
 * 資料庫連線類別
 *
 * 以單例（Singleton）方式共用同一個 PDO 連線，
 * 並統一開啟例外模式與關閉模擬預處理，確保 prepared statement 由 MySQL 端真正處理。
 */

declare(strict_types=1);

final class Database
{
    /** @var PDO|null 共用的連線實體 */
    private static ?PDO $connection = null;

    /** 禁止外部實體化，本類別只提供靜態方法 */
    private function __construct()
    {
    }

    /**
     * 取得（必要時建立）PDO 連線。
     */
    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dataSourceName = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        self::$connection = new PDO($dataSourceName, DB_USER, DB_PASSWORD, [
            // 發生錯誤時丟出例外，避免錯誤被無聲吞掉
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // 預設以關聯陣列取回資料
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 關閉模擬預處理，讓參數繫結由資料庫端負責（防 SQL injection 更徹底）
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$connection;
    }

    /**
     * 執行一段 SQL 並回傳 PDOStatement。
     *
     * @param string               $sql        含具名或問號參數的 SQL
     * @param array<int|string,mixed> $parameters 要繫結的參數
     */
    public static function run(string $sql, array $parameters = []): PDOStatement
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement;
    }
}

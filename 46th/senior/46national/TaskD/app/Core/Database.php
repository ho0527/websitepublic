<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

/**
 * 資料庫連線管理者。
 *
 * 全站共用同一條 PDO 連線，並一律以「預備語句 + 參數綁定」執行 SQL，
 * 讓使用者輸入永遠不會被當成 SQL 語法解析，藉此防止 SQL Injection。
 */
final class Database
{
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct(array $settings)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $settings['host'],
            $settings['port'],
            $settings['name'],
            $settings['charset']
        );

        $this->connection = new PDO($dsn, $settings['user'], $settings['password'], [
            // 讓資料庫錯誤以例外拋出，避免錯誤被靜默忽略
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 關閉模擬預備語句，改由資料庫端真正做參數綁定
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    /**
     * 取得唯一的資料庫實體。
     */
    public static function instance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self(Config::get('database'));
        }

        return self::$instance;
    }

    /**
     * 執行 SQL 並回傳語句物件。
     *
     * @param array<int|string, mixed> $bindings 綁定參數
     */
    public function execute(string $sql, array $bindings = []): PDOStatement
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($bindings);

        return $statement;
    }

    /**
     * 取得所有符合條件的資料列。
     *
     * @param array<int|string, mixed> $bindings
     * @return array<int, array<string, mixed>>
     */
    public function select(string $sql, array $bindings = []): array
    {
        return $this->execute($sql, $bindings)->fetchAll();
    }

    /**
     * 取得第一筆資料列，沒有資料時回傳 null。
     *
     * @param array<int|string, mixed> $bindings
     * @return array<string, mixed>|null
     */
    public function selectOne(string $sql, array $bindings = []): ?array
    {
        $row = $this->execute($sql, $bindings)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * 取得最後一次新增資料的自動遞增編號。
     */
    public function lastInsertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }

    /**
     * 以交易包住一段資料庫操作，任何例外都會使整段操作回復。
     */
    public function transaction(callable $callback): mixed
    {
        $this->connection->beginTransaction();

        try {
            $result = $callback($this);
            $this->connection->commit();

            return $result;
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            throw $exception;
        }
    }
}

<?php
/**
 * 資料庫連線（PDO 單例）
 *
 * 所有查詢一律使用 prepared statement 傳參，杜絕 SQL injection。
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

final class Database
{
    /** 單一共用實例 */
    private static ?Database $instance = null;

    private PDO $pdo;

    private function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
                // 以例外回報錯誤，方便統一處理
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // 關閉模擬預處理，讓資料庫端真正做參數繫結
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('資料庫連線失敗：' . $e->getMessage(), 0, $e);
        }
    }

    /** 取得（必要時建立）共用連線 */
    public static function instance(?array $config = null): Database
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new RuntimeException('第一次取得資料庫連線時必須提供設定。');
            }
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /** 執行帶參數的查詢並回傳 statement */
    public function run(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    /** 取回全部資料列 */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /** 取回單一資料列，查無資料回傳 null */
    public function one(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /** 取回單一欄位值 */
    public function value(string $sql, array $params = []): mixed
    {
        $value = $this->run($sql, $params)->fetchColumn();

        return $value === false ? null : $value;
    }

    /** 最後一次 INSERT 產生的主鍵 */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}

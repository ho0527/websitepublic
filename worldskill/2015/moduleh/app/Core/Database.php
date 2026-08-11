<?php
namespace App\Core;

use PDO;
use PDOStatement;

/**
 * 資料庫連線單例（PDO）
 * 全部查詢一律使用 prepared statement，避免 SQL injection。
 */
class Database
{
    /** @var Database|null 單例實體 */
    private static ?Database $instance = null;

    /** @var PDO PDO 連線 */
    private PDO $pdo;

    /**
     * @param array $config 由 app/config.php 傳入的 db 設定
     */
    private function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // 關閉模擬預處理，確保由資料庫端真正做參數綁定
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    /**
     * 取得（並在第一次呼叫時建立）資料庫單例
     */
    public static function getInstance(?array $config = null): Database
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new \RuntimeException('資料庫尚未初始化，第一次呼叫必須傳入設定。');
            }
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * 取得原生 PDO 物件（交易控制用）
     */
    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * 執行一段 SQL（自動以 prepared statement 綁定參數）
     *
     * @param string $sql    含具名或問號佔位符的 SQL
     * @param array  $params 綁定參數
     */
    public function run(string $sql, array $params = []): PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement;
    }

    /**
     * 取回多筆資料
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /**
     * 取回單筆資料，查無資料時回傳 null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();

        return $row === false ? null : $row;
    }

    /**
     * 取回單一欄位值
     */
    public function fetchColumn(string $sql, array $params = [])
    {
        return $this->run($sql, $params)->fetchColumn();
    }

    /**
     * 取得最後一次 INSERT 的自動編號
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}

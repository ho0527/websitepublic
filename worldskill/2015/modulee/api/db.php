<?php
/**
 * 資料庫連線與結構初始化
 * 結構取自主辦單位提供的 Media/SQL/competitorXX_db01.sql
 * （difficult / ranking 兩張表、ranking.difficult_id 外鍵）
 */

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'worldskill2015_modulee';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * 取得 PDO 連線；資料庫或資料表不存在時自動建立。
 *
 * @return PDO
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 先連到伺服器（不指定資料庫）以便必要時建立資料庫
    $root = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4', DB_USER, DB_PASS, $options);
    $root->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        $options
    );

    ensureSchema($pdo);

    return $pdo;
}

/**
 * 建立資料表與難度基本資料（僅在不存在時執行）。
 */
function ensureSchema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `difficult` (
            `id`   INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(155) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `ranking` (
            `id`           INT(11) NOT NULL AUTO_INCREMENT,
            `name`         VARCHAR(155) NOT NULL,
            `difficult_id` INT(11) NOT NULL,
            `time`         TIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `difficult_id` (`difficult_id`),
            CONSTRAINT `ranking_ibfk_1` FOREIGN KEY (`difficult_id`)
                REFERENCES `difficult` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $count = (int) $pdo->query('SELECT COUNT(*) FROM `difficult`')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO `difficult` (`id`, `name`) VALUES (?, ?)');
        foreach ([1 => 'EASY', 2 => 'MEDIUM', 3 => 'HARD'] as $id => $name) {
            $stmt->execute([$id, $name]);
        }
    }
}

/**
 * 以 JSON 回應並結束程式。
 */
function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

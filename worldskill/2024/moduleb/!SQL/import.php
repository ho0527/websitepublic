<?php
/**
 * 匯入 schema.sql 的小工具
 *
 * 因為這台主機沒有 mysql 命令列工具，改用 PHP + PDO 直接把 schema.sql 跑一次。
 * 使用方式（在本資料夾執行）：
 *     php import.php
 *
 * 注意：schema.sql 會先 DROP DATABASE 再重建，執行後既有資料會被清空。
 */

declare(strict_types=1);

require __DIR__ . '/../config.php';

/**
 * 以引號感知的方式切割 SQL 敘述，避免字串內容中的分號被誤判為敘述結尾。
 *
 * @return array<int,string>
 */
function splitSqlStatements(string $sql): array
{
    $statements       = [];
    $current          = '';
    $quoteCharacter   = null;
    $length           = strlen($sql);

    for ($index = 0; $index < $length; $index++) {
        $character = $sql[$index];

        if ($quoteCharacter !== null) {
            $current .= $character;

            if ($character === '\\' && $index + 1 < $length) {
                // 反斜線跳脫，把下一個字元一併吃掉
                $current .= $sql[++$index];
                continue;
            }

            if ($character === $quoteCharacter) {
                $quoteCharacter = null;
            }

            continue;
        }

        if ($character === "'" || $character === '"' || $character === '`') {
            $quoteCharacter = $character;
            $current       .= $character;
            continue;
        }

        // 單行註解：整行略過
        if ($character === '-' && substr($sql, $index, 3) === '-- ') {
            $lineEnd = strpos($sql, "\n", $index);
            $index   = $lineEnd === false ? $length : $lineEnd;
            continue;
        }

        if ($character === ';') {
            $statements[] = trim($current);
            $current      = '';
            continue;
        }

        $current .= $character;
    }

    if (trim($current) !== '') {
        $statements[] = trim($current);
    }

    return array_values(array_filter($statements, static fn (string $statement): bool => $statement !== ''));
}

$sqlFilePath = __DIR__ . '/schema.sql';
$sqlContent  = file_get_contents($sqlFilePath);

if ($sqlContent === false) {
    fwrite(STDERR, "無法讀取 {$sqlFilePath}\n");
    exit(1);
}

// 連線時不指定資料庫，因為 schema.sql 會自己建立資料庫
$pdo = new PDO(
    sprintf('mysql:host=%s;port=%d;charset=%s', DB_HOST, DB_PORT, DB_CHARSET),
    DB_USER,
    DB_PASSWORD,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$statements = splitSqlStatements($sqlContent);

foreach ($statements as $statementIndex => $statement) {
    try {
        $pdo->exec($statement);
    } catch (PDOException $exception) {
        fwrite(STDERR, sprintf(
            "第 %d 個敘述執行失敗：%s\nSQL: %s\n",
            $statementIndex + 1,
            $exception->getMessage(),
            substr($statement, 0, 200)
        ));
        exit(1);
    }
}

echo '匯入完成，共執行 ' . count($statements) . " 個 SQL 敘述。\n";

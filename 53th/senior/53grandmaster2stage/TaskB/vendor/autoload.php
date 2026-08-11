<?php
/**
 * 精簡版自動載入器
 *
 * 本機為離線環境，無法執行 composer install，因此 PHPUnit 10.1.2 與其相依套件
 * 是直接放進 vendor 目錄，並以此檔案取代 composer 產生的自動載入器。
 * autoload_classmap.php 由建置腳本掃描所有套件原始碼後產生（類別 => 檔案）。
 */

declare(strict_types=1);

$vendorDirectory = __DIR__;

/** @var array<string, string> 類別對應表 */
$classMap = require $vendorDirectory . '/autoload_classmap.php';

spl_autoload_register(static function (string $class) use ($classMap, $vendorDirectory): void {
    if (isset($classMap[$class])) {
        require_once $vendorDirectory . '/' . $classMap[$class];
    }
});

// 需要無條件載入的輔助函式檔
foreach (require $vendorDirectory . '/autoload_files.php' as $file) {
    require_once $vendorDirectory . '/' . $file;
}

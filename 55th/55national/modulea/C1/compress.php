<?php
/**
 * C1: Folder Zip 資料夾壓縮（WorldSkills 2024 Module A）
 *
 * 將使用者選取的資料夾壓縮成 zip 後自動下載：
 *  - zip 檔名與上傳的資料夾同名
 *  - 保留資料夾內的階層結構
 *  - 空的子資料夾不會被包進壓縮檔（只加入實際檔案，因此空目錄自然不會出現）
 */

declare(strict_types=1);

/** 以純文字回報錯誤並結束 */
function fail(string $message): void
{
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

if (empty($_FILES['folder']) || !is_array($_FILES['folder']['name'])) {
    fail('沒有收到任何檔案，請選擇一個資料夾。');
}

$names     = $_FILES['folder']['name'];
$tmpNames  = $_FILES['folder']['tmp_name'];
$errors    = $_FILES['folder']['error'];
// PHP 8.1 起提供 full_path，內含瀏覽器上傳時的相對路徑（例如 test/subfolder/word.docx）
$fullPaths = $_FILES['folder']['full_path'] ?? [];

/** 收集「相對路徑 => 暫存檔」，同時找出最外層的資料夾名稱 */
$entries    = [];
$rootFolder = '';

foreach ($names as $index => $name) {
    if ($errors[$index] !== UPLOAD_ERR_OK || !is_uploaded_file($tmpNames[$index])) {
        continue;
    }

    // 優先使用 full_path 保留階層，取不到時退回單一檔名
    $relativePath = $fullPaths[$index] ?? $name;
    $relativePath = str_replace('\\', '/', (string) $relativePath);
    // 去掉 ../ 之類的危險片段
    $parts = array_values(array_filter(explode('/', $relativePath), static function (string $part): bool {
        return $part !== '' && $part !== '.' && $part !== '..';
    }));
    if ($parts === []) {
        continue;
    }

    if ($rootFolder === '' && count($parts) > 1) {
        $rootFolder = $parts[0];
    }

    $entries[implode('/', $parts)] = $tmpNames[$index];
}

if ($entries === []) {
    fail('這個資料夾是空的，請選擇含有檔案的資料夾。');
}

if ($rootFolder === '') {
    $rootFolder = 'folder';
}

// 產生壓縮檔（放在系統暫存目錄，回應完成後刪除）
$zipPath = tempnam(sys_get_temp_dir(), 'folderzip_');
$zip = new ZipArchive();
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fail('無法建立壓縮檔。');
}

foreach ($entries as $relativePath => $tmpName) {
    // 只加入檔案，空的子資料夾因此不會被包進來
    $zip->addFile($tmpName, $relativePath);
}
$zip->close();

// 以資料夾名稱作為下載檔名並自動下載
$downloadName = $rootFolder . '.zip';

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
header('Content-Length: ' . filesize($zipPath));
header('Cache-Control: no-store');
readfile($zipPath);
unlink($zipPath);

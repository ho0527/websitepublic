<?php
/**
 * 媒體庫：直接以 uploads/ 目錄為資料來源
 *
 * 精選圖片、相簿與登入頁背景都從這裡挑選檔案。
 */

declare(strict_types=1);

namespace App\Model;

final class MediaLibrary
{
    private const ALLOWED = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    public function __construct(private string $basePath)
    {
    }

    /**
     * 列出 uploads/ 之下所有圖片（相對於網站根目錄的路徑）
     *
     * @return string[] 例如 uploads/museums/hermitage-1.png
     */
    public function images(): array
    {
        $root = $this->basePath . '/uploads';
        if (!is_dir($root)) {
            return [];
        }

        $files    = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (!$file->isFile()) {
                continue;
            }
            if (!in_array(strtolower($file->getExtension()), self::ALLOWED, true)) {
                continue;
            }

            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($this->basePath) + 1));
            $files[]  = $relative;
        }

        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        return $files;
    }

    /**
     * 處理後台上傳，成功回傳相對路徑，失敗回傳 null
     *
     * @param array $file $_FILES 的單一項目
     */
    public function store(array $file, string $subDirectory = 'museums'): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED, true)) {
            return null;
        }

        // 以副檔名之外的實際內容再確認一次是不是圖片
        if ($extension !== 'svg' && @getimagesize($file['tmp_name']) === false) {
            return null;
        }

        $subDirectory = preg_replace('/[^a-z0-9_-]/i', '', $subDirectory) ?: 'museums';
        $targetDir    = $this->basePath . '/uploads/' . $subDirectory;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $base = pathinfo((string) $file['name'], PATHINFO_FILENAME);
        $base = preg_replace('/[^a-z0-9._-]+/i', '-', $base) ?: 'image';
        $name = strtolower(trim($base, '-')) . '-' . date('YmdHis') . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $targetDir . '/' . $name)) {
            return null;
        }

        return 'uploads/' . $subDirectory . '/' . $name;
    }

    public function count(): int
    {
        return count($this->images());
    }
}

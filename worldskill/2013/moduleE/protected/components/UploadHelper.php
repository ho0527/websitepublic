<?php
/**
 * 檔案上傳共用處理。
 */
class UploadHelper
{
    /**
     * 儲存上傳檔案並回傳實際存放的檔名。
     *
     * @param CUploadedFile $file      上傳的檔案
     * @param string        $directory 目的資料夾（絕對路徑）
     * @return string|null 成功時回傳檔名，否則 null
     */
    public static function save($file, $directory)
    {
        if (!($file instanceof CUploadedFile)) {
            return null;
        }
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = self::uniqueName($file->getName(), $directory);
        if ($file->saveAs($directory . DIRECTORY_SEPARATOR . $fileName)) {
            return $fileName;
        }
        return null;
    }

    /**
     * 若目的地已有同名檔案，於檔名後加上流水號避免覆蓋。
     */
    private static function uniqueName($originalName, $directory)
    {
        // 僅保留安全字元
        $originalName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($originalName));
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $stem      = pathinfo($originalName, PATHINFO_FILENAME);

        $candidate = $originalName;
        $counter   = 1;
        while (file_exists($directory . DIRECTORY_SEPARATOR . $candidate)) {
            $candidate = $stem . '_' . $counter . ($extension === '' ? '' : '.' . $extension);
            $counter++;
        }

        // 資料表欄位長度上限為 50
        if (strlen($candidate) > 50) {
            $candidate = substr($stem, 0, 40) . '_' . $counter . ($extension === '' ? '' : '.' . $extension);
        }
        return $candidate;
    }

    /**
     * 刪除既有檔案（預設 avatar.png 與題目提供的路線圖不刪除）。
     */
    public static function remove($fileName, $directory, array $protected = array())
    {
        if ($fileName === null || $fileName === '' || in_array($fileName, $protected, true)) {
            return;
        }
        $path = $directory . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($path)) {
            @unlink($path);
        }
    }
}

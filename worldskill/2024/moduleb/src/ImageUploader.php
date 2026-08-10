<?php
/**
 * 產品圖片上傳處理
 *
 * 圖片實際存放在 media/uploads/，資料庫只保存檔名。
 * 檔名由程式重新產生，避免使用者上傳的檔名造成路徑穿越或覆蓋既有檔案。
 */

declare(strict_types=1);

final class ImageUploader
{
    /**
     * 處理一次上傳。
     *
     * @param array<string,mixed>|null $uploadedFile $_FILES 的其中一個元素
     * @param array<int,string>        $errors       驗證失敗時會把訊息附加進來（傳參考）
     * @return string|null 成功時回傳存放的檔名；沒有選擇檔案或失敗時回傳 null
     */
    public static function store(?array $uploadedFile, array &$errors): ?string
    {
        // 沒有選檔案：UPLOAD_ERR_NO_FILE，直接視為「不變更圖片」
        if ($uploadedFile === null || ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed (error code ' . (int) $uploadedFile['error'] . ').';

            return null;
        }

        if ((int) $uploadedFile['size'] > MAX_IMAGE_SIZE_BYTES) {
            $errors[] = 'Image is too large, the limit is ' . (int) (MAX_IMAGE_SIZE_BYTES / 1024 / 1024) . ' MB.';

            return null;
        }

        $extension = strtolower(pathinfo((string) $uploadedFile['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS, true)) {
            $errors[] = 'Only ' . implode(', ', ALLOWED_IMAGE_EXTENSIONS) . ' images are allowed.';

            return null;
        }

        // SVG 無法用 getimagesize 檢查，其餘格式再確認一次真的是圖片
        if ($extension !== 'svg' && @getimagesize((string) $uploadedFile['tmp_name']) === false) {
            $errors[] = 'The uploaded file is not a valid image.';

            return null;
        }

        if (!is_dir(UPLOAD_DIRECTORY) && !mkdir(UPLOAD_DIRECTORY, 0777, true) && !is_dir(UPLOAD_DIRECTORY)) {
            $errors[] = 'Upload folder cannot be created.';

            return null;
        }

        $storedFileName = sprintf('product_%s_%s.%s', date('YmdHis'), bin2hex(random_bytes(4)), $extension);
        $targetPath     = UPLOAD_DIRECTORY . '/' . $storedFileName;

        // php -S 內建伺服器與 fastcgi 都支援 move_uploaded_file
        if (!move_uploaded_file((string) $uploadedFile['tmp_name'], $targetPath)) {
            $errors[] = 'Cannot save the uploaded image.';

            return null;
        }

        return $storedFileName;
    }

    /**
     * 刪除實體圖片檔（資料庫欄位由呼叫端負責清空）。
     */
    public static function removeStoredFile(?string $fileName): void
    {
        if ($fileName === null || $fileName === '') {
            return;
        }

        // 只允許刪除上傳資料夾內的檔案，basename() 可擋掉 ../ 之類的路徑
        $targetPath = UPLOAD_DIRECTORY . '/' . basename($fileName);

        if (is_file($targetPath)) {
            unlink($targetPath);
        }
    }

    /**
     * 取得圖片的對外網址；沒有圖片時回傳預設佔位圖。
     */
    public static function publicUrl(?string $fileName): string
    {
        if ($fileName === null || $fileName === '') {
            return assetUrl('media/placeholder.svg');
        }

        return assetUrl('media/uploads/' . rawurlencode(basename($fileName)));
    }
}

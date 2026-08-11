<?php
/**
 * 圖片上傳處理
 * 負責檢查是否為合法圖片、搬移檔案並回傳相對路徑
 */
class ImageService
{
    /** @var array 系統設定 */
    private array $config;

    /** @var string 模組根目錄的絕對路徑 */
    private string $moduleRoot;

    public function __construct(array $config, string $moduleRoot)
    {
        $this->config     = $config;
        $this->moduleRoot = rtrim(str_replace('\\', '/', $moduleRoot), '/');
    }

    /**
     * 儲存上傳的圖片
     *
     * @param array $files Request::files() 取得的檔案清單
     * @return string[] 相對於模組根目錄的圖片路徑
     * @throws ApiException MSG_IMAGE_CAN_NOT_PROCESS (400)
     */
    public function store(array $files): array
    {
        if ($files === []) {
            return [];
        }

        $uploadDirectory = $this->moduleRoot . '/' . trim($this->config['upload_dir'], '/');
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $paths = [];

        foreach ($files as $file) {
            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                throw new ApiException('MSG_IMAGE_CAN_NOT_PROCESS', 400);
            }

            $temporaryPath = $file['tmp_name'] ?? '';
            if ($temporaryPath === '' || !is_file($temporaryPath)) {
                throw new ApiException('MSG_IMAGE_CAN_NOT_PROCESS', 400);
            }

            // 以實際內容判斷是否為圖片，副檔名可以偽造因此不能只看檔名
            $imageInfo = @getimagesize($temporaryPath);
            if ($imageInfo === false) {
                throw new ApiException('MSG_IMAGE_CAN_NOT_PROCESS', 400);
            }

            $extension = $this->extensionFromImageType((int) $imageInfo[2]);
            if ($extension === null || !in_array($extension, $this->config['allow_image_ext'], true)) {
                throw new ApiException('MSG_IMAGE_CAN_NOT_PROCESS', 400);
            }

            $fileName    = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
            $destination = $uploadDirectory . '/' . $fileName;

            // 由 MultipartParser 產生的暫存檔並非 PHP 上傳檔案，不能使用 move_uploaded_file
            $moved = !empty($file['is_stream'])
                ? rename($temporaryPath, $destination)
                : (@move_uploaded_file($temporaryPath, $destination) ?: @rename($temporaryPath, $destination));

            if ($moved === false) {
                throw new ApiException('MSG_IMAGE_CAN_NOT_PROCESS', 400);
            }

            $paths[] = trim($this->config['upload_dir'], '/') . '/' . $fileName;
        }

        return $paths;
    }

    /** 依 getimagesize 回傳的型別代碼取得副檔名 */
    private function extensionFromImageType(int $imageType): ?string
    {
        $map = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_GIF  => 'gif',
            IMAGETYPE_WEBP => 'webp',
        ];

        return $map[$imageType] ?? null;
    }

    /** 刪除模組目錄下的圖片檔 */
    public function remove(string $relativePath): void
    {
        $fullPath = $this->moduleRoot . '/' . ltrim($relativePath, '/');

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}

<?php
/**
 * multipart/form-data 解析器
 * PHP 只會自動解析 POST 的 multipart，PUT / DELETE 需要自行處理，
 * 題目的「編輯房屋」API 為 [PUT] + multipart/form-data，因此必須支援。
 */
class MultipartParser
{
    /**
     * 解析 multipart 內容
     *
     * @param string $raw      原始請求主體
     * @param string $boundary 分隔字串
     * @return array{0: array, 1: array} [一般欄位, 檔案欄位]
     */
    public static function parse(string $raw, string $boundary): array
    {
        $fields = [];
        $files  = [];

        // 以分隔字串切開各個區段，第一段與最後一段為空白或結束標記
        $blocks = preg_split('/\r?\n?--' . preg_quote($boundary, '/') . '(--)?\r?\n?/', $raw);
        if ($blocks === false) {
            return [$fields, $files];
        }

        foreach ($blocks as $block) {
            if (trim($block) === '') {
                continue;
            }

            // 標頭與內容以空行分隔
            $separatorPosition = strpos($block, "\r\n\r\n");
            $separatorLength   = 4;
            if ($separatorPosition === false) {
                $separatorPosition = strpos($block, "\n\n");
                $separatorLength   = 2;
            }
            if ($separatorPosition === false) {
                continue;
            }

            $headerText = substr($block, 0, $separatorPosition);
            $content    = substr($block, $separatorPosition + $separatorLength);

            // 去掉內容尾端多餘的換行
            $content = preg_replace('/\r?\n$/', '', $content) ?? $content;

            if (!preg_match('/name="([^"]*)"/i', $headerText, $nameMatch)) {
                continue;
            }
            $name = $nameMatch[1];

            if (preg_match('/filename="([^"]*)"/i', $headerText, $fileMatch)) {
                // 檔案欄位：寫入暫存檔，之後的處理方式與 $_FILES 相同
                $fileName = $fileMatch[1];
                if ($fileName === '') {
                    continue;
                }

                $mimeType = 'application/octet-stream';
                if (preg_match('/Content-Type:\s*([^\r\n]+)/i', $headerText, $typeMatch)) {
                    $mimeType = trim($typeMatch[1]);
                }

                $temporaryPath = tempnam(sys_get_temp_dir(), 'wsd51');
                file_put_contents($temporaryPath, $content);

                $files[self::fieldName($name)][] = [
                    'name'      => $fileName,
                    'type'      => $mimeType,
                    'tmp_name'  => $temporaryPath,
                    'error'     => UPLOAD_ERR_OK,
                    'size'      => strlen($content),
                    'is_stream' => true, // 非 PHP 上傳機制產生，搬移時不可用 move_uploaded_file
                ];
                continue;
            }

            // 一般欄位，支援 foo[] 陣列寫法
            if (substr($name, -2) === '[]') {
                $fields[self::fieldName($name)][] = $content;
            } else {
                $fields[$name] = $content;
            }
        }

        return [$fields, $files];
    }

    /** 去除欄位名稱尾端的 []，與 PHP 的處理方式一致 */
    private static function fieldName(string $name): string
    {
        return substr($name, -2) === '[]' ? substr($name, 0, -2) : $name;
    }
}

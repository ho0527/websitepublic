<?php
/**
 * 統一的 JSON 回應輸出
 * 題目規範格式：{ "success": bool, "message": string, "data": mixed }
 */
class Response
{
    /**
     * 輸出成功回應
     *
     * @param mixed $data 回應資料，沒有資料時題目規範為空字串
     */
    public static function success($data = '', int $statusCode = 200): void
    {
        self::send(true, '', $data, $statusCode);
    }

    /**
     * 輸出失敗回應
     *
     * @param string $messageCode 錯誤訊息代碼
     * @param int    $statusCode  HTTP 狀態碼
     * @param mixed  $data        附加資料
     */
    public static function error(string $messageCode, int $statusCode, $data = ''): void
    {
        self::send(false, $messageCode, $data, $statusCode);
    }

    /** 實際輸出 JSON 並結束程式 */
    private static function send(bool $success, string $message, $data, int $statusCode): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            // 允許前端以其他來源存取（同機器不同埠時仍可運作）
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type, X-User-Token, X-HTTP-Method-Override, Accept');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        }

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

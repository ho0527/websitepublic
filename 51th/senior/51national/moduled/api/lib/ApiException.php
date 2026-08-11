<?php
/**
 * API 錯誤例外
 * 以「錯誤訊息代碼 + HTTP 狀態碼」表示題目規範中的各種錯誤情境
 */
class ApiException extends Exception
{
    /** @var int HTTP 狀態碼 */
    private int $statusCode;

    /** @var mixed 附加資料（例如缺少的欄位名稱），預設為空字串 */
    private $payload;

    /**
     * @param string $messageCode 題目定義的錯誤訊息代碼，如 MSG_INVALID_TOKEN
     * @param int    $statusCode  HTTP 狀態碼
     * @param mixed  $payload     附加資料
     */
    public function __construct(string $messageCode, int $statusCode, $payload = '')
    {
        parent::__construct($messageCode);
        $this->statusCode = $statusCode;
        $this->payload    = $payload;
    }

    /** 取得 HTTP 狀態碼 */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** 取得附加資料 */
    public function getPayload()
    {
        return $this->payload;
    }
}

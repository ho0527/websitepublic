<?php
/**
 * 請求解析
 * 負責處理 JSON、application/x-www-form-urlencoded、multipart/form-data
 * 其中 PUT / DELETE 的 multipart 與 urlencoded 內容 PHP 不會自動解析，於此自行處理
 */
class Request
{
    /** @var string HTTP 方法 */
    private string $method;

    /** @var string 路由路徑，例如 /user/login */
    private string $path;

    /** @var array 網址查詢字串參數 */
    private array $query;

    /** @var array 請求主體欄位 */
    private array $body = [];

    /** @var array 上傳檔案，格式同 $_FILES 的單一檔案陣列集合 */
    private array $files = [];

    public function __construct()
    {
        $this->method = $this->resolveMethod();
        $this->path   = $this->resolvePath();
        $this->query  = $_GET;
        $this->parseBody();
    }

    /** 取得 HTTP 方法（支援 _method 與 X-HTTP-Method-Override 覆寫） */
    private function resolveMethod(): string
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? ($_POST['_method'] ?? '');
            if ($override !== '') {
                $method = strtoupper($override);
            }
        }

        return $method;
    }

    /**
     * 取得路由路徑
     * 同時支援 PATH_INFO（api/index.php/user/login）與乾淨網址（api/user/login）
     */
    private function resolvePath(): string
    {
        $pathInfo = $_SERVER['PATH_INFO'] ?? '';

        if ($pathInfo === '') {
            // 乾淨網址模式：從 REQUEST_URI 去掉 api 目錄前綴
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
            $scriptDir  = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            if ($scriptDir !== '' && strpos($requestUri, $scriptDir) === 0) {
                $pathInfo = substr($requestUri, strlen($scriptDir));
            } else {
                $pathInfo = $requestUri;
            }
            // 若網址直接指到 index.php，去除檔名部分
            $pathInfo = preg_replace('#^/index\.php#', '', $pathInfo) ?? '';
        }

        return '/' . trim($pathInfo, '/');
    }

    /** 解析請求主體 */
    private function parseBody(): void
    {
        $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '');

        // PHP 已自動解析的情況（POST + form-data / urlencoded）
        if (!empty($_POST) || !empty($_FILES)) {
            $this->body  = $_POST;
            $this->files = $this->normalizeFiles($_FILES);
            return;
        }

        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return;
        }

        if (strpos($contentType, 'application/json') !== false) {
            $decoded = json_decode($raw, true);
            $this->body = is_array($decoded) ? $decoded : [];
            return;
        }

        if (strpos($contentType, 'multipart/form-data') !== false) {
            // PUT/DELETE 的 multipart 需自行解析
            $boundary = '';
            if (preg_match('/boundary=(?:"([^"]+)"|([^;]+))/i', $_SERVER['CONTENT_TYPE'] ?? '', $matches)) {
                $boundary = $matches[1] !== '' ? $matches[1] : $matches[2];
            }
            if ($boundary !== '') {
                [$fields, $files] = MultipartParser::parse($raw, trim($boundary));
                $this->body  = $fields;
                $this->files = $files;
            }
            return;
        }

        // 其餘視為 urlencoded
        parse_str($raw, $parsed);
        if (is_array($parsed)) {
            $this->body = $parsed;
        }

        // 也可能是未帶 Content-Type 的 JSON
        if ($this->body === [] || array_keys($this->body) === [0]) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->body = $decoded;
            }
        }
    }

    /**
     * 將 $_FILES 轉為統一格式：欄位名 => 檔案陣列清單
     * 每個檔案為 ['name' =>, 'type' =>, 'tmp_name' =>, 'error' =>, 'size' =>]
     */
    private function normalizeFiles(array $files): array
    {
        $result = [];

        foreach ($files as $field => $info) {
            if (is_array($info['name'])) {
                $count = count($info['name']);
                for ($i = 0; $i < $count; $i++) {
                    $result[$field][] = [
                        'name'     => $info['name'][$i],
                        'type'     => $info['type'][$i],
                        'tmp_name' => $info['tmp_name'][$i],
                        'error'    => $info['error'][$i],
                        'size'     => $info['size'][$i],
                    ];
                }
            } else {
                $result[$field][] = $info;
            }
        }

        return $result;
    }

    /** HTTP 方法 */
    public function method(): string
    {
        return $this->method;
    }

    /** 路由路徑 */
    public function path(): string
    {
        return $this->path;
    }

    /** 取得查詢字串參數 */
    public function query(string $key, $default = null)
    {
        return array_key_exists($key, $this->query) ? $this->query[$key] : $default;
    }

    /** 取得主體欄位 */
    public function input(string $key, $default = null)
    {
        return array_key_exists($key, $this->body) ? $this->body[$key] : $default;
    }

    /** 主體是否含有該欄位 */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->body);
    }

    /** 取得全部主體欄位 */
    public function all(): array
    {
        return $this->body;
    }

    /** 取得指定欄位的上傳檔案清單 */
    public function files(string $field): array
    {
        return $this->files[$field] ?? [];
    }

    /** 取得驗證用的 Token（X-User-Token 標頭，並相容 Authorization: Bearer） */
    public function token(): string
    {
        $token = $_SERVER['HTTP_X_USER_TOKEN'] ?? '';

        if ($token === '') {
            $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (stripos($authorization, 'bearer ') === 0) {
                $token = substr($authorization, 7);
            }
        }

        return trim((string) $token);
    }
}

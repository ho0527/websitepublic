<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 封裝一次 HTTP 請求。
 *
 * 所有取值都經過型別轉換與修剪，控制器不直接碰觸 $_GET / $_POST 超全域變數。
 */
final class Request
{
    /** @var array<string, mixed> */
    private array $queryParameters;

    /** @var array<string, mixed> */
    private array $bodyParameters;

    private string $method;

    private string $path;

    private string $basePath;

    private function __construct(
        array $queryParameters,
        array $bodyParameters,
        string $method,
        string $path,
        string $basePath
    ) {
        $this->queryParameters = $queryParameters;
        $this->bodyParameters  = $bodyParameters;
        $this->method          = $method;
        $this->path            = $path;
        $this->basePath        = $basePath;
    }

    /**
     * 由目前的超全域變數建立請求物件。
     */
    public static function capture(): Request
    {
        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

        // 網址重寫後真正的路徑放在 route 參數，直接開啟 index.php 時則退回 REQUEST_URI
        $rawPath = $_GET['route'] ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $rawPath = (string) $rawPath;

        if ($basePath !== '' && str_starts_with($rawPath, $basePath)) {
            $rawPath = substr($rawPath, strlen($basePath));
        }

        $path  = '/' . trim(rawurldecode($rawPath), '/');
        $query = $_GET;
        unset($query['route']);

        return new self(
            $query,
            $_POST,
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            $basePath
        );
    }

    /**
     * 請求方法（GET / POST）。
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * 去掉安裝路徑後的請求路徑，例如 /train-info/1101。
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * 網站安裝的基底路徑，用來組出正確的連結。
     */
    public function basePath(): string
    {
        return $this->basePath;
    }

    /**
     * 是否為 POST 請求。
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * 取得查詢字串參數（已修剪前後空白）。
     */
    public function query(string $key, ?string $default = null): ?string
    {
        $value = $this->queryParameters[$key] ?? null;

        return is_string($value) ? trim($value) : $default;
    }

    /**
     * 取得表單欄位（已修剪前後空白）。
     */
    public function input(string $key, ?string $default = null): ?string
    {
        $value = $this->bodyParameters[$key] ?? $this->queryParameters[$key] ?? null;

        return is_string($value) ? trim($value) : $default;
    }

    /**
     * 取得整數型別的欄位。
     */
    public function integer(string $key, int $default = 0): int
    {
        $value = $this->input($key);

        return $value === null || $value === '' ? $default : (int) $value;
    }

    /**
     * 取得陣列型別的欄位（例如複選的行經車站）。
     *
     * @return array<int, string>
     */
    public function arrayInput(string $key): array
    {
        $value = $this->bodyParameters[$key] ?? $this->queryParameters[$key] ?? [];

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(static fn ($item): string => trim((string) $item), $value));
    }

    /**
     * 判斷欄位是否有填寫內容。
     */
    public function filled(string $key): bool
    {
        $value = $this->input($key);

        return $value !== null && $value !== '';
    }

    /**
     * 全部的查詢字串參數。
     *
     * @return array<string, mixed>
     */
    public function allQuery(): array
    {
        return $this->queryParameters;
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 樣板渲染器。
 *
 * 樣板一律透過 e() 輸出動態內容，把 HTML 的特殊字元轉為實體，
 * 使用者輸入即使含有 <script> 也只會被當成文字顯示，藉此防止 XSS。
 */
final class View
{
    private Request $request;

    /** @var array<string, mixed> 每個頁面共用的資料 */
    private array $sharedData = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 加入所有樣板都能取用的共用資料。
     *
     * @param array<string, mixed> $data
     */
    public function share(array $data): void
    {
        $this->sharedData = array_merge($this->sharedData, $data);
    }

    /**
     * 渲染頁面（含共用版型）。
     *
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], string $layout = 'layouts/app'): void
    {
        $content = $this->capture($template, $data);

        echo $this->capture($layout, array_merge($data, ['content' => $content]));
    }

    /**
     * 只渲染片段而不套用版型，供 AJAX 回應使用。
     *
     * @param array<string, mixed> $data
     */
    public function partial(string $template, array $data = []): string
    {
        return $this->capture($template, $data);
    }

    /**
     * 將樣板輸出擷取為字串。
     *
     * @param array<string, mixed> $data
     */
    private function capture(string $template, array $data): string
    {
        // 這裡的區域變數一律加上 __ 前綴，避免與樣板資料的鍵名互相覆蓋
        $__file = dirname(__DIR__, 2) . '/resources/views/' . $template . '.php';

        if (!is_file($__file)) {
            throw new \RuntimeException(sprintf('找不到樣板檔案：%s', $template));
        }

        // 在樣板內可直接使用 $view、$request 以及傳入的資料
        extract(array_merge($this->sharedData, $data), EXTR_OVERWRITE);

        $view    = $this;
        $request = $this->request;

        ob_start();
        require $__file;

        return (string) ob_get_clean();
    }

    /**
     * 輸出安全的文字內容（防止 XSS）。
     */
    public function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * 組出網站內的絕對路徑連結。
     */
    public function url(string $path = ''): string
    {
        return $this->request->basePath() . '/' . ltrim($path, '/');
    }

    /**
     * 組出靜態資源的連結，並在後面附上檔案的最後修改時間做為版本號。
     *
     * 檔案一改，網址就跟著改，瀏覽器與中間的 CDN 都會視為新資源重新抓取，
     * 不會發生「程式已更新、使用者卻仍看到舊樣式」的情況。
     */
    public function asset(string $path): string
    {
        $relativePath = 'assets/' . ltrim($path, '/');
        $url          = $this->url($relativePath);
        $file         = dirname(__DIR__, 2) . '/' . $relativePath;

        return is_file($file) ? $url . '?v=' . filemtime($file) : $url;
    }
}

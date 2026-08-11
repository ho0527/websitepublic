<?php
namespace App\Core;

/**
 * 極簡樣板引擎：把 PHP 樣板檔算繪成字串，並包進共用版面。
 * 所有輸出一律經過 View::e()（htmlspecialchars）跳脫，避免 XSS。
 */
class View
{
    /** @var string 樣板檔所在目錄 */
    private string $viewPath;

    public function __construct(string $viewPath)
    {
        $this->viewPath = rtrim($viewPath, '/\\');
    }

    /**
     * 算繪樣板並套用共用版面 layout.php
     *
     * @param string $template 樣板檔名（不含 .php）
     * @param array  $data     傳入樣板的變數
     */
    public function render(string $template, array $data = []): string
    {
        $content = $this->partial($template, $data);

        return $this->partial('layout', array_merge($data, ['content' => $content]));
    }

    /**
     * 只算繪樣板本身，不套版面（供版面內部或 AJAX 片段使用）
     */
    public function partial(string $template, array $data = []): string
    {
        $file = $this->viewPath . DIRECTORY_SEPARATOR . $template . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException('找不到樣板檔：' . $template);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $file;

        return (string) ob_get_clean();
    }

    /**
     * HTML 跳脫（XSS 防護）
     */
    public static function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

<?php
/**
 * 極簡樣板引擎
 *
 * 把 views/ 資料夾中的 PHP 樣板檔算繪成 HTML。
 * 樣板內部一律使用 h() 做輸出跳脫，避免 XSS。
 */

declare(strict_types=1);

final class View
{
    /**
     * 算繪樣板並直接輸出。
     *
     * @param string              $templateName views/ 下的檔名（不含 .php）
     * @param array<string,mixed> $data         要帶入樣板的變數
     */
    public static function render(string $templateName, array $data = []): void
    {
        echo self::capture($templateName, $data);
    }

    /**
     * 算繪樣板並回傳字串（用於把內容包進外層版型）。
     *
     * @param array<string,mixed> $data
     */
    public static function capture(string $templateName, array $data = []): string
    {
        $templatePath = __DIR__ . '/../views/' . $templateName . '.php';

        if (!is_file($templatePath)) {
            throw new RuntimeException('找不到樣板檔：' . $templateName);
        }

        // 把陣列鍵值展開成樣板可直接使用的區域變數
        extract($data, EXTR_SKIP);

        ob_start();
        require $templatePath;

        return (string) ob_get_clean();
    }

    /**
     * 以「後台版型」輸出頁面：共用 head、導覽列與頁尾。
     *
     * @param string              $templateName 內容樣板
     * @param string              $pageTitle    瀏覽器標題
     * @param array<string,mixed> $data         內容樣板需要的變數
     */
    public static function renderAdminPage(string $templateName, string $pageTitle, array $data = []): void
    {
        $content = self::capture($templateName, $data);

        self::render('layout', [
            'pageTitle'   => $pageTitle,
            'pageContent' => $content,
        ]);
    }
}

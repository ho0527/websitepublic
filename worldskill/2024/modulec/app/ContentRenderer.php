<?php
/**
 * 模組 C - 內容渲染器
 *
 * 負責把檔案的主要內容轉成可以直接輸出的 HTML：
 *  - .html 檔：標籤原樣輸出，只重寫圖片路徑（並補上缺少的 alt）
 *  - .txt  檔：每一行文字變成 <p>，單獨一行的圖片路徑變成 <p><img></p>
 */

declare(strict_types=1);

require_once __DIR__ . '/ContentPage.php';

final class ContentRenderer
{
    /**
     * 依副檔名選擇渲染策略
     */
    public static function render(ContentPage $page): string
    {
        return $page->extension() === 'txt'
            ? self::renderPlainText($page->body())
            : self::renderHtml($page->body());
    }

    /**
     * .html：原樣輸出，只把 <img src> 換成伺服器上可用的圖片網址
     */
    public static function renderHtml(string $body): string
    {
        $rendered = preg_replace_callback(
            '/<img\b[^>]*>/i',
            static fn (array $matches): string => self::rewriteImageTag($matches[0]),
            $body
        );

        return $rendered ?? $body;
    }

    /**
     * 重寫單一 <img> 標籤：修正 src，並在缺少 alt 時依檔名補上替代文字（無障礙需求）
     */
    private static function rewriteImageTag(string $tag): string
    {
        $source = '';

        $rewritten = preg_replace_callback(
            '/(\bsrc\s*=\s*)(["\'])(.*?)\2/i',
            static function (array $matches) use (&$source): string {
                $source = $matches[3];

                return $matches[1] . $matches[2] . mc_e(mc_image_url($matches[3])) . $matches[2];
            },
            $tag
        ) ?? $tag;

        if (preg_match('/\balt\s*=/i', $rewritten) !== 1) {
            $alternative = mc_image_alt_text($source);
            $rewritten = preg_replace(
                '/<img\b/i',
                '<img alt="' . mc_e($alternative) . '"',
                $rewritten,
                1
            ) ?? $rewritten;
        }

        return $rewritten;
    }

    /**
     * .txt：以空行分段
     *
     * 「沒有空白字元且以圖片副檔名結尾」的整行視為圖片路徑，轉成圖片標籤，
     * 其餘每一段文字轉成一個 <p>。
     */
    public static function renderPlainText(string $body): string
    {
        $normalised = str_replace(["\r\n", "\r"], "\n", $body);
        $output = [];

        foreach (explode("\n", $normalised) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (self::isImageLine($line)) {
                $alternative = mc_image_alt_text($line);
                $output[] = '<p><img src="' . mc_e(mc_image_url($line)) . '" alt="' . mc_e($alternative) . '"></p>';
                continue;
            }

            $output[] = '<p>' . mc_e($line) . '</p>';
        }

        return implode("\n", $output);
    }

    /**
     * 是否為「單獨一行的圖片路徑」：不含空白字元，且副檔名是圖片
     */
    private static function isImageLine(string $line): bool
    {
        if (preg_match('/\s/u', $line) === 1) {
            return false;
        }

        return mc_is_image_file($line);
    }
}

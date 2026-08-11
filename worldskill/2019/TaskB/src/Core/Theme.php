<?php
/**
 * 佈景主題（含子主題）樣板引擎
 *
 * 規格要求「以既有的起始主題（blankslate）為父主題，建立名為 Kazan_MuseumTour 的子主題，
 * 所有修改都放在子主題」。此處以樣板搜尋順序實作相同概念：
 *   1. 先找子主題 themes/Kazan_MuseumTour/templates/<name>.php
 *   2. 找不到才回退父主題 themes/blankslate/templates/<name>.php
 * 主題資訊（含 Template: 父主題宣告）寫在各主題的 style.css 檔頭。
 */

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Theme
{
    /** @var string[] 樣板搜尋順序（子主題 → 父主題） */
    private array $searchPaths = [];

    /** @var array<string, string> 主題檔頭資訊 */
    private array $headers = [];

    public function __construct(private string $themeDir, private string $childSlug)
    {
        $this->headers = $this->readHeaders($childSlug);
        $this->searchPaths[] = $themeDir . '/' . $childSlug . '/templates';

        $parent = $this->headers['Template'] ?? '';
        if ($parent !== '' && is_dir($themeDir . '/' . $parent)) {
            $this->searchPaths[] = $themeDir . '/' . $parent . '/templates';
        }
    }

    public function name(): string
    {
        return $this->childSlug;
    }

    public function parentName(): string
    {
        return $this->headers['Template'] ?? '';
    }

    public function headers(): array
    {
        return $this->headers;
    }

    /** 子主題的樣式檔網址（父主題樣式由子主題以 @import 或 link 自行引入） */
    public function styleUrl(string $file = 'style.css'): string
    {
        return Url::asset('themes/' . $this->childSlug . '/' . $file);
    }

    public function parentStyleUrl(string $file = 'style.css'): string
    {
        return Url::asset('themes/' . $this->parentName() . '/' . $file);
    }

    /** 找出樣板實際檔案路徑 */
    public function locate(string $template): ?string
    {
        foreach ($this->searchPaths as $path) {
            $file = $path . '/' . $template . '.php';
            if (is_file($file)) {
                return $file;
            }
        }

        return null;
    }

    /** 渲染樣板並回傳字串 */
    public function render(string $template, array $data = []): string
    {
        $file = $this->locate($template);
        if ($file === null) {
            throw new RuntimeException('找不到樣板：' . $template);
        }

        // 讓樣板可以直接用變數名稱取值，並保留 $theme 供巢狀 include 使用
        $theme = $this;
        extract($data, EXTR_SKIP);

        ob_start();
        require $file;

        return (string) ob_get_clean();
    }

    /** 在樣板中嵌入其他樣板（區塊） */
    public function partial(string $template, array $data = []): void
    {
        echo $this->render($template, $data);
    }

    /** 讀取主題 style.css 檔頭資訊 */
    private function readHeaders(string $slug): array
    {
        $file = $this->themeDir . '/' . $slug . '/style.css';
        if (!is_file($file)) {
            return [];
        }

        $content = (string) file_get_contents($file, false, null, 0, 2048);
        $headers = [];

        foreach (['Theme Name', 'Template', 'Author', 'Version', 'Description'] as $key) {
            if (preg_match('/^[\s*]*' . preg_quote($key, '/') . ':\s*(.+)$/mi', $content, $m)) {
                $headers[str_replace(' ', '', $key)] = trim($m[1]);
            }
        }

        return $headers;
    }
}

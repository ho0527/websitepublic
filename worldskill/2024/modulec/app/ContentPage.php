<?php
/**
 * 模組 C - 單篇文章的資料模型
 *
 * 一個 ContentPage 對應 content-pages（或其子資料夾）內的一個 .html / .txt 檔案。
 * 建構時會把檔案切成「前置資料（front-matter）」與「主要內容（body）」兩段，
 * 之後所有規則（標題挑選、草稿、未來日期、封面圖）都由這個類別統一回答，
 * 讓列表頁與單篇頁不會各自實作出不一致的判斷。
 */

declare(strict_types=1);

final class ContentPage
{
    /** 相對於 content-pages 的檔案路徑，例如 basilicas/2024-09-10-title.html */
    private string $relativePath;

    /** 實體絕對路徑 */
    private string $absolutePath;

    /** 檔名（含副檔名） */
    private string $fileName;

    /** 檔名（不含副檔名），也就是 YYYY-MM-DD-title-in-slug */
    private string $baseName;

    /** 小寫副檔名：html 或 txt */
    private string $extension;

    /** front-matter 的鍵值對，鍵一律小寫 */
    private array $frontMatter = [];

    /** 去掉 front-matter 之後的主要內容 */
    private string $body = '';

    private function __construct(string $contentRoot, string $relativePath)
    {
        $this->relativePath = str_replace('\\', '/', trim($relativePath, '/'));
        $this->absolutePath = $contentRoot . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $this->relativePath);
        $this->fileName = basename($this->relativePath);
        $this->baseName = pathinfo($this->fileName, PATHINFO_FILENAME);
        $this->extension = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));

        $this->parseFile();
    }

    /**
     * 由檔案建立文章物件，檔案不存在或副檔名不支援時回傳 null
     */
    public static function fromFile(string $contentRoot, string $relativePath): ?self
    {
        $candidate = $contentRoot . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, trim($relativePath, '/'));

        if (!is_file($candidate)) {
            return null;
        }

        $extension = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
        if (!in_array($extension, MC_PAGE_EXTENSIONS, true)) {
            return null;
        }

        return new self($contentRoot, $relativePath);
    }

    /**
     * 讀檔並拆出 front-matter 與主要內容
     *
     * front-matter 是選用的，若存在則以單獨一行的 "---" 作為開頭與結尾。
     */
    private function parseFile(): void
    {
        $raw = (string) file_get_contents($this->absolutePath);

        // 去掉 UTF-8 BOM，否則開頭的 --- 會判斷失敗
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw) ?? $raw;
        $normalised = str_replace(["\r\n", "\r"], "\n", $raw);

        if (preg_match('/^---[ \t]*\n(.*?)\n---[ \t]*(?:\n|$)/s', $normalised, $matches) === 1) {
            $this->frontMatter = $this->parseFrontMatter($matches[1]);
            $this->body = substr($normalised, strlen($matches[0]));
        } else {
            $this->body = $normalised;
        }

        $this->body = ltrim($this->body, "\n");
    }

    /**
     * 解析 front-matter 區塊的 key: value（每行一組，鍵轉小寫）
     */
    private function parseFrontMatter(string $block): array
    {
        $pairs = [];

        foreach (explode("\n", $block) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $position = strpos($line, ':');
            if ($position === false) {
                continue;
            }

            $key = strtolower(trim(substr($line, 0, $position)));
            $value = trim(substr($line, $position + 1));

            // 允許值被引號包住
            $value = trim($value, "\"'");

            if ($key !== '') {
                $pairs[$key] = $value;
            }
        }

        return $pairs;
    }

    // ------------------------------------------------------------------
    // 基本存取
    // ------------------------------------------------------------------

    public function relativePath(): string
    {
        return $this->relativePath;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function baseName(): string
    {
        return $this->baseName;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function frontMatter(): array
    {
        return $this->frontMatter;
    }

    /** 所在子資料夾（相對 content-pages），根目錄回傳空字串 */
    public function folder(): string
    {
        $folder = trim(str_replace('\\', '/', dirname($this->relativePath)), '/');

        return ($folder === '.' || $folder === '') ? '' : $folder;
    }

    // ------------------------------------------------------------------
    // 命名規則：YYYY-MM-DD-title-in-slug
    // ------------------------------------------------------------------

    /**
     * 檔名是否符合日期命名規則（前 11 個字元為 YYYY-MM-DD-）
     */
    public function hasDate(): bool
    {
        return $this->date() !== null;
    }

    /**
     * 取得文章日期，格式不符或不是合法日期時回傳 null
     */
    public function date(): ?string
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})-/', $this->fileName, $matches) !== 1) {
            return null;
        }

        [, $year, $month, $day] = $matches;
        if (!checkdate((int) $month, (int) $day, (int) $year)) {
            return null;
        }

        return sprintf('%s-%s-%s', $year, $month, $day);
    }

    /** 日期是否晚於今天（未來文章不可列出） */
    public function isFuture(): bool
    {
        $date = $this->date();
        if ($date === null) {
            return false;
        }

        return $date > date('Y-m-d');
    }

    /** front-matter 的 draft 是否為 true（不分大小寫） */
    public function isDraft(): bool
    {
        $draft = $this->frontMatter['draft'] ?? '';

        return strtolower(trim($draft)) === 'true';
    }

    /**
     * 是否可以出現在列表 / 標籤 / 搜尋結果中
     *
     * 三個排除條件：沒有日期、日期在未來、草稿狀態。
     */
    public function isListable(): bool
    {
        return $this->hasDate() && !$this->isFuture() && !$this->isDraft();
    }

    /**
     * 檔名去掉日期與副檔名之後的 slug，也是網址最後一段
     *
     * 例如 2024-09-01-example-page.html → 2024-09-01-example-page
     * （規格書的網址帶著日期，所以這裡保留完整 baseName）
     */
    public function slug(): string
    {
        return $this->baseName;
    }

    /** 檔名去掉日期前綴之後的標題 slug，用於「由檔名推導標題」 */
    public function titleSlug(): string
    {
        return preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $this->baseName) ?? $this->baseName;
    }

    // ------------------------------------------------------------------
    // 標題挑選規則
    // ------------------------------------------------------------------

    /**
     * 依規格書順序決定標題：
     *  1. front-matter 的 title
     *  2. 內容中第一個 <h1> 的純文字
     *  3. 檔名（去掉日期與副檔名、連字號換空白、每字首字母大寫）
     */
    public function title(): string
    {
        $frontMatterTitle = trim($this->frontMatter['title'] ?? '');
        if ($frontMatterTitle !== '') {
            return $frontMatterTitle;
        }

        $headingTitle = $this->firstHeadingText();
        if ($headingTitle !== null && $headingTitle !== '') {
            return $headingTitle;
        }

        return mc_titleize($this->titleSlug());
    }

    /**
     * 取出內容中第一個 <h1> 的文字內容（h1 內只會是簡單文字）
     */
    private function firstHeadingText(): ?string
    {
        if (preg_match('/<h1\b[^>]*>(.*?)<\/h1>/is', $this->body, $matches) !== 1) {
            return null;
        }

        return trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /** 標題是否來自內容中的 h1（單篇頁渲染時要避免同一個標題出現兩次） */
    public function titleComesFromHeading(): bool
    {
        return trim($this->frontMatter['title'] ?? '') === ''
            && ($this->firstHeadingText() ?? '') !== '';
    }

    // ------------------------------------------------------------------
    // 其他 front-matter 欄位
    // ------------------------------------------------------------------

    /**
     * 標籤陣列（以逗號分隔，允許逗號後帶空白）
     *
     * @return string[]
     */
    public function tags(): array
    {
        $raw = trim($this->frontMatter['tags'] ?? '');
        if ($raw === '') {
            return [];
        }

        $tags = preg_split('/\s*,\s*/u', $raw) ?: [];
        $tags = array_map('trim', $tags);

        return array_values(array_filter($tags, static fn (string $tag): bool => $tag !== ''));
    }

    /** 是否含有指定標籤（以 slug 比對，因此不分大小寫與空白寫法） */
    public function hasTag(string $tagSlug): bool
    {
        $target = mc_slugify($tagSlug);

        foreach ($this->tags() as $tag) {
            if (mc_slugify($tag) === $target) {
                return true;
            }
        }

        return false;
    }

    /**
     * 列表用摘要：優先使用 front-matter 的 summary，沒有時由內容自動截取
     */
    public function summary(): string
    {
        $summary = trim($this->frontMatter['summary'] ?? '');
        if ($summary !== '') {
            return $summary;
        }

        return mc_excerpt($this->plainText());
    }

    /**
     * 封面圖片檔名
     *
     * 預設由 front-matter 的 cover 指定；沒有指定時，改用與檔名同名的圖片。
     */
    public function coverImage(): string
    {
        $cover = trim($this->frontMatter['cover'] ?? '');
        if ($cover !== '') {
            return $cover;
        }

        // 沒有定義時：images 資料夾內與本檔案同名的圖片，逐一嘗試常見副檔名
        foreach (MC_IMAGE_EXTENSIONS as $extension) {
            $candidate = MC_CONTENT_DIR . DIRECTORY_SEPARATOR . MC_IMAGES_DIRNAME
                . DIRECTORY_SEPARATOR . $this->baseName . '.' . $extension;
            if (is_file($candidate)) {
                return $this->baseName . '.' . $extension;
            }
        }

        // 找不到實體檔案時仍回傳慣例名稱（規格書說明不需考慮封面缺失的情況）
        return $this->baseName . '.jpeg';
    }

    public function coverImageUrl(): string
    {
        return mc_image_url($this->coverImage());
    }

    // ------------------------------------------------------------------
    // 網址與搜尋
    // ------------------------------------------------------------------

    /** 本文章的路由，例如 heritages/basilicas/2024-09-10-title */
    public function route(): string
    {
        $folder = $this->folder();

        return 'heritages/' . ($folder === '' ? '' : $folder . '/') . $this->slug();
    }

    public function url(): string
    {
        return mc_url($this->route());
    }

    /**
     * 內容的純文字版本，供搜尋比對與備援摘要使用
     */
    public function plainText(): string
    {
        $text = $this->extension === 'html'
            ? strip_tags(preg_replace('/<(script|style)\b[^>]*>.*?<\/\1>/is', ' ', $this->body) ?? $this->body)
            : $this->body;

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    /**
     * 標題或內容是否含有指定關鍵字（不分大小寫）
     */
    public function matchesKeyword(string $keyword): bool
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return false;
        }

        $haystack = $this->title() . ' ' . $this->summary() . ' ' . $this->plainText();

        return mb_stripos($haystack, $keyword, 0, 'UTF-8') !== false;
    }
}

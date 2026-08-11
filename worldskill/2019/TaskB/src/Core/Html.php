<?php
/**
 * 輸出跳脫與文字處理工具
 *
 * 所有輸出到 HTML 的動態內容都必須經過 Html::e()，避免 XSS。
 */

declare(strict_types=1);

namespace App\Core;

final class Html
{
    /** HTML 內容跳脫（同時處理單引號，可安全放在屬性內） */
    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** 將以空白行分段的純文字轉為安全的 <p> 段落 */
    public static function paragraphs(?string $text): string
    {
        $blocks = preg_split('/\R{2,}/u', trim((string) $text)) ?: [];
        $html   = '';

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }
            $html .= '<p>' . nl2br(self::e($block)) . '</p>';
        }

        return $html;
    }

    /** 產生純文字摘要（用於 meta description 等） */
    public static function excerpt(?string $text, int $length = 160): string
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)) ?? '');
        if (mb_strlen($plain, 'UTF-8') <= $length) {
            return $plain;
        }

        return rtrim(mb_substr($plain, 0, $length - 1, 'UTF-8')) . '…';
    }

    /** 由標題產生網址代稱（slug） */
    public static function slugify(string $title): string
    {
        $slug = mb_strtolower(trim($title), 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug) ?? '';

        return trim($slug, '-');
    }

    /** 將多行文字（一行一筆）轉成陣列 */
    public static function lines(?string $text): array
    {
        $lines = preg_split('/\R/u', (string) $text) ?: [];

        return array_values(array_filter(array_map('trim', $lines), static fn ($line) => $line !== ''));
    }

    /** 依 ISO 日期輸出人類可讀格式 */
    public static function date(?string $datetime, string $format = 'j M Y'): string
    {
        if (empty($datetime)) {
            return '';
        }

        $timestamp = strtotime($datetime);

        return $timestamp === false ? '' : date($format, $timestamp);
    }
}

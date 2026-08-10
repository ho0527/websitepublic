<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 應用程式設定的存取入口。
 *
 * 以「.」分隔的路徑讀取巢狀設定值，例如 Config::get('database.host')。
 */
final class Config
{
    /** @var array<string, mixed> 已載入的設定內容 */
    private static array $items = [];

    /**
     * 載入設定檔。
     */
    public static function load(string $configFile): void
    {
        self::$items = require $configFile;
    }

    /**
     * 讀取設定值，找不到時回傳預設值。
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$items;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }
}

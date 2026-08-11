<?php
namespace App\Core;

/**
 * 國家清單。
 *
 * 依規格要求：「The list of countries is static and provided in the HTML code」，
 * 因此國家清單並不放在資料庫，而是寫死在程式碼中。
 * 前八個國家沿用官方樣板 restaurantapp.js 提供的清單，
 * 其餘為規格範例（UK / US / UAE / BE …）中出現的國家。
 */
class Countries
{
    /** @var array<string, string> 代碼 => 名稱 */
    private const LIST = [
        'AU' => 'Australia',
        'BE' => 'Belgium',
        'BR' => 'Brasil',
        'CA' => 'Canada',
        'CH' => 'Switzerland',
        'CN' => 'China',
        'DE' => 'Germany',
        'FR' => 'France',
        'IN' => 'India',
        'JP' => 'Japan',
        'KR' => 'Korea',
        'AE' => 'United Arab Emirates',
        'UK' => 'United Kingdom',
        'US' => 'United States',
    ];

    /**
     * 取得完整國家清單
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::LIST;
    }

    /**
     * 判斷代碼是否有效
     */
    public static function isValid(string $code): bool
    {
        return isset(self::LIST[$code]);
    }

    /**
     * 取得國家名稱，找不到時回傳代碼本身
     */
    public static function name(string $code): string
    {
        return self::LIST[$code] ?? $code;
    }

    /**
     * 產生 <option> 清單的 HTML（值已跳脫）
     *
     * @param string $selected    目前選取的代碼
     * @param string $placeholder 第一個空值選項的文字
     */
    public static function options(string $selected = '', string $placeholder = 'choose a country'): string
    {
        $html = '<option value="">' . View::e($placeholder) . '</option>';

        foreach (self::LIST as $code => $name) {
            $html .= '<option value="' . View::e($code) . '"'
                . ($code === $selected ? ' selected' : '') . '>'
                . View::e($code . ' - ' . $name)
                . '</option>';
        }

        return $html;
    }
}

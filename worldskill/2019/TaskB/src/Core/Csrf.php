<?php
/**
 * CSRF 權杖：所有後台表單都必須帶 _token 欄位
 */

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    /** 取得（必要時產生）本次 session 的權杖 */
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /** 輸出隱藏欄位 */
    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . Html::e(self::token()) . '">';
    }

    /** 驗證表單送出的權杖 */
    public static function verify(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION[self::SESSION_KEY])
            && hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}

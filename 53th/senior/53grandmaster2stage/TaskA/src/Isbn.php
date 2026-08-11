<?php
/**
 * ISBN-13 格式驗證
 *
 * 依試題說明：ISBN 由 13 個數字組成，排列方式為 XXX-XXX-XXX-X。
 * 前 12 位依序乘以權重 1、3、1、3…相加後，校驗碼為加權和除以 10 的負餘數。
 * 例：978-986-181-728 的加權和為 164，164 = 17 x 10 - 6，故校驗碼為 6。
 */
class Isbn
{
    /**
     * 驗證 ISBN 是否合法（允許以「-」分隔）
     */
    public static function isValid(mixed $isbn): bool
    {
        if (!is_string($isbn)) {
            return false;
        }

        $digits = str_replace(['-', ' '], '', $isbn);

        // 必須剛好 13 個數字
        if (!preg_match('/^\d{13}$/', $digits)) {
            return false;
        }

        return self::checkDigit(substr($digits, 0, 12)) === (int) $digits[12];
    }

    /**
     * 由前 12 位數字計算校驗碼
     */
    public static function checkDigit(string $first12Digits): int
    {
        $weightedSum = 0;

        for ($position = 0; $position < 12; $position++) {
            $weight       = $position % 2 === 0 ? 1 : 3;
            $weightedSum += ((int) $first12Digits[$position]) * $weight;
        }

        // 加權和除以 10 的負餘數
        return (10 - ($weightedSum % 10)) % 10;
    }
}

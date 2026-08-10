<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Models\Booking;

/**
 * 訂票編號產生器。
 *
 * 產生 12 碼的英數字編號，大小寫視為不同字元，並確保不與既有紀錄重複。
 */
final class BookingCodeGenerator
{
    /** 可用字元：大寫、小寫與數字 */
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    /** 避免極端情況下無限迴圈的重試上限 */
    private const MAX_ATTEMPTS = 50;

    /**
     * 產生一組尚未被使用的訂票編號。
     */
    public function generate(): string
    {
        $length = (int) Config::get('booking.code_length', 12);

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->randomCode($length);

            if (!Booking::codeExists($code)) {
                return $code;
            }
        }

        throw new \RuntimeException('無法產生不重複的訂票編號，請稍後再試');
    }

    /**
     * 以密碼學安全的亂數產生指定長度的英數字字串。
     */
    private function randomCode(int $length): string
    {
        $lastIndex = strlen(self::ALPHABET) - 1;
        $code      = '';

        for ($position = 0; $position < $length; $position++) {
            $code .= self::ALPHABET[random_int(0, $lastIndex)];
        }

        return $code;
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 問答驗證碼的正確答案區塊（圖片中目標物件的外框）。
 *
 * @property int $id
 * @property int $question_id
 * @property int $x
 * @property int $y
 * @property int $width
 * @property int $height
 */
final class CaptchaAnswerRegion extends Model
{
    protected static string $table = 'captcha_answer_region';

    protected static array $fillable = ['question_id', 'x', 'y', 'width', 'height'];

    /**
     * 判斷指定座標是否落在本區塊內。
     */
    public function contains(int $pointX, int $pointY): bool
    {
        return $pointX >= (int) $this->x
            && $pointX <= (int) $this->x + (int) $this->width
            && $pointY >= (int) $this->y
            && $pointY <= (int) $this->y + (int) $this->height;
    }
}

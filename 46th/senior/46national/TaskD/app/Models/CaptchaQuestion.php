<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 問答驗證碼題目。
 *
 * @property int    $id
 * @property string $image_file 題目圖片檔名
 * @property string $question   題目敘述
 */
final class CaptchaQuestion extends Model
{
    protected static string $table = 'captcha_question';

    protected static array $fillable = ['image_file', 'question'];

    /**
     * 隨機取一道題目，可指定要避開的題號（換題時避免抽到同一題）。
     */
    public static function random(?int $exceptId = null): ?CaptchaQuestion
    {
        $questions = self::all();

        if ($questions === []) {
            return null;
        }

        $candidates = array_values(array_filter(
            $questions,
            static fn (CaptchaQuestion $question): bool => $exceptId === null || $question->id() !== $exceptId
        ));

        // 只有一題時仍需回傳該題
        $pool = $candidates === [] ? $questions : $candidates;

        return $pool[random_int(0, count($pool) - 1)];
    }

    /**
     * 取得本題的所有正確答案區塊。
     *
     * @return array<int, CaptchaAnswerRegion>
     */
    public function answerRegions(): array
    {
        return CaptchaAnswerRegion::where('question_id', $this->id())->orderBy('id')->get();
    }
}

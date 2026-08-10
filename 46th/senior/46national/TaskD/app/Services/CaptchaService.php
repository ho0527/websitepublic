<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Session;
use App\Models\CaptchaQuestion;

/**
 * 問答驗證碼。
 *
 * 目前的題目與「是否已通過驗證」都保存在 Session，
 * 正確答案的座標只存在伺服器端，用戶端拿不到，也就無法直接偽造通過。
 */
final class CaptchaService
{
    /** Session 中保存目前題號的鍵 */
    private const QUESTION_KEY = 'captcha_question_id';

    /** Session 中保存「已通過驗證」的鍵 */
    private const PASSED_KEY = 'captcha_passed';

    /**
     * 取得目前的題目，尚未指定時隨機挑一題。
     */
    public function currentQuestion(): ?CaptchaQuestion
    {
        $questionId = Session::get(self::QUESTION_KEY);
        $question   = $questionId === null ? null : CaptchaQuestion::find((int) $questionId);

        if ($question === null) {
            $question = $this->newQuestion();
        }

        return $question;
    }

    /**
     * 換一道新題目，並清除先前的通過狀態。
     */
    public function newQuestion(): ?CaptchaQuestion
    {
        $currentId = Session::get(self::QUESTION_KEY);
        $question  = CaptchaQuestion::random($currentId === null ? null : (int) $currentId);

        Session::forget(self::PASSED_KEY);

        if ($question === null) {
            Session::forget(self::QUESTION_KEY);

            return null;
        }

        Session::put(self::QUESTION_KEY, $question->id());

        return $question;
    }

    /**
     * 驗證使用者選取的區塊是否正確。
     *
     * 必須「每個目標物件都被選到」且「沒有多選其他地方」才算通過；
     * 驗證失敗時會自動換一道新題目。
     *
     * @param array<int, array{x: int, y: int}> $selections 使用者點擊的座標（以圖片原始尺寸為準）
     */
    public function verify(array $selections): bool
    {
        $question = $this->currentQuestion();

        if ($question === null) {
            return false;
        }

        $regions = $question->answerRegions();

        // 完全沒有選取，或選取數量與目標物件數量不符，直接判定失敗
        if ($selections === [] || count($selections) !== count($regions)) {
            $this->newQuestion();

            return false;
        }

        // 記錄每個目標物件被選到幾次
        $hitCounts = array_fill(0, count($regions), 0);

        foreach ($selections as $selection) {
            $matchedIndex = null;

            foreach ($regions as $index => $region) {
                if ($region->contains($selection['x'], $selection['y'])) {
                    $matchedIndex = $index;

                    break;
                }
            }

            // 選到目標物件以外的地方＝多選取
            if ($matchedIndex === null) {
                $this->newQuestion();

                return false;
            }

            $hitCounts[$matchedIndex]++;
        }

        // 每個目標物件都必須剛好被選取一次
        foreach ($hitCounts as $count) {
            if ($count !== 1) {
                $this->newQuestion();

                return false;
            }
        }

        Session::put(self::PASSED_KEY, true);

        return true;
    }

    /**
     * 目前是否已通過驗證。
     */
    public function hasPassed(): bool
    {
        return Session::get(self::PASSED_KEY) === true;
    }

    /**
     * 用掉這次的驗證結果（訂票送出後必須重新驗證）。
     */
    public function consume(): void
    {
        Session::forget(self::PASSED_KEY);
        Session::forget(self::QUESTION_KEY);
    }
}

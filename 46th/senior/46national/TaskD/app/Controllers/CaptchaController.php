<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\ServiceContainer;
use App\Models\CaptchaQuestion;

/**
 * 問答驗證碼。
 *
 * 全部以 AJAX 在訂票頁的同一個畫面上操作，不會另開新頁面。
 */
final class CaptchaController extends Controller
{
    /**
     * 取得目前的題目。
     */
    public function show(): void
    {
        $this->json($this->questionPayload(ServiceContainer::captcha()->currentQuestion()));
    }

    /**
     * 產生新的驗證問題（同時會清除先前的選取與通過狀態）。
     */
    public function refresh(): void
    {
        $this->json($this->questionPayload(ServiceContainer::captcha()->newQuestion()));
    }

    /**
     * 驗證使用者選取的區塊。
     */
    public function verify(): void
    {
        $captcha    = ServiceContainer::captcha();
        $selections = $this->parseSelections();
        $passed     = $captcha->verify($selections);

        if ($passed) {
            $this->json(['success' => true, 'message' => '驗證通過']);
        }

        // 驗證失敗時服務層已自動換題，這裡把新題目一併回傳
        $this->json([
            'success'  => false,
            'message'  => '驗證失敗，請依題目重新選取正確的物件',
            'question' => $this->questionPayload($captcha->currentQuestion())['question'],
        ]);
    }

    /**
     * 解析用戶端送來的選取座標。
     *
     * @return array<int, array{x: int, y: int}>
     */
    private function parseSelections(): array
    {
        $raw = json_decode((string) file_get_contents('php://input'), true);

        if (!is_array($raw) || !isset($raw['selections']) || !is_array($raw['selections'])) {
            return [];
        }

        $selections = [];

        foreach ($raw['selections'] as $selection) {
            if (!is_array($selection) || !isset($selection['x'], $selection['y'])) {
                continue;
            }

            $selections[] = ['x' => (int) $selection['x'], 'y' => (int) $selection['y']];
        }

        return $selections;
    }

    /**
     * 把題目整理成回傳給用戶端的內容（不包含正確答案座標）。
     *
     * @return array<string, mixed>
     */
    private function questionPayload(?CaptchaQuestion $question): array
    {
        if ($question === null) {
            return ['success' => false, 'question' => null];
        }

        return [
            'success'  => true,
            'question' => [
                'text'          => (string) $question->question,
                'image'         => $this->view->url(
                    Config::get('captcha.image_directory', 'assets/captcha') . '/' . $question->image_file
                ),
                'marker_width'  => (int) Config::get('captcha.marker_width', 120),
                'marker_height' => (int) Config::get('captcha.marker_height', 100),
                'marker_border' => (int) Config::get('captcha.marker_border', 5),
            ],
        ];
    }
}

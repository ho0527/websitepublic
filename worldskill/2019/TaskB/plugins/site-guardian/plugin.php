<?php
/**
 * 外掛：Site Guardian Security
 *
 * 功能：
 *   1. 送出基本安全性標頭（點擊劫持、MIME 嗅探、Referrer、權限政策）
 *   2. 所有登入嘗試（成功與失敗）都寫入 login_attempts 資料表
 *      —— 失敗次數達門檻即封鎖該 IP 一段時間（門檻與時間可於後台設定）
 *   3. 後台「Security」頁面可檢視攻擊者 IP、時間與嘗試的帳號
 *
 * 註：登入紀錄與封鎖判斷的實作位於 App\Core\Auth 與 App\Model\LoginAttempt，
 *     本檔案負責在外掛啟用時掛上標頭並提供後台提示。
 */

declare(strict_types=1);

use App\Core\App;
use App\Core\Html;
use App\Core\PluginManager;

return static function (PluginManager $hooks, App $app): void {
    // 1. 安全性標頭（必須在任何輸出之前送出）
    if (!headers_sent()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    }

    // 2. 後台安全性頁面的說明區塊
    $hooks->addAction('admin_security_notice', static function () use ($app): void {
        $maxAttempts = (int) $app->setting('security_max_attempts', '5');
        $lockout     = (int) $app->setting('security_lockout_min', '15');
        ?>
        <div class="notice notice--info">
            <p>
                <strong>Site Guardian is active.</strong>
                Every login attempt is logged. After
                <strong><?= Html::e((string) $maxAttempts) ?></strong> failed attempts from the same IP address,
                that address is blocked for <strong><?= Html::e((string) $lockout) ?></strong> minutes.
            </p>
        </div>
        <?php
    });

    // 3. 登入頁下方的保護提示
    $hooks->addAction('login_footer', static function (): void {
        echo '<p class="login-note">Protected by Site Guardian — login attempts are logged.</p>';
    });
};

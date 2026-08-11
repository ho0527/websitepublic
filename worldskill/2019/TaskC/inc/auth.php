<?php
/**
 * 主辦者（organizer）後台的登入狀態管理與多租戶存取控制
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * 啟動 session（後台頁面共用）
 */
function start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('wsc2019_taskc');
        session_start();
    }
}

/**
 * 目前登入的主辦者資料；未登入回傳 null
 *
 * @return array<string, mixed>|null
 */
function current_organizer(): ?array
{
    start_session();
    if (empty($_SESSION['organizer_id'])) {
        return null;
    }
    return db_one('SELECT `id`, `name`, `slug`, `email` FROM `organizers` WHERE `id` = ?', [$_SESSION['organizer_id']]);
}

/**
 * 要求必須登入，否則導回登入頁
 *
 * @return array<string, mixed>
 */
function require_organizer(): array
{
    $organizer = current_organizer();
    if ($organizer === null) {
        redirect('index.php');
    }
    // 登出後按上一頁不可以再看到後台內容
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    return $organizer;
}

/**
 * 取得「屬於目前登入主辦者」的活動；不屬於自己的活動一律視為不存在（多租戶隔離）
 *
 * @return array<string, mixed>
 */
function require_own_event(int $eventId, array $organizer): array
{
    $event = db_one(
        'SELECT * FROM `events` WHERE `id` = ? AND `organizer_id` = ?',
        [$eventId, $organizer['id']]
    );
    if ($event === null) {
        http_response_code(404);
        exit('Event not found');
    }
    return $event;
}

/**
 * 設定一次性提示訊息
 */
function set_flash(string $type, string $message): void
{
    start_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * 取出並清除一次性提示訊息
 *
 * @return array{type: string, message: string}|null
 */
function take_flash(): ?array
{
    start_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

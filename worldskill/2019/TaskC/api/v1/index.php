<?php
/**
 * 參加者 REST API（Attendee API）- 第一階段 B1 ~ B4
 *
 * 路由入口。因為不修改 nginx.conf，實際網址採用 PATH_INFO 形式：
 *   .../TaskC/api/v1/index.php/events
 * 若在 nginx 加上 README 內附的 rewrite 片段，即可使用規格中的乾淨網址：
 *   .../TaskC/api/v1/events
 *
 * 支援的端點：
 *   GET  /events                                                     B1a 所有即將舉行的活動
 *   GET  /organizers/{organizer-slug}/events/{event-slug}             B2a 單一活動詳細資料
 *   POST /login                                                       B3a 參加者登入
 *   POST /logout?token=...                                            B3b 參加者登出
 *   POST /organizers/{o-slug}/events/{e-slug}/registration?token=...   B4a 報名活動
 *   GET  /registrations?token=...                                     B4b 取得自己的報名紀錄
 */

declare(strict_types=1);

require_once __DIR__ . '/../../inc/config.php';
require_once __DIR__ . '/../../inc/model.php';

header('Content-Type: application/json; charset=utf-8');
// 方便前端（Task E / 第二階段）以瀏覽器跨來源存取
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/**
 * 輸出 JSON 回應並結束
 */
function json_response(mixed $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * 讀取請求內容：同時支援 JSON 與 FormData / x-www-form-urlencoded
 *
 * @return array<string, mixed>
 */
function request_body(): array
{
    static $body = null;
    if ($body !== null) {
        return $body;
    }
    $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));
    if (str_contains($contentType, 'application/json')) {
        $decoded = json_decode((string) file_get_contents('php://input'), true);
        $body = is_array($decoded) ? $decoded : [];
    } else {
        $body = $_POST;
        if (!$body) {
            // 某些用戶端會用 PUT/PATCH 或未帶正確 Content-Type，仍嘗試解析
            $raw = (string) file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $body = $decoded;
            } else {
                parse_str($raw, $parsed);
                $body = $parsed;
            }
        }
    }
    return $body;
}

/**
 * 依 token 取得參加者；token 無效回傳 null
 *
 * @return array<string, mixed>|null
 */
function attendee_by_token(?string $token): ?array
{
    if ($token === null || $token === '') {
        return null;
    }
    return db_one(
        'SELECT * FROM `attendees` WHERE `login_token` = ? AND `login_token` <> \'\'',
        [$token]
    );
}

// ---------------------------------------------------------------------------
// 路由解析
// ---------------------------------------------------------------------------
$pathInfo = (string) ($_SERVER['PATH_INFO'] ?? '');
if ($pathInfo === '') {
    // 若透過 rewrite 進來，改由 REQUEST_URI 取出 /api/v1/ 之後的部分
    $uri = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?: '';
    if (preg_match('#/api/v1(/.*)$#', $uri, $m)) {
        $pathInfo = $m[1];
    }
}
$segments = array_values(array_filter(explode('/', trim($pathInfo, '/')), static fn($s) => $s !== ''));
$method   = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$token    = isset($_GET['token']) ? (string) $_GET['token'] : null;

// ---------------------------------------------------------------------------
// B1a - GET /events：列出所有即將舉行的活動（跨所有主辦者），依日期由小到大
// ---------------------------------------------------------------------------
if ($method === 'GET' && $segments === ['events']) {
    $rows = db_all(
        'SELECT e.`id`, e.`name`, e.`slug`, e.`date`,
                o.`id` AS organizer_id, o.`name` AS organizer_name, o.`slug` AS organizer_slug
           FROM `events` e
           JOIN `organizers` o ON o.`id` = e.`organizer_id`
          WHERE e.`date` >= ?
          ORDER BY e.`date` ASC, e.`id` ASC',
        [today()]
    );

    $events = array_map(static fn(array $row) => [
        'id'        => (int) $row['id'],
        'name'      => $row['name'],
        'slug'      => $row['slug'],
        'date'      => $row['date'],
        'organizer' => [
            'id'   => (int) $row['organizer_id'],
            'name' => $row['organizer_name'],
            'slug' => $row['organizer_slug'],
        ],
    ], $rows);

    json_response(['events' => $events]);
}

// ---------------------------------------------------------------------------
// B2a - GET /organizers/{organizer-slug}/events/{event-slug}：單一活動完整資料
// ---------------------------------------------------------------------------
if ($method === 'GET' && count($segments) === 4 && $segments[0] === 'organizers' && $segments[2] === 'events') {
    $organizer = db_one('SELECT * FROM `organizers` WHERE `slug` = ?', [$segments[1]]);
    if ($organizer === null) {
        json_response(['message' => 'Organizer not found'], 404);
    }

    $event = db_one(
        'SELECT * FROM `events` WHERE `slug` = ? AND `organizer_id` = ?',
        [$segments[3], $organizer['id']]
    );
    if ($event === null) {
        json_response(['message' => 'Event not found'], 404);
    }

    $eventId = (int) $event['id'];

    // 頻道 -> 房間 -> 議程 的巢狀結構
    $channels = [];
    foreach (event_channels($eventId) as $channel) {
        $rooms = [];
        $roomRows = db_all(
            'SELECT * FROM `rooms` WHERE `channel_id` = ? ORDER BY `id`',
            [$channel['id']]
        );
        foreach ($roomRows as $room) {
            $sessionRows = db_all(
                'SELECT * FROM `sessions` WHERE `room_id` = ? ORDER BY `start`, `id`',
                [$room['id']]
            );
            $rooms[] = [
                'id'       => (int) $room['id'],
                'name'     => $room['name'],
                'sessions' => array_map(static fn(array $s) => [
                    'id'          => (int) $s['id'],
                    'title'       => $s['title'],
                    'description' => $s['description'],
                    'speaker'     => $s['speaker'],
                    'start'       => $s['start'],
                    'end'         => $s['end'],
                    'type'        => $s['type'],
                    'cost'        => $s['cost'] === null ? null : (float) $s['cost'],
                ], $sessionRows),
            ];
        }
        $channels[] = [
            'id'    => (int) $channel['id'],
            'name'  => $channel['name'],
            'rooms' => $rooms,
        ];
    }

    $tickets = array_map(static fn(array $t) => [
        'id'          => (int) $t['id'],
        'name'        => $t['name'],
        'description' => ticket_description($t['special_validity']),
        'cost'        => (float) $t['cost'],
        'available'   => ticket_is_available($t),
    ], event_tickets($eventId));

    json_response([
        'id'       => $eventId,
        'name'     => $event['name'],
        'slug'     => $event['slug'],
        'date'     => $event['date'],
        'channels' => $channels,
        'tickets'  => $tickets,
    ]);
}

// ---------------------------------------------------------------------------
// B3a - POST /login：以「姓氏 + 報名代碼」登入，回傳 md5(username) 作為 token
// ---------------------------------------------------------------------------
if ($method === 'POST' && $segments === ['login']) {
    $body             = request_body();
    $lastname         = isset($body['lastname']) ? trim((string) $body['lastname']) : '';
    $registrationCode = isset($body['registration_code']) ? trim((string) $body['registration_code']) : '';

    if ($lastname === '' || $registrationCode === '') {
        json_response(['message' => 'Invalid login'], 401);
    }

    // 姓氏與報名代碼都必須符合（資料中有同姓、也有同代碼的人）
    $attendee = db_one(
        'SELECT * FROM `attendees` WHERE `lastname` = ? AND `registration_code` = ?',
        [$lastname, $registrationCode]
    );
    if ($attendee === null) {
        json_response(['message' => 'Invalid login'], 401);
    }

    $token = md5((string) $attendee['username']);
    db_exec('UPDATE `attendees` SET `login_token` = ? WHERE `id` = ?', [$token, $attendee['id']]);

    json_response([
        'firstname' => $attendee['firstname'],
        'lastname'  => $attendee['lastname'],
        'username'  => $attendee['username'],
        'email'     => $attendee['email'],
        'token'     => $token,
    ]);
}

// ---------------------------------------------------------------------------
// B3b - POST /logout?token=...：使 token 失效
// ---------------------------------------------------------------------------
if ($method === 'POST' && $segments === ['logout']) {
    $attendee = attendee_by_token($token);
    if ($attendee === null) {
        json_response(['message' => 'Invalid token'], 401);
    }
    db_exec('UPDATE `attendees` SET `login_token` = \'\' WHERE `id` = ?', [$attendee['id']]);
    json_response(['message' => 'Logout success']);
}

// ---------------------------------------------------------------------------
// B4b - GET /registrations?token=...：目前登入者的報名紀錄（依 id 由小到大）
// ---------------------------------------------------------------------------
if ($method === 'GET' && $segments === ['registrations']) {
    $attendee = attendee_by_token($token);
    if ($attendee === null) {
        json_response(['message' => 'User not logged in'], 401);
    }

    $rows = db_all(
        'SELECT reg.`id` AS registration_id,
                e.`id` AS event_id, e.`name` AS event_name, e.`slug` AS event_slug, e.`date` AS event_date,
                o.`id` AS organizer_id, o.`name` AS organizer_name, o.`slug` AS organizer_slug
           FROM `registrations` reg
           JOIN `event_tickets` t ON t.`id` = reg.`ticket_id`
           JOIN `events` e        ON e.`id` = t.`event_id`
           JOIN `organizers` o    ON o.`id` = e.`organizer_id`
          WHERE reg.`attendee_id` = ?
          ORDER BY reg.`id` ASC',
        [$attendee['id']]
    );

    $registrations = [];
    foreach ($rows as $row) {
        $sessionIds = db_all(
            'SELECT `session_id` FROM `session_registrations` WHERE `registration_id` = ? ORDER BY `session_id` ASC',
            [$row['registration_id']]
        );
        $registrations[] = [
            'event' => [
                'id'        => (int) $row['event_id'],
                'name'      => $row['event_name'],
                'slug'      => $row['event_slug'],
                'date'      => $row['event_date'],
                'organizer' => [
                    'id'   => (int) $row['organizer_id'],
                    'name' => $row['organizer_name'],
                    'slug' => $row['organizer_slug'],
                ],
            ],
            'session_ids' => array_map(static fn(array $s) => (int) $s['session_id'], $sessionIds),
        ];
    }

    json_response(['registrations' => $registrations]);
}

// ---------------------------------------------------------------------------
// B4a - POST /organizers/{o-slug}/events/{e-slug}/registration?token=...
// ---------------------------------------------------------------------------
if ($method === 'POST' && count($segments) === 5
    && $segments[0] === 'organizers' && $segments[2] === 'events' && $segments[4] === 'registration') {

    $attendee = attendee_by_token($token);
    if ($attendee === null) {
        json_response(['message' => 'User not logged in'], 401);
    }

    $organizer = db_one('SELECT * FROM `organizers` WHERE `slug` = ?', [$segments[1]]);
    if ($organizer === null) {
        json_response(['message' => 'Organizer not found'], 404);
    }
    $event = db_one(
        'SELECT * FROM `events` WHERE `slug` = ? AND `organizer_id` = ?',
        [$segments[3], $organizer['id']]
    );
    if ($event === null) {
        json_response(['message' => 'Event not found'], 404);
    }
    $eventId = (int) $event['id'];

    // 同一位參加者對同一個活動只能報名一次
    $existing = db_one(
        'SELECT reg.`id`
           FROM `registrations` reg
           JOIN `event_tickets` t ON t.`id` = reg.`ticket_id`
          WHERE reg.`attendee_id` = ? AND t.`event_id` = ?',
        [$attendee['id'], $eventId]
    );
    if ($existing !== null) {
        json_response(['message' => 'User already registered'], 401);
    }

    $body     = request_body();
    $ticketId = isset($body['ticket_id']) ? (int) $body['ticket_id'] : 0;
    $ticket   = db_one('SELECT * FROM `event_tickets` WHERE `id` = ? AND `event_id` = ?', [$ticketId, $eventId]);
    if ($ticket === null || !ticket_is_available($ticket)) {
        json_response(['message' => 'Ticket is no longer available'], 401);
    }

    // session_ids 為選填，且只接受屬於本活動的 workshop
    $sessionIds = $body['session_ids'] ?? [];
    if (is_string($sessionIds)) {
        $decoded    = json_decode($sessionIds, true);
        $sessionIds = is_array($decoded) ? $decoded : array_filter(explode(',', $sessionIds), 'strlen');
    }
    $sessionIds = is_array($sessionIds) ? array_map('intval', $sessionIds) : [];

    $pdo = db();
    $pdo->beginTransaction();
    try {
        db_exec(
            'INSERT INTO `registrations` (`attendee_id`, `ticket_id`, `registration_time`) VALUES (?, ?, ?)',
            [$attendee['id'], $ticketId, now()]
        );
        $registrationId = (int) $pdo->lastInsertId();

        foreach (array_unique($sessionIds) as $sessionId) {
            $valid = db_one(
                'SELECT s.`id`
                   FROM `sessions` s
                   JOIN `rooms` r    ON r.`id` = s.`room_id`
                   JOIN `channels` c ON c.`id` = r.`channel_id`
                  WHERE s.`id` = ? AND c.`event_id` = ?',
                [$sessionId, $eventId]
            );
            if ($valid !== null) {
                db_exec(
                    'INSERT INTO `session_registrations` (`registration_id`, `session_id`) VALUES (?, ?)',
                    [$registrationId, $sessionId]
                );
            }
        }
        $pdo->commit();
    } catch (Throwable $exception) {
        $pdo->rollBack();
        json_response(['message' => 'Registration failed'], 500);
    }

    json_response(['message' => 'Registration successful']);
}

// ---------------------------------------------------------------------------
// 找不到對應的端點
// ---------------------------------------------------------------------------
json_response(['message' => 'Not found'], 404);

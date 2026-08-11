<?php
/**
 * 領域邏輯：票券效期規則、活動關聯資料查詢、報名人數統計等
 * 後台頁面與 REST API 共用，避免邏輯重複。
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * 解析票券的特殊效期規則（存於 event_tickets.special_validity 的 JSON）
 *
 * @return array{type: string, date?: string, amount?: int}|null
 */
function ticket_validity(?string $specialValidity): ?array
{
    if ($specialValidity === null || trim($specialValidity) === '') {
        return null;
    }
    $rule = json_decode($specialValidity, true);
    return is_array($rule) && isset($rule['type']) ? $rule : null;
}

/**
 * 依規格產生票券描述字串
 *   - 無特殊規則           => null
 *   - type = date          => "Available until September 1, 2019"
 *   - type = amount        => "30 tickets available"（總量，不是剩餘量）
 */
function ticket_description(?string $specialValidity): ?string
{
    $rule = ticket_validity($specialValidity);
    if ($rule === null) {
        return null;
    }
    if ($rule['type'] === 'date' && !empty($rule['date'])) {
        $date = date_create((string) $rule['date']);
        return $date ? 'Available until ' . $date->format('F j, Y') : null;
    }
    if ($rule['type'] === 'amount' && isset($rule['amount'])) {
        return ((int) $rule['amount']) . ' tickets available';
    }
    return null;
}

/**
 * 票券目前是否仍可購買
 *   - date   規則：基準日期不可超過指定日期
 *   - amount 規則：已售出數量必須小於上限
 */
function ticket_is_available(array $ticket): bool
{
    $rule = ticket_validity($ticket['special_validity'] ?? null);
    if ($rule === null) {
        return true;
    }
    if ($rule['type'] === 'date') {
        return empty($rule['date']) || today() <= substr((string) $rule['date'], 0, 10);
    }
    if ($rule['type'] === 'amount') {
        $sold = (int) db_one(
            'SELECT COUNT(*) AS c FROM `registrations` WHERE `ticket_id` = ?',
            [$ticket['id']]
        )['c'];
        return $sold < (int) $rule['amount'];
    }
    return true;
}

/**
 * 取得活動的票券清單
 *
 * @return array<int, array<string, mixed>>
 */
function event_tickets(int $eventId): array
{
    return db_all('SELECT * FROM `event_tickets` WHERE `event_id` = ? ORDER BY `id`', [$eventId]);
}

/**
 * 取得活動的頻道清單
 *
 * @return array<int, array<string, mixed>>
 */
function event_channels(int $eventId): array
{
    return db_all('SELECT * FROM `channels` WHERE `event_id` = ? ORDER BY `id`', [$eventId]);
}

/**
 * 取得活動的所有房間（含所屬頻道名稱）
 *
 * @return array<int, array<string, mixed>>
 */
function event_rooms(int $eventId): array
{
    return db_all(
        'SELECT r.*, c.`name` AS channel_name
           FROM `rooms` r
           JOIN `channels` c ON c.`id` = r.`channel_id`
          WHERE c.`event_id` = ?
          ORDER BY c.`id`, r.`id`',
        [$eventId]
    );
}

/**
 * 取得活動的所有議程（含房間與頻道名稱），依開始時間排序
 *
 * @return array<int, array<string, mixed>>
 */
function event_sessions(int $eventId): array
{
    return db_all(
        'SELECT s.*, r.`name` AS room_name, r.`capacity` AS room_capacity,
                c.`name` AS channel_name, c.`id` AS channel_id
           FROM `sessions` s
           JOIN `rooms` r    ON r.`id` = s.`room_id`
           JOIN `channels` c ON c.`id` = r.`channel_id`
          WHERE c.`event_id` = ?
          ORDER BY s.`start`, s.`id`',
        [$eventId]
    );
}

/**
 * 活動的報名總數
 */
function event_registration_count(int $eventId): int
{
    return (int) db_one(
        'SELECT COUNT(*) AS c
           FROM `registrations` reg
           JOIN `event_tickets` t ON t.`id` = reg.`ticket_id`
          WHERE t.`event_id` = ?',
        [$eventId]
    )['c'];
}

/**
 * 單一議程的參加人數
 *   - talk     ：包含在活動門票內，等同該活動的報名總數
 *   - workshop ：需要額外報名，只計算 session_registrations
 */
function session_attendee_count(array $session, int $eventRegistrations): int
{
    if (($session['type'] ?? '') === 'talk') {
        return $eventRegistrations;
    }
    return (int) db_one(
        'SELECT COUNT(*) AS c FROM `session_registrations` WHERE `session_id` = ?',
        [$session['id']]
    )['c'];
}

/**
 * 檢查房間在指定時段是否已有其他議程（時間區間重疊即視為衝突）
 *
 * @param int|null $ignoreSessionId 編輯時要排除自己
 */
function room_is_booked(int $roomId, string $start, string $end, ?int $ignoreSessionId = null): bool
{
    $sql = 'SELECT COUNT(*) AS c
              FROM `sessions`
             WHERE `room_id` = ?
               AND `start` < ?
               AND `end` > ?';
    $params = [$roomId, $end, $start];
    if ($ignoreSessionId !== null) {
        $sql .= ' AND `id` <> ?';
        $params[] = $ignoreSessionId;
    }
    return ((int) db_one($sql, $params)['c']) > 0;
}

/**
 * 驗證 slug 格式：不可為空，且只能包含 a-z、0-9 與「-」
 */
function slug_is_valid(string $slug): bool
{
    return $slug !== '' && preg_match('/^[a-z0-9-]+$/', $slug) === 1;
}

/**
 * 驗證日期字串是否符合指定格式
 */
function is_valid_datetime(string $value, string $format): bool
{
    $dt = DateTime::createFromFormat($format, $value);
    return $dt !== false && $dt->format($format) === $value;
}

/**
 * 將表單輸入的日期時間標準化為 "Y-m-d H:i:s"，格式不合法時回傳 null
 */
function normalize_datetime(string $value): ?string
{
    $value = trim($value);
    foreach (['Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d\TH:i', 'Y-m-d\TH:i:s'] as $format) {
        $dt = DateTime::createFromFormat($format, $value);
        if ($dt !== false && $dt->format($format) === $value) {
            return $dt->format('Y-m-d H:i:s');
        }
    }
    return null;
}

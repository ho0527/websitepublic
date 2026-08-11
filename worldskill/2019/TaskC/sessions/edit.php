<?php
/**
 * A4d / A4e - 編輯議程
 *   只能編輯屬於目前登入主辦者的議程；房間時段衝突同樣要檢查（排除自己）
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$sessionId = (int) ($_GET['id'] ?? 0);

// 一併確認這場議程確實屬於目前登入的主辦者
$session = db_one(
    'SELECT s.*, c.`event_id`
       FROM `sessions` s
       JOIN `rooms` r    ON r.`id` = s.`room_id`
       JOIN `channels` c ON c.`id` = r.`channel_id`
       JOIN `events` ev  ON ev.`id` = c.`event_id`
      WHERE s.`id` = ? AND ev.`organizer_id` = ?',
    [$sessionId, $organizer['id']]
);
if ($session === null) {
    http_response_code(404);
    exit('Session not found');
}

$event   = require_own_event((int) $session['event_id'], $organizer);
$eventId = (int) $event['id'];
$rooms   = event_rooms($eventId);

$values = [
    'type'        => (string) $session['type'],
    'title'       => (string) $session['title'],
    'speaker'     => (string) $session['speaker'],
    'room'        => (string) $session['room_id'],
    'cost'        => $session['cost'] === null ? '' : (string) (float) $session['cost'],
    'start'       => date('Y-m-d H:i', strtotime((string) $session['start'])),
    'end'         => date('Y-m-d H:i', strtotime((string) $session['end'])),
    'description' => (string) $session['description'],
];
$errors    = [];
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($values) as $key) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if (!in_array($values['type'], ['talk', 'workshop'], true)) {
        $errors['type'] = 'Type is required.';
    }
    if ($values['title'] === '') {
        $errors['title'] = 'Title is required.';
    }
    if ($values['speaker'] === '') {
        $errors['speaker'] = 'Speaker is required.';
    }
    if ($values['description'] === '') {
        $errors['description'] = 'Description is required.';
    }
    $validRoomIds = array_map(static fn($r) => (string) $r['id'], $rooms);
    if (!in_array($values['room'], $validRoomIds, true)) {
        $errors['room'] = 'Room is required.';
    }

    $start = normalize_datetime($values['start']);
    $end   = normalize_datetime($values['end']);
    if ($start === null) {
        $errors['start'] = 'Start is required (yyyy-mm-dd HH:MM).';
    }
    if ($end === null) {
        $errors['end'] = 'End is required (yyyy-mm-dd HH:MM).';
    }
    if ($start !== null && $end !== null && $end <= $start) {
        $errors['end'] = 'End must be after start.';
    }

    if (!$errors && room_is_booked((int) $values['room'], $start, $end, $sessionId)) {
        $formError = 'Room already booked during this time';
    }

    if (!$errors && $formError === '') {
        $cost = ($values['type'] === 'workshop' && $values['cost'] !== '' && is_numeric($values['cost']) && (float) $values['cost'] > 0)
            ? (float) $values['cost']
            : null;

        db_exec(
            'UPDATE `sessions`
                SET `room_id` = ?, `title` = ?, `description` = ?, `speaker` = ?,
                    `start` = ?, `end` = ?, `type` = ?, `cost` = ?
              WHERE `id` = ?',
            [
                (int) $values['room'], $values['title'], $values['description'], $values['speaker'],
                $start, $end, $values['type'], $cost, $sessionId,
            ]
        );
        set_flash('success', 'Session successfully updated');
        redirect('events/detail.php?id=' . $eventId);
    }
}

render_header($organizer, $event, 'overview');
?>
<div class="border-bottom mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h1 class="h2"><?= e($event['name']) ?></h1>
    </div>
    <span class="h6"><?= e(format_event_date($event['date'])) ?></span>
</div>

<div class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Edit session</h2>
    </div>
</div>

<?php if ($formError !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= e($formError) ?></div>
<?php endif; ?>

<form class="needs-validation" novalidate method="post" action="sessions/edit.php?id=<?= $sessionId ?>">
    <?php require __DIR__ . '/_form.php'; ?>

    <hr class="mb-4">
    <button class="btn btn-primary" type="submit">Save session</button>
    <a href="events/detail.php?id=<?= $eventId ?>" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

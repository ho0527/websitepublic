<?php
/**
 * A4a / A4b / A4c - 建立新議程
 *   除 cost 外所有欄位必填；同一房間在同一時段不可有兩場議程
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['event_id'] ?? 0), $organizer);
$eventId   = (int) $event['id'];

$rooms  = event_rooms($eventId);
$values = [
    'type'        => 'talk',
    'title'       => '',
    'speaker'     => '',
    'room'        => '',
    'cost'        => '',
    'start'       => '',
    'end'         => '',
    'description' => '',
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
    // 房間只能選本活動底下的房間
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

    // 房間時段衝突檢查
    if (!$errors && room_is_booked((int) $values['room'], $start, $end)) {
        $formError = 'Room already booked during this time';
    }

    if (!$errors && $formError === '') {
        $cost = ($values['type'] === 'workshop' && $values['cost'] !== '' && is_numeric($values['cost']) && (float) $values['cost'] > 0)
            ? (float) $values['cost']
            : null;

        db_exec(
            'INSERT INTO `sessions` (`room_id`, `title`, `description`, `speaker`, `start`, `end`, `type`, `cost`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [
                (int) $values['room'], $values['title'], $values['description'], $values['speaker'],
                $start, $end, $values['type'], $cost,
            ]
        );
        set_flash('success', 'Session successfully created');
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
        <h2 class="h4">Create new session</h2>
    </div>
</div>

<?php if ($formError !== ''): ?>
    <div class="alert alert-danger" role="alert"><?= e($formError) ?></div>
<?php endif; ?>

<form class="needs-validation" novalidate method="post" action="sessions/create.php?event_id=<?= $eventId ?>">
    <?php require __DIR__ . '/_form.php'; ?>

    <hr class="mb-4">
    <button class="btn btn-primary" type="submit">Save session</button>
    <a href="events/detail.php?id=<?= $eventId ?>" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

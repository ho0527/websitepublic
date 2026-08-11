<?php
/**
 * A6a / A6b - 建立新房間（房間固定隸屬於一個頻道）
 *   頻道下拉選單只會列出目前活動的頻道，避免跨活動、跨主辦者建立資料
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['event_id'] ?? 0), $organizer);
$eventId   = (int) $event['id'];

$channels = event_channels($eventId);
$values   = ['name' => '', 'channel' => '', 'capacity' => ''];
$errors   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($values) as $key) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if ($values['name'] === '') {
        $errors['name'] = 'Name is required.';
    }
    // 只接受屬於本活動的頻道 id
    $validChannelIds = array_map(static fn($c) => (string) $c['id'], $channels);
    if (!in_array($values['channel'], $validChannelIds, true)) {
        $errors['channel'] = 'Channel is required.';
    }
    if ($values['capacity'] === '' || !ctype_digit($values['capacity']) || (int) $values['capacity'] <= 0) {
        $errors['capacity'] = 'Capacity is required.';
    }

    if (!$errors) {
        db_exec(
            'INSERT INTO `rooms` (`channel_id`, `name`, `capacity`) VALUES (?, ?, ?)',
            [(int) $values['channel'], $values['name'], (int) $values['capacity']]
        );
        set_flash('success', 'Room successfully created');
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
        <h2 class="h4">Create new room</h2>
    </div>
</div>

<?php if (!$channels): ?>
    <div class="alert alert-warning">Please create a channel first.</div>
<?php endif; ?>

<form class="needs-validation" novalidate method="post" action="rooms/create.php?event_id=<?= $eventId ?>">

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="inputName">Name</label>
            <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                   id="inputName" name="name" placeholder="" value="<?= e($values['name']) ?>">
            <div class="invalid-feedback"><?= e($errors['name'] ?? '') ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="selectChannel">Channel</label>
            <select class="form-control<?= isset($errors['channel']) ? ' is-invalid' : '' ?>"
                    id="selectChannel" name="channel">
                <?php foreach ($channels as $channel): ?>
                    <option value="<?= (int) $channel['id'] ?>"
                        <?= $values['channel'] === (string) $channel['id'] ? 'selected' : '' ?>>
                        <?= e($channel['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback"><?= e($errors['channel'] ?? '') ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="inputCapacity">Capacity</label>
            <input type="number" class="form-control<?= isset($errors['capacity']) ? ' is-invalid' : '' ?>"
                   id="inputCapacity" name="capacity" placeholder="" value="<?= e($values['capacity']) ?>">
            <div class="invalid-feedback"><?= e($errors['capacity'] ?? '') ?></div>
        </div>
    </div>

    <hr class="mb-4">
    <button class="btn btn-primary" type="submit">Save room</button>
    <a href="events/detail.php?id=<?= $eventId ?>" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

<?php
/**
 * A5a / A5b - 建立新頻道（頻道固定隸屬於一個活動）
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['event_id'] ?? 0), $organizer);
$eventId   = (int) $event['id'];

$values = ['name' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name'] = trim((string) ($_POST['name'] ?? ''));

    if ($values['name'] === '') {
        $errors['name'] = 'Name is required.';
    }

    if (!$errors) {
        db_exec('INSERT INTO `channels` (`event_id`, `name`) VALUES (?, ?)', [$eventId, $values['name']]);
        set_flash('success', 'Channel successfully created');
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
        <h2 class="h4">Create new channel</h2>
    </div>
</div>

<form class="needs-validation" novalidate method="post" action="channels/create.php?event_id=<?= $eventId ?>">

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="inputName">Name</label>
            <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                   id="inputName" name="name" placeholder="" value="<?= e($values['name']) ?>">
            <div class="invalid-feedback"><?= e($errors['name'] ?? '') ?></div>
        </div>
    </div>

    <hr class="mb-4">
    <button class="btn btn-primary" type="submit">Save channel</button>
    <a href="events/detail.php?id=<?= $eventId ?>" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

<?php
/**
 * A2f / A2g - 編輯既有活動
 *   只能編輯屬於目前登入主辦者的活動（多租戶隔離）
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['id'] ?? 0), $organizer);

$values = [
    'name' => (string) $event['name'],
    'slug' => (string) $event['slug'],
    'date' => (string) $event['date'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name'] = trim((string) ($_POST['name'] ?? ''));
    $values['slug'] = trim((string) ($_POST['slug'] ?? ''));
    $values['date'] = trim((string) ($_POST['date'] ?? ''));

    if ($values['name'] === '') {
        $errors['name'] = 'Name is required.';
    }
    if (!slug_is_valid($values['slug'])) {
        $errors['slug'] = "Slug must not be empty and only contain a-z, 0-9 and '-'";
    } elseif (db_one(
        'SELECT `id` FROM `events` WHERE `organizer_id` = ? AND `slug` = ? AND `id` <> ?',
        [$organizer['id'], $values['slug'], $event['id']]
    ) !== null) {
        $errors['slug'] = 'Slug is already used';
    }
    if ($values['date'] === '' || !is_valid_datetime($values['date'], 'Y-m-d')) {
        $errors['date'] = 'Date is required (yyyy-mm-dd).';
    }

    if (!$errors) {
        db_exec(
            'UPDATE `events` SET `name` = ?, `slug` = ?, `date` = ? WHERE `id` = ? AND `organizer_id` = ?',
            [$values['name'], $values['slug'], $values['date'], $event['id'], $organizer['id']]
        );
        set_flash('success', 'Event successfully updated');
        redirect('events/detail.php?id=' . (int) $event['id']);
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
        <h2 class="h4">Edit event</h2>
    </div>
</div>

<form class="needs-validation" novalidate method="post" action="events/edit.php?id=<?= (int) $event['id'] ?>">

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
            <label for="inputSlug">Slug</label>
            <input type="text" class="form-control<?= isset($errors['slug']) ? ' is-invalid' : '' ?>"
                   id="inputSlug" name="slug" placeholder="" value="<?= e($values['slug']) ?>">
            <div class="invalid-feedback"><?= e($errors['slug'] ?? '') ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="inputDate">Date</label>
            <input type="text"
                   class="form-control<?= isset($errors['date']) ? ' is-invalid' : '' ?>"
                   id="inputDate"
                   name="date"
                   placeholder="yyyy-mm-dd"
                   value="<?= e($values['date']) ?>">
            <div class="invalid-feedback"><?= e($errors['date'] ?? '') ?></div>
        </div>
    </div>

    <hr class="mb-4">
    <button class="btn btn-primary" type="submit">Save event</button>
    <a href="events/detail.php?id=<?= (int) $event['id'] ?>" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

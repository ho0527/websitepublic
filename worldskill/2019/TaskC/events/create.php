<?php
/**
 * A2b / A2c / A2d / A2e - 建立新活動
 *   - 所有欄位必填
 *   - slug 只能包含 a-z、0-9 與「-」
 *   - 同一位主辦者底下 slug 不可重複
 *   - 驗證失敗時保留使用者剛才輸入的內容
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();

$values = ['name' => '', 'slug' => '', 'date' => ''];
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
        'SELECT `id` FROM `events` WHERE `organizer_id` = ? AND `slug` = ?',
        [$organizer['id'], $values['slug']]
    ) !== null) {
        $errors['slug'] = 'Slug is already used';
    }
    if ($values['date'] === '' || !is_valid_datetime($values['date'], 'Y-m-d')) {
        $errors['date'] = 'Date is required (yyyy-mm-dd).';
    }

    if (!$errors) {
        db_exec(
            'INSERT INTO `events` (`organizer_id`, `name`, `slug`, `date`) VALUES (?, ?, ?, ?)',
            [$organizer['id'], $values['name'], $values['slug'], $values['date']]
        );
        $newId = (int) db()->lastInsertId();
        set_flash('success', 'Event successfully created');
        redirect('events/detail.php?id=' . $newId);
    }
}

render_header($organizer, null, 'events');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Events</h1>
</div>

<div class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Create new event</h2>
    </div>
</div>

<form class="needs-validation" novalidate method="post" action="events/create.php">

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
    <a href="events/index.php" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

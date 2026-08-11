<?php
/**
 * A3a / A3b - 建立新票券
 *   name 與 cost 必填；若選擇特殊效期規則，該規則對應的欄位也必填
 *   規則寫入 event_tickets.special_validity（JSON），格式與提供的 dump 一致：
 *     {"type":"date","date":"2019-06-01"} 或 {"type":"amount","amount":50}
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['event_id'] ?? 0), $organizer);
$eventId   = (int) $event['id'];

$values = ['name' => '', 'cost' => '0', 'special_validity' => '', 'amount' => '0', 'valid_until' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($values) as $key) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if ($values['name'] === '') {
        $errors['name'] = 'Name is required.';
    }
    if ($values['cost'] === '' || !is_numeric($values['cost'])) {
        $errors['cost'] = 'Cost is required.';
    }

    $specialValidity = null;
    if ($values['special_validity'] === 'amount') {
        if ($values['amount'] === '' || !ctype_digit($values['amount']) || (int) $values['amount'] <= 0) {
            $errors['amount'] = 'Amount is required.';
        } else {
            $specialValidity = json_encode(['type' => 'amount', 'amount' => (int) $values['amount']]);
        }
    } elseif ($values['special_validity'] === 'date') {
        $date = substr($values['valid_until'], 0, 10);
        if (!is_valid_datetime($date, 'Y-m-d')) {
            $errors['valid_until'] = 'A valid date is required.';
        } else {
            $specialValidity = json_encode(['type' => 'date', 'date' => $date]);
        }
    }

    if (!$errors) {
        db_exec(
            'INSERT INTO `event_tickets` (`event_id`, `name`, `cost`, `special_validity`) VALUES (?, ?, ?, ?)',
            [$eventId, $values['name'], (float) $values['cost'], $specialValidity]
        );
        set_flash('success', 'Ticket successfully created');
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
        <h2 class="h4">Create new ticket</h2>
    </div>
</div>

<form class="needs-validation" novalidate method="post" action="tickets/create.php?event_id=<?= $eventId ?>">

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
            <label for="inputCost">Cost</label>
            <input type="number" step="0.01" class="form-control<?= isset($errors['cost']) ? ' is-invalid' : '' ?>"
                   id="inputCost" name="cost" placeholder="" value="<?= e($values['cost']) ?>">
            <div class="invalid-feedback"><?= e($errors['cost'] ?? '') ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="selectSpecialValidity">Special Validity</label>
            <select class="form-control" id="selectSpecialValidity" name="special_validity">
                <option value="" <?= $values['special_validity'] === '' ? 'selected' : '' ?>>None</option>
                <option value="amount" <?= $values['special_validity'] === 'amount' ? 'selected' : '' ?>>Limited amount</option>
                <option value="date" <?= $values['special_validity'] === 'date' ? 'selected' : '' ?>>Purchaseable till date</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="inputAmount">Maximum amount of tickets to be sold</label>
            <input type="number" class="form-control<?= isset($errors['amount']) ? ' is-invalid' : '' ?>"
                   id="inputAmount" name="amount" placeholder="" value="<?= e($values['amount']) ?>">
            <div class="invalid-feedback"><?= e($errors['amount'] ?? '') ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-4 mb-3">
            <label for="inputValidTill">Tickets can be sold until</label>
            <input type="text"
                   class="form-control<?= isset($errors['valid_until']) ? ' is-invalid' : '' ?>"
                   id="inputValidTill"
                   name="valid_until"
                   placeholder="yyyy-mm-dd"
                   value="<?= e($values['valid_until']) ?>">
            <div class="invalid-feedback"><?= e($errors['valid_until'] ?? '') ?></div>
        </div>
    </div>

    <hr class="mb-4">
    <button class="btn btn-primary" type="submit">Save ticket</button>
    <a href="events/detail.php?id=<?= $eventId ?>" class="btn btn-link">Cancel</a>
</form>
<?php
render_footer();

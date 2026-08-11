<?php
/**
 * 議程表單欄位（建立與編輯共用）
 * 需要外部先準備好 $values、$errors、$rooms 三個變數
 */

declare(strict_types=1);
?>
<div class="row">
    <div class="col-12 col-lg-4 mb-3">
        <label for="selectType">Type</label>
        <select class="form-control" id="selectType" name="type">
            <option value="talk" <?= $values['type'] === 'talk' ? 'selected' : '' ?>>Talk</option>
            <option value="workshop" <?= $values['type'] === 'workshop' ? 'selected' : '' ?>>Workshop</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-4 mb-3">
        <label for="inputTitle">Title</label>
        <input type="text" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>"
               id="inputTitle" name="title" placeholder="" value="<?= e($values['title']) ?>">
        <div class="invalid-feedback"><?= e($errors['title'] ?? '') ?></div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-4 mb-3">
        <label for="inputSpeaker">Speaker</label>
        <input type="text" class="form-control<?= isset($errors['speaker']) ? ' is-invalid' : '' ?>"
               id="inputSpeaker" name="speaker" placeholder="" value="<?= e($values['speaker']) ?>">
        <div class="invalid-feedback"><?= e($errors['speaker'] ?? '') ?></div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-4 mb-3">
        <label for="selectRoom">Room</label>
        <select class="form-control<?= isset($errors['room']) ? ' is-invalid' : '' ?>" id="selectRoom" name="room">
            <?php foreach ($rooms as $room): ?>
                <option value="<?= (int) $room['id'] ?>" <?= $values['room'] === (string) $room['id'] ? 'selected' : '' ?>>
                    <?= e($room['name']) ?> / <?= e($room['channel_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="invalid-feedback"><?= e($errors['room'] ?? 'Room is required.') ?></div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-4 mb-3">
        <label for="inputCost">Cost</label>
        <input type="number" step="0.01" class="form-control" id="inputCost" name="cost"
               placeholder="" value="<?= e($values['cost']) ?>">
        <small class="form-text text-muted">Only workshops can have an additional cost.</small>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-6 mb-3">
        <label for="inputStart">Start</label>
        <input type="text"
               class="form-control<?= isset($errors['start']) ? ' is-invalid' : '' ?>"
               id="inputStart"
               name="start"
               placeholder="yyyy-mm-dd HH:MM"
               value="<?= e($values['start']) ?>">
        <div class="invalid-feedback"><?= e($errors['start'] ?? '') ?></div>
    </div>
    <div class="col-12 col-lg-6 mb-3">
        <label for="inputEnd">End</label>
        <input type="text"
               class="form-control<?= isset($errors['end']) ? ' is-invalid' : '' ?>"
               id="inputEnd"
               name="end"
               placeholder="yyyy-mm-dd HH:MM"
               value="<?= e($values['end']) ?>">
        <div class="invalid-feedback"><?= e($errors['end'] ?? '') ?></div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-3">
        <label for="textareaDescription">Description</label>
        <textarea class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>"
                  id="textareaDescription" name="description" placeholder="" rows="5"><?= e($values['description']) ?></textarea>
        <div class="invalid-feedback"><?= e($errors['description'] ?? '') ?></div>
    </div>
</div>

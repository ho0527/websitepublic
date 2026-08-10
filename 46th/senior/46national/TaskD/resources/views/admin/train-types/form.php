<?php
/**
 * 車種新增／編輯表單。
 *
 * @var \App\Core\View               $view
 * @var \App\Models\TrainType|null   $trainType
 * @var array<int, string>           $errors
 * @var array<string, mixed>         $old
 * @var string                       $title
 */

$action = $trainType === null
    ? $view->url('admin/train-types')
    : $view->url('admin/train-types/' . $trainType->id());

$nameValue     = $old['name'] ?? ($trainType->name ?? '');
$capacityValue = $old['capacity'] ?? ($trainType->capacity ?? '');
?>
<h1 class="page-title"><?= $view->e($title) ?></h1>
<p class="page-subtitle">車種名稱不可重複，乘客承載量會用於計算各區間的剩餘座位。</p>

<?= $view->partial('partials/alerts', ['errors' => $errors]) ?>

<div class="card" style="max-width: 560px;">
    <h2 class="card-title">車種資訊</h2>

    <form method="post" action="<?= $view->e($action) ?>">
        <div class="field" style="margin-bottom: 14px;">
            <label for="name">車種名稱</label>
            <input type="text" id="name" name="name" value="<?= $view->e($nameValue) ?>" required>
        </div>

        <div class="field">
            <label for="capacity">乘客承載量</label>
            <input type="number" id="capacity" name="capacity" min="1"
                   value="<?= $view->e($capacityValue) ?>" required>
        </div>

        <div class="button-row">
            <button type="submit" class="button">儲存</button>
            <a class="button button-secondary" href="<?= $view->e($view->url('admin/train-types')) ?>">取消</a>
        </div>
    </form>
</div>

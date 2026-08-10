<?php
/**
 * 車種管理列表。
 *
 * @var \App\Core\View                    $view
 * @var array<int, \App\Models\TrainType> $trainTypes
 * @var array<int, string>                $errors
 * @var string|null                       $notice
 */
?>
<h1 class="page-title">車種管理</h1>
<p class="page-subtitle">管理系統內建與自訂的車種；已有列車使用的車種無法刪除。</p>

<?= $view->partial('partials/alerts', ['errors' => $errors, 'notice' => $notice]) ?>

<div class="card">
    <h2 class="card-title">
        車種清單
        <span class="card-hint">共 <?= $view->e(count($trainTypes)) ?> 種</span>
    </h2>

    <div class="table-scroll">
        <table class="data">
            <thead>
                <tr>
                    <th>編號</th>
                    <th>車種名稱</th>
                    <th class="numeric">乘客承載量</th>
                    <th>使用狀態</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trainTypes as $trainType): ?>
                    <?php $inUse = $trainType->isInUse(); ?>
                    <tr>
                        <td><?= $view->e($trainType->id()) ?></td>
                        <td><?= $view->e($trainType->name) ?></td>
                        <td class="numeric"><?= $view->e($trainType->capacity) ?> 人</td>
                        <td>
                            <?php if ($inUse): ?>
                                <span class="tag tag-booked">使用中</span>
                            <?php else: ?>
                                <span class="tag tag-departed">未使用</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="button button-secondary button-small"
                               href="<?= $view->e($view->url('admin/train-types/' . $trainType->id() . '/edit')) ?>">編輯</a>

                            <form method="post" style="display:inline;"
                                  action="<?= $view->e($view->url('admin/train-types/' . $trainType->id() . '/delete')) ?>"
                                  onsubmit="return confirm('確定要刪除車種「<?= $view->e($trainType->name) ?>」嗎？');">
                                <button type="submit" class="button button-danger button-small"
                                    <?= $inUse ? 'disabled title="仍有列車使用此車種，無法刪除"' : '' ?>>刪除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="button-row">
        <a class="button" href="<?= $view->e($view->url('admin/train-types/create')) ?>">新增車種</a>
    </div>
</div>

<?php
/**
 * 列車管理列表。
 *
 * @var \App\Core\View                   $view
 * @var array<int, array<string, mixed>> $rows
 * @var array<int, string>               $weekdayNames
 * @var array<int, string>               $errors
 * @var string|null                      $notice
 */
?>
<h1 class="page-title">列車管理</h1>
<p class="page-subtitle">管理各車次的車種、發車時間、行車星期與行經車站。</p>

<?= $view->partial('partials/alerts', ['errors' => $errors, 'notice' => $notice]) ?>

<div class="card">
    <h2 class="card-title">
        列車清單
        <span class="card-hint">共 <?= $view->e(count($rows)) ?> 個車次</span>
    </h2>

    <div class="table-scroll">
        <table class="data">
            <thead>
                <tr>
                    <th>列車代碼</th>
                    <th>車種</th>
                    <th>發車時間</th>
                    <th>行車星期</th>
                    <th>行經車站</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="code-text"><?= $view->e($row['train']->code) ?></td>
                        <td><?= $view->e($row['type_name']) ?></td>
                        <td><?= $view->e(substr((string) $row['train']->depart_time, 0, 5)) ?></td>
                        <td>
                            <?php
                            $dayLabels = array_map(
                                static fn (int $weekday): string => $weekdayNames[$weekday] ?? '',
                                $row['weekdays']
                            );
                            ?>
                            <?= $view->e(implode('、', $dayLabels)) ?>
                        </td>
                        <td style="white-space: normal; min-width: 260px;"><?= $view->e($row['route_text']) ?></td>
                        <td>
                            <a class="button button-secondary button-small"
                               href="<?= $view->e($view->url('admin/trains/' . $row['train']->id() . '/edit')) ?>">編輯</a>
                            <a class="button button-danger button-small"
                               href="<?= $view->e($view->url('admin/trains/' . $row['train']->id() . '/delete')) ?>">刪除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="button-row">
        <a class="button" href="<?= $view->e($view->url('admin/trains/create')) ?>">新增列車</a>
    </div>
</div>

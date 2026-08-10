<?php
/**
 * 列車資訊：輸入車次代碼。
 *
 * @var \App\Core\View                $view
 * @var array<int, \App\Models\Train> $trains
 * @var string|null                   $error
 */
?>
<h1 class="page-title">列車資訊</h1>
<p class="page-subtitle">輸入車次代碼，即可查詢該班次的行駛星期與各站時刻。</p>

<?php if ($error !== null): ?>
    <div class="alert alert-error"><?= $view->e($error) ?></div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title">查詢車次</h2>

    <form method="get" action="<?= $view->e($view->url('train-info')) ?>">
        <div class="field-grid">
            <div class="field" style="grid-column: span 2;">
                <label for="code">車次代碼</label>
                <input type="text" id="code" name="code" list="train-codes" placeholder="例如 1101" required>
                <datalist id="train-codes">
                    <?php foreach ($trains as $train): ?>
                        <option value="<?= $view->e($train->code) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="field">
                <button type="submit" class="button">查詢</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <h2 class="card-title">所有車次</h2>

    <div class="table-scroll">
        <table class="data">
            <thead>
                <tr>
                    <th>車次代碼</th>
                    <th>車種</th>
                    <th>發車時間</th>
                    <th>行駛區間</th>
                    <th>詳細資訊</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trains as $train): ?>
                    <tr>
                        <td class="code-text"><?= $view->e($train->code) ?></td>
                        <td><?= $view->e($train->type()?->name ?? '') ?></td>
                        <td><?= $view->e(substr((string) $train->depart_time, 0, 5)) ?></td>
                        <td>
                            <?= $view->e($train->originStop()?->station()?->name ?? '') ?>
                            →
                            <?= $view->e($train->terminusStop()?->station()?->name ?? '') ?>
                        </td>
                        <td>
                            <a class="button button-secondary button-small"
                               href="<?= $view->e($view->url('train-info/' . rawurlencode((string) $train->code))) ?>">查看時刻</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

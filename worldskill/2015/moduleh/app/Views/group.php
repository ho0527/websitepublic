<?php
/**
 * 團體訂位：以分頁（tab）呈現四個競賽日，每個場次可動態增加賓客欄位。
 *
 * 表單欄位命名沿用官方樣板 restaurantapp.js 的規則：
 *   c{競賽日序號}-d{場次序號}-n{列號}  賓客姓名
 *   c{競賽日序號}-d{場次序號}-o{列號}  賓客國家
 *
 * @var array $contact      聯絡人資料
 * @var array $days         競賽日
 * @var array $seatings     場次
 * @var array $availability [日 id][場次 id] => 剩餘座位數
 * @var array $guests       驗證失敗時回填用的賓客輸入資料
 * @var array $errors       錯誤訊息
 */

use App\Core\Countries;
use App\Core\Url;
use App\Core\View;
use App\Models\CompetitionDay;
use App\Services\BookingService;

/** 取得某個場次已輸入的列（至少回傳一列空白列） */
$rowsFor = static function (int $dayIndex, int $seatingIndex) use ($guests): array {
    return $guests[$dayIndex . '-' . $seatingIndex] ?? [];
};

// 驗證失敗時，預設開啟第一個有輸入資料的競賽日分頁，方便使用者直接修正
$activeDayIndex = 0;

foreach ($guests as $key => $rows) {
    foreach ($rows as $row) {
        if ($row['country'] !== '') {
            $activeDayIndex = ((int) explode('-', $key)[0]) - 1;
            break 2;
        }
    }
}
?>
                <div id="booking_request">
                    <h1 class="page-header">Booking request</h1>

                    <form action="<?= View::e(Url::to('booking/group')) ?>" method="post">
                        <fieldset>
                            <legend>Group</legend>
                            <p>Booking a group for
                                <strong><?= View::e($contact['name']) ?></strong>
                                <?= $contact['organization'] !== '' ? '(' . View::e($contact['organization']) . ')' : '' ?>
                                &ndash; the booking contact does not request a seat.</p>

                            <?php if ($errors !== []): ?>
                                <div class="alert alert-danger error-message">
                                    <strong>Too many guests</strong>
                                    <?php foreach ($errors as $message): ?>
                                        <div><?= View::e($message) ?></div>
                                    <?php endforeach; ?>
                                    <div>Please edit your booking request.</div>
                                </div>
                            <?php endif; ?>

                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" role="tablist">
                                <?php foreach ($days as $dayIndex => $day): ?>
                                    <li role="presentation"<?= $dayIndex === $activeDayIndex ? ' class="active"' : '' ?>>
                                        <a href="#c<?= $dayIndex + 1 ?>" aria-controls="c<?= $dayIndex + 1 ?>" role="tab" data-toggle="tab"><?= View::e(CompetitionDay::label($day)) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <?php foreach ($days as $dayIndex => $day): ?>
                                    <div role="tabpanel" class="tab-pane<?= $dayIndex === $activeDayIndex ? ' active' : '' ?>" id="c<?= $dayIndex + 1 ?>">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Dining experience</th>
                                                        <th>Number of seats available<br/>Number of guests to be seated</th>
                                                        <th>Guest names (if known)</th>
                                                        <th>Guest country*</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($seatings as $seatingIndex => $seating): ?>
                                                        <?php
                                                            $prefix = BookingService::fieldPrefix($dayIndex + 1, $seatingIndex + 1);
                                                            $free   = $availability[(int) $day['id']][(int) $seating['id']] ?? 0;
                                                            $rows   = $rowsFor($dayIndex + 1, $seatingIndex + 1);
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= View::e($seating['module_name']) ?><br/>
                                                                <?= View::e($seating['time_label']) ?>
                                                            </td>
                                                            <td>
                                                                available: <?= (int) $free ?><br/>
                                                                <small class="text-muted">max. <?= (int) $seating['max_per_country'] ?> per country</small><br/>
                                                                <button type="button" class="btn btn-default addguest" id="<?= View::e($prefix) ?>">+ Add guest</button>
                                                            </td>
                                                            <td id="<?= View::e($prefix) ?>-n">
                                                                <?php foreach ($rows as $rowIndex => $row): ?>
                                                                    <p><input type="text" id="<?= View::e($prefix) ?>-n<?= $rowIndex + 1 ?>" name="<?= View::e($prefix) ?>-n<?= $rowIndex + 1 ?>" class="form-control" value="<?= View::e($row['name']) ?>"></p>
                                                                <?php endforeach; ?>
                                                            </td>
                                                            <td id="<?= View::e($prefix) ?>-o">
                                                                <?php foreach ($rows as $rowIndex => $row): ?>
                                                                    <p>
                                                                        <select id="<?= View::e($prefix) ?>-o<?= $rowIndex + 1 ?>" name="<?= View::e($prefix) ?>-o<?= $rowIndex + 1 ?>" class="form-control">
                                                                            <?= Countries::options($row['country']) ?>
                                                                        </select>
                                                                    </p>
                                                                <?php endforeach; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p>Please note that most seating take place at the same time and you are not allowed to change once seated.<br />For a seating that is full, you will be waitlisted.<br />* Guests must be identified by their country at a minimum &ndash; rows without a country are ignored.</p>
                        </fieldset>
                        <button class="btn btn-primary" type="submit" name="book-group">Submit booking request</button>
                    </form>

                    <!-- 國家清單樣板：restaurantapp.js 動態新增欄位時會複製這裡的選項 -->
                    <select id="guest-country-template" class="hidden" aria-hidden="true" tabindex="-1">
                        <?= Countries::options() ?>
                    </select>
                </div>

<?php
/**
 * 訂位管理（WSI 工作人員）。
 *
 * 排序：競賽日 -> 場次 -> 狀態（confirmed, requested, waitlisted, declined）-> 訂位編號
 * 每個「日 + 場次」重新編號，方便判斷還能再放多少賓客。
 *
 * @var array $reservations 訂位清單
 * @var array $days         競賽日（改期下拉選單用）
 * @var array $seatings     場次（改期下拉選單用）
 * @var array $messages     操作結果訊息
 */

use App\Core\Url;
use App\Core\View;
use App\Models\CompetitionDay;

/** 狀態對應的 Bootstrap 標籤樣式 */
$statusClass = [
    'confirmed'  => 'label label-success',
    'requested'  => 'label label-warning',
    'waitlisted' => 'label label-info',
    'declined'   => 'label label-danger',
];
?>
                <div id="reservation_management">
                    <h1 class="page-header">Reservation management</h1>

                    <?php foreach ($messages as $message): ?>
                        <div class="alert alert-info"><?= View::e($message) ?></div>
                    <?php endforeach; ?>

                    <form action="<?= View::e(Url::management()) ?>" method="post">
                        <fieldset>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Day</th>
                                            <th rowspan="2">Seating</th>
                                            <th rowspan="2">Booking No.</th>
                                            <th rowspan="2">Guests</th>
                                            <th rowspan="2">Status</th>
                                            <th colspan="4">Action</th>
                                        </tr>
                                        <tr>
                                            <th>Confirm</th>
                                            <th>Decline</th>
                                            <th>Waitlist</th>
                                            <th>Reschedule</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($reservations === []): ?>
                                            <tr><td colspan="9">There are no reservations yet.</td></tr>
                                        <?php endif; ?>

                                        <?php foreach ($reservations as $reservation): ?>
                                            <?php
                                                $id       = (int) $reservation['id'];
                                                $isOpen   = $reservation['status'] === 'requested';
                                                $needsNew = $isOpen && (int) $reservation['needs_reschedule'] === 1;

                                                $contactTitle = implode(', ', array_filter([
                                                    $reservation['contact_name'],
                                                    $reservation['organization'],
                                                    $reservation['phone'],
                                                    $reservation['email'],
                                                    $reservation['contact_country'],
                                                ]));
                                            ?>
                                            <tr>
                                                <td>
                                                    <?php if ($needsNew): ?>
                                                        <select name="reschedule_day[<?= $id ?>]" class="form-control input-sm">
                                                            <?php foreach ($days as $day): ?>
                                                                <option value="<?= (int) $day['id'] ?>"<?= (int) $day['id'] === (int) $reservation['competition_day_id'] ? ' selected' : '' ?>>
                                                                    <?= View::e(CompetitionDay::label($day)) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <?= View::e($reservation['day_code']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($needsNew): ?>
                                                        <select name="reschedule_seating[<?= $id ?>]" class="form-control input-sm">
                                                            <?php foreach ($seatings as $seating): ?>
                                                                <option value="<?= (int) $seating['id'] ?>"<?= (int) $seating['id'] === (int) $reservation['seating_id'] ? ' selected' : '' ?>>
                                                                    <?= View::e($seating['label']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    <?php else: ?>
                                                        <?= View::e($reservation['module_name'] . ' ' . $reservation['time_label']) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span title="<?= View::e($contactTitle) ?>"><?= View::e($reservation['booking_no']) ?></span></td>
                                                <td>
                                                    <?= (int) $reservation['seq'] ?>.
                                                    <?= View::e(($reservation['guest_name'] ?? '') !== '' ? $reservation['guest_name'] : '(name not given)') ?>
                                                    <?= View::e($reservation['guest_country']) ?>
                                                </td>
                                                <td>
                                                    <span class="<?= View::e($statusClass[$reservation['status']] ?? 'label label-default') ?>"><?= View::e($reservation['status']) ?></span>
                                                    <?php if ($needsNew): ?>
                                                        <br/><small class="text-muted">to be rescheduled</small>
                                                    <?php endif; ?>
                                                </td>
                                                <?php if ($isOpen): ?>
                                                    <td><p class="text-center"><input type="radio" name="action[<?= $id ?>]" value="confirm"></p></td>
                                                    <td><p class="text-center"><input type="radio" name="action[<?= $id ?>]" value="decline"></p></td>
                                                    <td><p class="text-center"><input type="radio" name="action[<?= $id ?>]" value="waitlist"></p></td>
                                                    <td><p class="text-center"><input type="radio" name="action[<?= $id ?>]" value="reschedule"<?= $needsNew ? ' checked' : '' ?>></p></td>
                                                <?php else: ?>
                                                    <td></td><td></td><td></td><td></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                        <a class="btn btn-default" href="<?= View::e(Url::managementPage('GuestList.php')) ?>">Generate Guest List</a>
                        <button class="btn btn-default" type="submit" name="send-emails" value="1">Send emails</button>
                        <button class="btn btn-primary" type="submit" name="save-confirmations" value="1">Save changes</button>
                    </form>
                </div>

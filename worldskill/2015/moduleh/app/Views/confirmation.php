<?php
/**
 * 送出確認頁：內容全部由資料庫讀回（而非直接回顯表單輸入）。
 *
 * @var array $booking 訂位申請（含聯絡人）
 * @var array $groups  以「競賽日 + 場次」分組的賓客
 */

use App\Core\Url;
use App\Core\View;
?>
                <div id="submission_confirmation">
                    <h1 class="page-header">Submission confirmation</h1>
                    <p>
                        <?= View::e($booking['contact_name']) ?>,<br/><br/>
                        Thank you for your booking request <?= View::e($booking['booking_no']) ?>.<br/><br/>
                        You have requested booking for the following guests:
                    </p>
                    <ul>
                        <?php foreach ($groups as $label => $reservations): ?>
                            <li>
                                <?= View::e($label) ?><br/>for
                                <?php
                                    $names = [];

                                    foreach ($reservations as $reservation) {
                                        $name = $reservation['guest_name'] !== null && $reservation['guest_name'] !== ''
                                            ? $reservation['guest_name']
                                            : 'guest';

                                        $entry = $name . ' ' . $reservation['guest_country'];

                                        if ($reservation['status'] === 'waitlisted') {
                                            $entry .= ' (waitlisted)';
                                        }

                                        $names[] = $entry;
                                    }
                                ?>
                                <?= View::e(implode(', ', $names)) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php
                        // 是否有被排入候補的賓客
                        $hasWaitlisted = false;

                        foreach ($groups as $reservations) {
                            foreach ($reservations as $reservation) {
                                if ($reservation['status'] === 'waitlisted') {
                                    $hasWaitlisted = true;
                                    break 2;
                                }
                            }
                        }
                    ?>
                    <?php if ($hasWaitlisted): ?>
                        <div class="alert alert-warning">
                            Some of your guests are on the <strong>waiting list</strong> because the seating was already full.
                            You will be called if a cancellation occurs.
                        </div>
                    <?php endif; ?>
                    <p>
                        Please note that these booking requests will need to be reviewed and confirmed by WSI. <br/>
                        You will receive an email with the confirmation as soon as possible.
                    </p>
                    <p>
                        <a class="btn btn-default" href="<?= View::e(Url::to()) ?>">Back to the homepage</a>
                    </p>
                </div>

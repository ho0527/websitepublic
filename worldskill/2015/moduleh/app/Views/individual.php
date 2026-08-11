<?php
/**
 * 個人訂位：以「場次 x 競賽日」矩陣顯示剩餘座位與核取方塊
 *
 * @var array $contact      session 中的聯絡人資料
 * @var array $days         競賽日
 * @var array $seatings     場次
 * @var array $availability [日 id][場次 id] => 剩餘座位數
 * @var array $errors       錯誤訊息
 */

use App\Core\Url;
use App\Core\View;
use App\Models\CompetitionDay;
use App\Services\BookingService;
?>
                <div id="booking_request">
                    <h1 class="page-header">Booking request</h1>

                    <?php if ($errors !== []): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $message): ?>
                                <div><?= View::e($message) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= View::e(Url::to('booking/individual')) ?>" method="post">
                        <fieldset>
                            <legend>Individual</legend>
                            <p>Booking an individual guest:
                                <strong><?= View::e($contact['name']) ?></strong>
                                (<?= View::e($contact['country']) ?>)</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Dining experience</th>
                                            <?php foreach ($days as $day): ?>
                                                <th><?= View::e(CompetitionDay::label($day)) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($seatings as $seatingIndex => $seating): ?>
                                            <tr>
                                                <td>
                                                    <?= View::e($seating['module_name']) ?><br/>
                                                    <?= View::e($seating['time_label']) ?>
                                                </td>
                                                <?php foreach ($days as $dayIndex => $day): ?>
                                                    <?php
                                                        $field = BookingService::fieldPrefix($dayIndex + 1, $seatingIndex + 1) . '-n1';
                                                        $free  = $availability[(int) $day['id']][(int) $seating['id']] ?? 0;
                                                    ?>
                                                    <td>
                                                        available: <?= (int) $free ?>
                                                        <input type="checkbox" name="<?= View::e($field) ?>" value="1">
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p>Please note that most seating take place at the same time and you are not allowed to change once seated.<br />For a seating that is full, you will be waitlisted.</p>
                        </fieldset>
                        <button class="btn btn-primary" type="submit" name="book-individual">Submit booking request</button>
                    </form>
                </div>

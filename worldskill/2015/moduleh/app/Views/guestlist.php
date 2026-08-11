<?php
/**
 * 給餐廳外場主管（host/hostess）的賓客名單。
 * 只列出 confirmed，依「競賽日 -> 場次」分組，組內依訂位編號排序。
 *
 * @var array  $groups 分組後的賓客
 * @var string $csvUrl CSV 下載網址
 */

use App\Core\Countries;
use App\Core\Url;
use App\Core\View;
?>
                <div id="guest_list">
                    <h1 class="page-header">Guest list for the Restaurant Service host</h1>

                    <p>
                        <a class="btn btn-primary" href="<?= View::e($csvUrl) ?>">Download as CSV</a>
                        <a class="btn btn-default" href="<?= View::e(Url::management()) ?>">Back to reservation management</a>
                    </p>

                    <?php if ($groups === []): ?>
                        <div class="alert alert-info">There are no confirmed reservations yet.</div>
                    <?php endif; ?>

                    <?php foreach ($groups as $label => $rows): ?>
                        <h3><?= View::e($label) ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Booking No.</th>
                                        <th>Booking Contact Name</th>
                                        <th>Booking Contact Organization</th>
                                        <th>Guest Name</th>
                                        <th>Guest Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?= View::e($row['booking_no']) ?></td>
                                            <td><?= View::e($row['contact_name']) ?></td>
                                            <td><?= View::e($row['organization'] ?? '') ?></td>
                                            <td><?= View::e(($row['guest_name'] ?? '') !== '' ? $row['guest_name'] : '(name not given)') ?></td>
                                            <td><?= View::e($row['guest_country'] . ' - ' . Countries::name($row['guest_country'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

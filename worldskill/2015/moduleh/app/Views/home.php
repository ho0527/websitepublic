<?php
/**
 * 首頁：用餐體驗說明表格（內容全部來自資料庫）
 *
 * @var array $modules 餐飲模組（含所屬場次）
 */

use App\Core\Url;
use App\Core\View;
?>
                <div id="dining_experience_descriptions">
                    <div class="row">
                        <div class="col-xs-6">
                            <h1>Guests in Restaurant Service</h1>
                            <p class="clearfix">Become part of the competition: be a guest in Restaurant Service competition by requesting a seat and enjoy one of the different dining experiences!</p>
                        </div>
                        <div class="col-xs-offset-2 col-xs-4 col-sm-offset-4 col-sm-2">
                            <h1><img src="<?= View::e(Url::asset('6215177259.jpg')) ?>" alt="cook in restaurant service" class="img-thumbnail img-responsive"></h1>
                        </div>
                    </div>

                    <h3>Dining experience descriptions</h3>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <colgroup>
                                <?php foreach ($modules as $module): ?>
                                    <col style="width: <?= (int) (100 / max(1, count($modules))) ?>%">
                                <?php endforeach; ?>
                            </colgroup>
                            <thead>
                                <tr>
                                    <?php foreach ($modules as $module): ?>
                                        <th><?= View::e($module['name']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php foreach ($modules as $module): ?>
                                        <td><?= View::e($module['description']) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($modules as $module): ?>
                                        <td>
                                            <?php // 同一模組各場次的桌型設定相同時只顯示一次 ?>
                                            <?php foreach (array_unique(array_column($module['seatings'], 'configuration')) as $configuration): ?>
                                                <?= View::e($configuration) ?><br>
                                            <?php endforeach; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <?php foreach ($modules as $module): ?>
                                        <td>
                                            <?php foreach ($module['seatings'] as $seating): ?>
                                                <?= View::e($seating['name']) ?>:
                                                <?= View::e(substr($seating['start_time'], 0, 5)) ?> &ndash;
                                                <?= View::e(substr($seating['end_time'], 0, 5)) ?><br>
                                            <?php endforeach; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <form action="<?= View::e(Url::to('booking/contact')) ?>" method="get">
                        <button class="btn btn-primary" type="submit" name="start-booking">Start booking</button>
                    </form>
                </div>

<?php
/**
 * 訂位聯絡人資料 + 賓客規範
 *
 * @var array $data   目前表單值
 * @var array $errors 欄位 => 錯誤訊息
 */

use App\Core\Countries;
use App\Core\Url;
use App\Core\View;

$value = static fn (string $key): string => View::e($data[$key] ?? '');
?>
                <div id="booking_contact_guest_regulations">
                    <h1 class="page-header">Booking contact details and guest regulations</h1>

                    <?php if ($errors !== []): ?>
                        <div class="alert alert-danger">
                            <strong>Please correct the following:</strong>
                            <ul>
                                <?php foreach ($errors as $message): ?>
                                    <li><?= View::e($message) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= View::e(Url::to('booking/contact')) ?>" method="post" class="form-horizontal">
                        <fieldset>
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title">Booking Contact</h3></div>
                                <div class="panel-body">
                                    <div class="form-group<?= isset($errors['name']) ? ' has-error' : '' ?>">
                                        <label for="name" class="col-sm-3 control-label">Name *</label>
                                        <div class="col-sm-4">
                                            <input type="text" id="name" name="name" required="required" class="form-control" value="<?= $value('name') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="organization" class="col-sm-3 control-label">Organization &deg;</label>
                                        <div class="col-sm-4">
                                            <input type="text" id="organization" name="organization" class="form-control" value="<?= $value('organization') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group<?= isset($errors['email']) ? ' has-error' : '' ?>">
                                        <label for="email" class="col-sm-3 control-label">Email *</label>
                                        <div class="col-sm-4">
                                            <input type="text" id="email" name="email" required="required" class="form-control" value="<?= $value('email') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone" class="col-sm-3 control-label">Phone</label>
                                        <div class="col-sm-4">
                                            <input type="text" id="phone" name="phone" class="form-control" value="<?= $value('phone') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group<?= isset($errors['country']) ? ' has-error' : '' ?>">
                                        <label for="country" class="col-sm-3 control-label">Country *</label>
                                        <div class="col-sm-4">
                                            <select id="country" name="country" required="required" class="form-control">
                                                <?= Countries::options($data['country'] ?? '', 'select') ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-offset-3 col-sm-9">
                                            <p>
                                            *) these fields must be filled<br />
                                            &deg;) if applicable. We might give priority to a sponsor for example, if we get multiple requests.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="panel panel-default">
                                <div class="panel-heading"><h3 class="panel-title">Guest regulations</h3></div>
                                <div class="panel-body">
                                    <p>
                                        Welcome to the Restaurant Service booking request system. All bookings will be submitted to WorldSkills International for final confirmation. <br/><br/>
                                        Before proceeding with your booking please read and accept the guest regulations:
                                    </p>
                                    <ul>
                                        <li>Guests must be at the Restaurant Service area <em>15 minutes prior to scheduled seating time</em>.</li>
                                        <li>If guests are late (<em>maximum 5 minutes from allocated time</em>) their table will not be guaranteed (so that Competitors are not disadvantaged, the tables will be given to standby guests).</li>
                                        <li>Once seated &ndash; guests must accept all food and beverage that is offered, as Competitors must be marked on all skill areas.</li>
                                        <li>Dietary requests cannot be accepted, as menu items must be the same for all Competitors.</li>
                                        <li>No mobile phones, videos or cameras are permitted to be used.</li>
                                        <li>Guests cannot leave the area until the meal service is completed unless approved by Experts in the area (again this is so that no Competitor is disadvantaged with service).</li>
                                        <li>Guests will <em>not sit</em> at the tables where the Competitor is from the same country as the guests.</li>
                                        <li>Guest are invited as guests of WorldSkills, they are not to judge the Competitor or interfere with the Competitor in their work or cause disruption to their work or make comments to judges about any of the Competitors.</li>
                                        <li>Guest must be legal drinking age according to the Host Country regulations (i.e. 18 in Brazil).</li>
                                    </ul>
                                    <div class="checkbox<?= isset($errors['agree']) ? ' has-error' : '' ?>">
                                        <label>
                                            <input type="checkbox" id="agree" name="agree" value="1" required="required"<?= !empty($data['agree']) ? ' checked' : '' ?>>
                                            I agree to the guest regulations and confirm that myself and any guests (group booking) will respect all of the guest regulations
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <button class="btn btn-primary" name="agree-individual" value="1" type="submit">Continue booking for<strong> an individual</strong></button>
                        <button class="btn btn-primary" name="agree-group" value="1" type="submit">Continue booking for<strong> a group</strong></button>
                    </form>
                </div>

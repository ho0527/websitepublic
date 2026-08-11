<?php
/**
 * A2a - Manage events：列出目前登入主辦者的所有活動（依日期由小到大），並顯示報名總數
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();

// 只查詢自己的活動（多租戶隔離），並一次統計每個活動的報名數
$events = db_all(
    'SELECT e.*, COUNT(reg.`id`) AS registrations
       FROM `events` e
       LEFT JOIN `event_tickets` t ON t.`event_id` = e.`id`
       LEFT JOIN `registrations` reg ON reg.`ticket_id` = t.`id`
      WHERE e.`organizer_id` = ?
      GROUP BY e.`id`
      ORDER BY e.`date` ASC, e.`id` ASC',
    [$organizer['id']]
);

render_header($organizer, null, 'events');
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Events</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group mr-2">
            <a href="events/create.php" class="btn btn-sm btn-outline-secondary">Create new event</a>
        </div>
    </div>
</div>

<div class="row events">
    <?php foreach ($events as $event): ?>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <a href="events/detail.php?id=<?= (int) $event['id'] ?>" class="btn text-left event">
                    <div class="card-body">
                        <h5 class="card-title"><?= e($event['name']) ?></h5>
                        <p class="card-subtitle"><?= e(format_event_date($event['date'])) ?></p>
                        <hr>
                        <p class="card-text"><?= number_format((int) $event['registrations']) ?> registrations</p>
                    </div>
                </a>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!$events): ?>
        <div class="col-12"><p class="text-muted">No events yet.</p></div>
    <?php endif; ?>
</div>
<?php
render_footer();

<?php
/**
 * A2h - 活動詳細頁
 *   顯示活動基本資訊，以及票券、議程、頻道、房間等所有關聯資料
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['id'] ?? 0), $organizer);
$eventId   = (int) $event['id'];

$tickets  = event_tickets($eventId);
$sessions = event_sessions($eventId);
$channels = event_channels($eventId);
$rooms    = event_rooms($eventId);

// 每個頻道的議程數與房間數
$channelStats = [];
foreach ($channels as $channel) {
    $channelStats[(int) $channel['id']] = ['rooms' => 0, 'sessions' => 0];
}
foreach ($rooms as $room) {
    $channelStats[(int) $room['channel_id']]['rooms']++;
}
foreach ($sessions as $session) {
    $channelStats[(int) $session['channel_id']]['sessions']++;
}

$editButton = '<div class="btn-toolbar mb-2 mb-md-0"><div class="btn-group mr-2">'
    . '<a href="events/edit.php?id=' . $eventId . '" class="btn btn-sm btn-outline-secondary">Edit event</a>'
    . '</div></div>';

render_header($organizer, $event, 'overview');
render_event_heading($event, $editButton);
?>

<!-- Tickets -->
<div id="tickets" class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Tickets</h2>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a href="tickets/create.php?event_id=<?= $eventId ?>" class="btn btn-sm btn-outline-secondary">
                    Create new ticket
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row tickets">
    <?php foreach ($tickets as $ticket): ?>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= e($ticket['name']) ?></h5>
                    <p class="card-text"><?= e(number_format((float) $ticket['cost'], 2)) ?>.-</p>
                    <p class="card-text"><?= e(ticket_description($ticket['special_validity']) ?? '') ?>&nbsp;</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$tickets): ?>
        <div class="col-12"><p class="text-muted">No tickets yet.</p></div>
    <?php endif; ?>
</div>

<!-- Sessions -->
<div id="sessions" class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Sessions</h2>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a href="sessions/create.php?event_id=<?= $eventId ?>" class="btn btn-sm btn-outline-secondary">
                    Create new session
                </a>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive sessions">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Time</th>
            <th>Type</th>
            <th class="w-100">Title</th>
            <th>Speaker</th>
            <th>Channel</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($sessions as $session): ?>
            <tr>
                <td class="text-nowrap">
                    <?= e(date('H:i', strtotime((string) $session['start']))) ?>
                    -
                    <?= e(date('H:i', strtotime((string) $session['end']))) ?>
                </td>
                <td><?= e(ucfirst((string) $session['type'])) ?></td>
                <td><a href="sessions/edit.php?id=<?= (int) $session['id'] ?>"><?= e($session['title']) ?></a></td>
                <td class="text-nowrap"><?= e($session['speaker']) ?></td>
                <td class="text-nowrap"><?= e($session['channel_name']) ?> / <?= e($session['room_name']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$sessions): ?>
            <tr><td colspan="5" class="text-muted">No sessions yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Channels -->
<div id="channels" class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Channels</h2>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a href="channels/create.php?event_id=<?= $eventId ?>" class="btn btn-sm btn-outline-secondary">
                    Create new channel
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row channels">
    <?php foreach ($channels as $channel): ?>
        <?php $stats = $channelStats[(int) $channel['id']]; ?>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= e($channel['name']) ?></h5>
                    <p class="card-text">
                        <?= (int) $stats['sessions'] ?> session<?= $stats['sessions'] === 1 ? '' : 's' ?>,
                        <?= (int) $stats['rooms'] ?> room<?= $stats['rooms'] === 1 ? '' : 's' ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if (!$channels): ?>
        <div class="col-12"><p class="text-muted">No channels yet.</p></div>
    <?php endif; ?>
</div>

<!-- Rooms -->
<div id="rooms" class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Rooms</h2>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a href="rooms/create.php?event_id=<?= $eventId ?>" class="btn btn-sm btn-outline-secondary">
                    Create new room
                </a>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive rooms">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Channel</th>
            <th>Capacity</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?= e($room['name']) ?></td>
                <td><?= e($room['channel_name']) ?></td>
                <td><?= number_format((int) $room['capacity']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rooms): ?>
            <tr><td colspan="3" class="text-muted">No rooms yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
render_footer();

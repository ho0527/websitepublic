<?php
/**
 * A7a / A7b - 房間使用率報表
 *   長條圖：X 軸為議程，Y 軸同時顯示房間容量與已報名人數
 *   人數超過房間容量時，該筆的人數長條會由綠色轉為紅色
 *
 *   人數計算：
 *     - talk     ：包含在活動門票內，等於該活動的報名總數
 *     - workshop ：需另外報名，只計算 session_registrations
 */

declare(strict_types=1);

require_once __DIR__ . '/../inc/layout.php';
require_once __DIR__ . '/../inc/model.php';

$organizer = require_organizer();
$event     = require_own_event((int) ($_GET['event_id'] ?? 0), $organizer);
$eventId   = (int) $event['id'];

$sessions          = event_sessions($eventId);
$eventRegistrations = event_registration_count($eventId);

$labels     = [];
$capacities = [];
$attendees  = [];
$colors     = [];

foreach ($sessions as $session) {
    $count    = session_attendee_count($session, $eventRegistrations);
    $capacity = (int) $session['room_capacity'];

    $labels[]     = $session['title'] . ' (' . $session['room_name'] . ')';
    $capacities[] = $capacity;
    $attendees[]  = $count;
    // 超過容量顯示紅色，否則綠色
    $colors[]     = $count > $capacity ? 'rgba(220, 53, 69, 0.85)' : 'rgba(40, 167, 69, 0.85)';
}

$chartData = [
    'labels'     => $labels,
    'capacities' => $capacities,
    'attendees'  => $attendees,
    'colors'     => $colors,
];

render_header($organizer, $event, 'reports');
?>
<div class="border-bottom mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h1 class="h2"><?= e($event['name']) ?></h1>
    </div>
    <span class="h6"><?= e(format_event_date($event['date'])) ?></span>
</div>

<div class="mb-3 pt-3 pb-2">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
        <h2 class="h4">Room Capacity</h2>
    </div>
</div>

<?php if (!$sessions): ?>
    <p class="text-muted">This event has no sessions yet.</p>
<?php else: ?>
    <div class="mb-5" style="height: 520px;">
        <canvas id="roomCapacityChart"></canvas>
    </div>

    <table class="table table-sm table-striped">
        <thead>
        <tr>
            <th>Session</th>
            <th>Room</th>
            <th class="text-right">Capacity</th>
            <th class="text-right">Attendees</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($sessions as $index => $session): ?>
            <tr class="<?= $attendees[$index] > $capacities[$index] ? 'table-danger' : '' ?>">
                <td><?= e($session['title']) ?></td>
                <td class="text-nowrap"><?= e($session['room_name']) ?></td>
                <td class="text-right"><?= number_format($capacities[$index]) ?></td>
                <td class="text-right"><?= number_format($attendees[$index]) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php
$scripts = '';
if ($sessions) {
    $json = json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    $scripts = <<<HTML
<script src="assets/js/Chart.bundle.min.js"></script>
<script>
    // 由 PHP 傳入的圖表資料
    var chartData = {$json};

    new Chart(document.getElementById('roomCapacityChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Room capacity',
                    data: chartData.capacities,
                    backgroundColor: 'rgba(108, 117, 125, 0.35)',
                    borderColor: 'rgba(108, 117, 125, 0.8)',
                    borderWidth: 1
                },
                {
                    label: 'Registered attendees',
                    data: chartData.attendees,
                    backgroundColor: chartData.colors,
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            title: { display: true, text: 'Room capacity vs. registered attendees' },
            scales: {
                xAxes: [{
                    stacked: false,
                    ticks: { autoSkip: false, maxRotation: 60, minRotation: 30 }
                }],
                yAxes: [{
                    ticks: { beginAtZero: true },
                    scaleLabel: { display: true, labelString: 'Persons' }
                }]
            }
        }
    });
</script>
HTML;
}
render_footer($scripts);

<?php
/**
 * 後台頁面共用版型（沿用主辦單位提供的 admin GUI 模板，class / id 完全保留）
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

/**
 * 輸出頁首（含固定導覽列與左側選單）
 *
 * @param array<string, mixed>      $organizer 目前登入的主辦者
 * @param array<string, mixed>|null $event     目前所在的活動（有的話左側選單會顯示活動選單）
 * @param string                    $active    目前選中的選單：events / overview / reports
 */
function render_header(array $organizer, ?array $event = null, string $active = 'events'): void
{
    $flash = take_flash();
    ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Event Backend</title>

    <base href="<?= e(base_path()) ?>">
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="assets/css/custom.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="events/index.php">Event Platform</a>
    <span class="navbar-organizer w-100"><?= e($organizer['name']) ?></span>
    <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
            <a class="nav-link" id="logout" href="logout.php">Sign out</a>
        </li>
    </ul>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link<?= $active === 'events' ? ' active' : '' ?>" href="events/index.php">Manage Events</a>
                    </li>
                </ul>

                <?php if ($event !== null): ?>
                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span><?= e($event['name']) ?></span>
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link<?= $active === 'overview' ? ' active' : '' ?>"
                               href="events/detail.php?id=<?= (int) $event['id'] ?>">Overview</a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Reports</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link<?= $active === 'reports' ? ' active' : '' ?>"
                               href="reports/index.php?event_id=<?= (int) $event['id'] ?>">Room capacity</a>
                        </li>
                    </ul>
                <?php endif; ?>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <?php if ($flash !== null): ?>
                <div class="alert alert-<?= e($flash['type']) ?> mt-3 mb-0" role="alert">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>
    <?php
}

/**
 * 輸出頁尾
 */
function render_footer(string $extraScripts = ''): void
{
    ?>
        </main>
    </div>
</div>
<?= $extraScripts ?>
</body>
</html>
    <?php
}

/**
 * 活動頁共用的標題區塊（活動名稱 + 日期）
 *
 * @param array<string, mixed> $event
 */
function render_event_heading(array $event, string $editButton = ''): void
{
    ?>
    <div class="border-bottom mb-3 pt-3 pb-2 event-title">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
            <h1 class="h2"><?= e($event['name']) ?></h1>
            <?= $editButton ?>
        </div>
        <span class="h6"><?= e(format_event_date($event['date'])) ?></span>
    </div>
    <?php
}

/**
 * 將日期格式化為 "September 23, 2019"
 */
function format_event_date(?string $date): string
{
    if (!$date) {
        return '';
    }
    $dt = date_create($date);
    return $dt ? $dt->format('F j, Y') : $date;
}

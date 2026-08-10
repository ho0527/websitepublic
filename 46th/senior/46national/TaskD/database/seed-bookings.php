<?php

declare(strict_types=1);

/**
 * 產生示範用的歷史訂票紀錄。
 *
 * 「資料統計及開放資料」只統計昨天（含）以前的訂票，
 * 因此需要一批過去日期的資料才能看到圖表與 JSON 的內容。
 *
 * 用法：php database/seed-bookings.php [天數]
 */

use App\Core\Autoloader;
use App\Core\Config;
use App\Core\ServiceContainer;
use App\Models\Booking;
use App\Models\Train;

$root = dirname(__DIR__);

require $root . '/app/Core/Autoloader.php';

(new Autoloader('App', $root . '/app'))->register();
Config::load($root . '/config/config.php');
date_default_timezone_set((string) Config::get('timezone', 'Asia/Taipei'));

/** 要往前產生幾天的資料 */
$daysToSeed = isset($argv[1]) ? max(1, (int) $argv[1]) : 7;

$schedule = ServiceContainer::schedule();
$trains   = Train::active()->get();
$phones   = ['0911111111', '0922222222', '0933333333', '0944444444', '0955555555'];
$created  = 0;

// 先清掉先前產生的示範資料，避免重複執行後數字不斷累加
Booking::query()->whereLike('booking_code', 'DEMO')->delete();

foreach (range(1, $daysToSeed) as $daysAgo) {
    $travelDate = (new DateTimeImmutable('today'))->modify(sprintf('-%d days', $daysAgo));

    foreach ($trains as $train) {
        if (!$train->runsOn($travelDate)) {
            continue;
        }

        $stops = $train->stops();

        // 每個車次隨機產生幾筆不同區間的訂票
        foreach (range(1, random_int(2, 4)) as $ignored) {
            $fromIndex = random_int(0, count($stops) - 2);
            $toIndex   = random_int($fromIndex + 1, count($stops) - 1);

            $segment = $schedule->segmentOf(
                $train,
                (int) $stops[$fromIndex]->station_id,
                (int) $stops[$toIndex]->station_id,
                $travelDate
            );

            if ($segment === null) {
                continue;
            }

            $ticketCount = random_int(1, 12);
            // 訂票編號固定以 DEMO 開頭，方便辨識與清除
            $bookingCode = 'DEMO' . strtoupper(bin2hex(random_bytes(4)));

            Booking::create([
                'booking_code'    => $bookingCode,
                'phone'           => $phones[array_rand($phones)],
                'train_id'        => $train->id(),
                'from_station_id' => (int) $stops[$fromIndex]->station_id,
                'to_station_id'   => (int) $stops[$toIndex]->station_id,
                'travel_date'     => $travelDate->format('Y-m-d'),
                'depart_at'       => $segment['depart']->format('Y-m-d H:i:s'),
                'arrive_at'       => $segment['arrive']->format('Y-m-d H:i:s'),
                'ticket_count'    => $ticketCount,
                'unit_price'      => $segment['fare'],
                'total_price'     => $segment['fare'] * $ticketCount,
                'status'          => Booking::STATUS_BOOKED,
                'created_at'      => $travelDate->modify('-1 day')->format('Y-m-d H:i:s'),
            ]);

            $created++;
        }
    }
}

printf("已產生 %d 筆示範訂票紀錄（涵蓋過去 %d 天）\n", $created, $daysToSeed);

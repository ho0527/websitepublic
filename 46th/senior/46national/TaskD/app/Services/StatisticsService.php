<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Models\Booking;
use App\Models\Station;
use App\Models\Train;
use App\Models\TrainType;

/**
 * 搭乘人數統計與開放資料輸出。
 *
 * 只統計「昨天（含）以前」且未被取消的訂票紀錄：
 *  - 進站人數：以起程站為該站的訂票紀錄，累計其車票張數，時間取該站的開車時間
 *  - 離站人數：以到達站為該站的訂票紀錄，累計其車票張數，時間取該站的抵達時間
 * 時間以半小時（30 分鐘）為單位分組，例如 06:00 統計 06:00~06:29 的人數。
 */
final class StatisticsService
{
    private ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * 產生開放資料。
     *
     * @param int|null $trainTypeId 只統計指定車種，null 代表全部車種
     * @param string|null $fromDate 統計起始日期（含），null 代表不限
     * @param string|null $toDate   統計結束日期（含），null 代表統計到昨天
     *
     * @return array{
     *     stations: array<int, array{id: int, code: string, title: string}>,
     *     records: array<int, array{station_id: int, record: array<int, array{type_id: int, exit: int, entrance: int}>, time: int}>
     * }
     */
    public function build(?int $trainTypeId, ?string $fromDate, ?string $toDate, \DateTimeInterface $now): array
    {
        $intervalMinutes = (int) Config::get('statistics_interval_minutes', 30);
        $stations        = Station::allOrdered();
        $trainTypes      = TrainType::allOrdered();

        // 統計範圍的最後一天預設是昨天
        $yesterday = (new \DateTimeImmutable($now->format('Y-m-d')))->modify('-1 day');
        $rangeEnd  = $toDate !== null && $toDate !== '' ? new \DateTimeImmutable($toDate) : $yesterday;

        if ($rangeEnd > $yesterday) {
            $rangeEnd = $yesterday;
        }

        $query = Booking::active()->where('travel_date', '<=', $rangeEnd->format('Y-m-d'));

        if ($fromDate !== null && $fromDate !== '') {
            $query->where('travel_date', '>=', $fromDate);
        }

        // 車種篩選需要先取出該車種的列車
        if ($trainTypeId !== null) {
            $trainIds = array_map(
                static fn (Train $train): int => $train->id(),
                Train::where('train_type_id', $trainTypeId)->get()
            );

            $query->whereIn('train_id', $trainIds);
        }

        // 以「車站 + 時間桶」為鍵累計各車種的進出站人數
        $buckets   = [];
        $typeOf    = [];

        foreach (Train::all() as $train) {
            $typeOf[$train->id()] = (int) $train->train_type_id;
        }

        foreach ($query->get() as $booking) {
            $typeId = $typeOf[(int) $booking->train_id] ?? null;

            if ($typeId === null) {
                continue;
            }

            $count = (int) $booking->ticket_count;

            // 進站：起程站，時間取開車時間
            $this->accumulate(
                $buckets,
                (int) $booking->from_station_id,
                $this->bucketTimestamp((string) $booking->depart_at, $intervalMinutes),
                $typeId,
                'entrance',
                $count
            );

            // 離站：到達站，時間取抵達時間
            $this->accumulate(
                $buckets,
                (int) $booking->to_station_id,
                $this->bucketTimestamp((string) $booking->arrive_at, $intervalMinutes),
                $typeId,
                'exit',
                $count
            );
        }

        return [
            'stations' => array_map(
                static fn (Station $station): array => [
                    'id'    => $station->id(),
                    'code'  => (string) $station->code,
                    'title' => (string) $station->name,
                ],
                array_values($stations)
            ),
            'records'  => $this->formatRecords($buckets, $trainTypes),
        ];
    }

    /**
     * 累計單一筆進站或離站人數。
     *
     * @param array<string, array{station_id: int, time: int, types: array<int, array{entrance: int, exit: int}>}> $buckets
     */
    private function accumulate(
        array &$buckets,
        int $stationId,
        int $timestamp,
        int $typeId,
        string $direction,
        int $count
    ): void {
        $key = $stationId . '@' . $timestamp;

        if (!isset($buckets[$key])) {
            $buckets[$key] = ['station_id' => $stationId, 'time' => $timestamp, 'types' => []];
        }

        if (!isset($buckets[$key]['types'][$typeId])) {
            $buckets[$key]['types'][$typeId] = ['entrance' => 0, 'exit' => 0];
        }

        $buckets[$key]['types'][$typeId][$direction] += $count;
    }

    /**
     * 把時間往下對齊到半小時的整點，並回傳 UNIX 時間戳。
     */
    private function bucketTimestamp(string $dateTime, int $intervalMinutes): int
    {
        $moment    = new \DateTimeImmutable($dateTime);
        $minute    = (int) $moment->format('i');
        $aligned   = intdiv($minute, $intervalMinutes) * $intervalMinutes;

        return $moment->setTime((int) $moment->format('H'), $aligned, 0)->getTimestamp();
    }

    /**
     * 把累計結果整理成開放資料的輸出格式。
     *
     * @param array<string, array{station_id: int, time: int, types: array<int, array{entrance: int, exit: int}>}> $buckets
     * @param array<int, TrainType> $trainTypes
     *
     * @return array<int, array{station_id: int, record: array<int, array{type_id: int, exit: int, entrance: int}>, time: int}>
     */
    private function formatRecords(array $buckets, array $trainTypes): array
    {
        $records = [];

        foreach ($buckets as $bucket) {
            $record = [];

            foreach ($trainTypes as $type) {
                $counts   = $bucket['types'][$type->id()] ?? ['entrance' => 0, 'exit' => 0];
                $record[] = [
                    'type_id'  => $type->id(),
                    'exit'     => $counts['exit'],
                    'entrance' => $counts['entrance'],
                ];
            }

            $records[] = [
                'station_id' => $bucket['station_id'],
                'record'     => $record,
                'time'       => $bucket['time'],
            ];
        }

        // 依車站、再依時間排序，讓輸出穩定可預期
        usort($records, static function (array $left, array $right): int {
            return [$left['station_id'], $left['time']] <=> [$right['station_id'], $right['time']];
        });

        return $records;
    }
}

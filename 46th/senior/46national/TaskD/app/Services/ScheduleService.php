<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Train;
use App\Models\TrainStop;

/**
 * 時刻與票價計算。
 *
 * 資料庫只儲存「發車站的發車時間」「各站的行駛分鐘數與停留分鐘數」，
 * 各站實際的抵達／開車時間一律由本服務推算，確保時刻表只有單一資料來源。
 */
final class ScheduleService
{
    /**
     * 推算列車在各停靠站的抵達與開車時間（自發車起算的分鐘數）。
     *
     * 發車站沒有抵達時間、終點站沒有開車時間，分別以 null 表示。
     *
     * @return array<int, array{stop: TrainStop, arrive_offset: int|null, depart_offset: int|null}>
     */
    public function timetableOf(Train $train): array
    {
        $stops     = $train->stops();
        $lastIndex = count($stops) - 1;
        $timetable = [];
        $elapsed   = 0;

        foreach ($stops as $index => $stop) {
            $isOrigin   = $index === 0;
            $isTerminus = $index === $lastIndex;

            // 從前一站行駛到本站
            $elapsed += (int) $stop->travel_minutes;
            $arriveOffset = $isOrigin ? null : $elapsed;

            // 在本站停留後才開車
            if (!$isOrigin) {
                $elapsed += (int) $stop->stop_minutes;
            }

            $timetable[] = [
                'stop'          => $stop,
                'arrive_offset' => $arriveOffset,
                'depart_offset' => $isTerminus ? null : $elapsed,
            ];
        }

        return $timetable;
    }

    /**
     * 取得列車在指定車站的抵達／開車時刻（以指定乘車日為基準的完整日期時間）。
     *
     * @return array{arrive: \DateTimeImmutable|null, depart: \DateTimeImmutable|null}|null
     */
    public function momentsAtStation(Train $train, int $stationId, \DateTimeInterface $travelDate): ?array
    {
        $departure = $this->departureMoment($train, $travelDate);

        foreach ($this->timetableOf($train) as $entry) {
            if ((int) $entry['stop']->station_id !== $stationId) {
                continue;
            }

            return [
                'arrive' => $entry['arrive_offset'] === null
                    ? null
                    : $departure->modify(sprintf('+%d minutes', $entry['arrive_offset'])),
                'depart' => $entry['depart_offset'] === null
                    ? null
                    : $departure->modify(sprintf('+%d minutes', $entry['depart_offset'])),
            ];
        }

        return null;
    }

    /**
     * 取得指定乘車日的發車時刻。
     */
    public function departureMoment(Train $train, \DateTimeInterface $travelDate): \DateTimeImmutable
    {
        return new \DateTimeImmutable(
            $travelDate->format('Y-m-d') . ' ' . substr((string) $train->depart_time, 0, 8)
        );
    }

    /**
     * 計算某一段旅程的時刻與票價。
     *
     * @return array{
     *     depart: \DateTimeImmutable,
     *     arrive: \DateTimeImmutable,
     *     duration_minutes: int,
     *     fare: int
     * }|null 當列車未依序行經這兩站時回傳 null
     */
    public function segmentOf(
        Train $train,
        int $fromStationId,
        int $toStationId,
        \DateTimeInterface $travelDate
    ): ?array {
        $fromStop = $train->stopAtStation($fromStationId);
        $toStop   = $train->stopAtStation($toStationId);

        // 兩站都必須在行經路線上，且必須是「先起程站、後到達站」的方向
        if ($fromStop === null || $toStop === null) {
            return null;
        }

        if ((int) $fromStop->stop_sequence >= (int) $toStop->stop_sequence) {
            return null;
        }

        $fromMoments = $this->momentsAtStation($train, $fromStationId, $travelDate);
        $toMoments   = $this->momentsAtStation($train, $toStationId, $travelDate);

        // 起程站看的是開車時間、到達站看的是抵達時間
        $depart = $fromMoments['depart'] ?? null;
        $arrive = $toMoments['arrive'] ?? null;

        if ($depart === null || $arrive === null) {
            return null;
        }

        return [
            'depart'           => $depart,
            'arrive'           => $arrive,
            'duration_minutes' => (int) round(($arrive->getTimestamp() - $depart->getTimestamp()) / 60),
            'fare'             => max(0, (int) $toStop->fare_from_origin - (int) $fromStop->fare_from_origin),
        ];
    }

    /**
     * 把分鐘數格式化為「X 小時 Y 分」。
     */
    public function formatDuration(int $minutes): string
    {
        $hours     = intdiv($minutes, 60);
        $remainder = $minutes % 60;

        if ($hours === 0) {
            return sprintf('%d 分', $remainder);
        }

        return $remainder === 0
            ? sprintf('%d 小時', $hours)
            : sprintf('%d 小時 %d 分', $hours, $remainder);
    }
}

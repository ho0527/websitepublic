<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Booking;
use App\Models\Train;

/**
 * 區間座位計算。
 *
 * 一張車票只佔用「起程站到到達站之間」的座位，下車後座位即可再賣給別人。
 * 因此把路線拆成一段一段的相鄰區間，分別累計已售出的張數，
 * 要訂的區間裡「最壅塞的那一段」才是真正的限制。
 *
 * 例：列車經過 A-B-C-D 且可載 100 人，已有 90 人買 A→C 的票時，
 * A-B 與 B-C 兩段各已佔用 90 席，B→C 只剩 10 席，
 * 但 C-D 段完全未被佔用，所以 C→D 仍可訂滿 100 張。
 */
final class SeatService
{
    /**
     * 計算指定區間的剩餘座位數。
     *
     * @param Train              $train         列車
     * @param \DateTimeInterface $travelDate    乘車日期
     * @param int                $fromSequence  起程站的停靠順序
     * @param int                $toSequence    到達站的停靠順序
     * @param int|null           $ignoreBooking 計算時要忽略的訂票編號（例如改票情境）
     */
    public function availableSeats(
        Train $train,
        \DateTimeInterface $travelDate,
        int $fromSequence,
        int $toSequence,
        ?int $ignoreBooking = null
    ): int {
        $capacity = (int) ($train->type()?->capacity ?? 0);
        $occupied = $this->occupancyBySegment($train, $travelDate, $ignoreBooking);

        $peak = 0;

        // 只檢查所要訂的那幾段相鄰區間
        for ($sequence = $fromSequence; $sequence < $toSequence; $sequence++) {
            $peak = max($peak, $occupied[$sequence] ?? 0);
        }

        return max(0, $capacity - $peak);
    }

    /**
     * 統計列車在指定日期、每一段相鄰區間已售出的張數。
     *
     * 陣列的鍵是區間起點的停靠順序，例如鍵 2 表示「第 2 站到第 3 站」這一段。
     *
     * @return array<int, int>
     */
    public function occupancyBySegment(
        Train $train,
        \DateTimeInterface $travelDate,
        ?int $ignoreBooking = null
    ): array {
        // 先把車站編號對應到停靠順序，避免在迴圈裡重複查詢
        $sequenceByStation = [];

        foreach ($train->stops() as $stop) {
            $sequenceByStation[(int) $stop->station_id] = (int) $stop->stop_sequence;
        }

        $query = Booking::active()
            ->where('train_id', $train->id())
            ->where('travel_date', $travelDate->format('Y-m-d'));

        if ($ignoreBooking !== null) {
            $query->where('id', '!=', $ignoreBooking);
        }

        $occupied = [];

        foreach ($query->get() as $booking) {
            $from = $sequenceByStation[(int) $booking->from_station_id] ?? null;
            $to   = $sequenceByStation[(int) $booking->to_station_id] ?? null;

            // 行經車站被調整過時，舊訂票可能已不在路線上，略過不計
            if ($from === null || $to === null || $from >= $to) {
                continue;
            }

            for ($sequence = $from; $sequence < $to; $sequence++) {
                $occupied[$sequence] = ($occupied[$sequence] ?? 0) + (int) $booking->ticket_count;
            }
        }

        return $occupied;
    }
}

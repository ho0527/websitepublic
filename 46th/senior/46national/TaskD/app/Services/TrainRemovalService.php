<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Booking;
use App\Models\Train;

/**
 * 列車刪除。
 *
 * 刪除列車前必須先確認是否還有未發車的訂票；若管理員確認仍要刪除，
 * 則把這些訂票一併取消，並以簡訊通知所有受影響的乘客。
 */
final class TrainRemovalService
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * 取得該列車尚未發車且未取消的訂票紀錄。
     *
     * @return array<int, Booking>
     */
    public function affectedBookings(Train $train, \DateTimeInterface $now): array
    {
        return Booking::active()
            ->where('train_id', $train->id())
            ->where('depart_at', '>', $now->format('Y-m-d H:i:s'))
            ->orderBy('depart_at')
            ->get();
    }

    /**
     * 刪除列車並取消受影響的訂票。
     *
     * 列車採軟刪除，讓已發車的歷史訂票紀錄與統計資料仍然完整可查。
     *
     * @return int 被取消的訂票筆數
     */
    public function remove(Train $train, \DateTimeInterface $now): int
    {
        return Database::instance()->transaction(function () use ($train, $now): int {
            $affected = $this->affectedBookings($train, $now);

            foreach ($affected as $booking) {
                $booking->cancel(Booking::CANCELLED_BY_TRAIN_REMOVED, $now);
                $this->smsService->sendTrainRemoved($booking);
            }

            $train->markAsRemoved($now);

            return count($affected);
        });
    }
}

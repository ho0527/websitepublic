<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Station;
use App\Models\Train;

/**
 * 車次查詢。
 *
 * 依起程站、到達站、車種與搭乘日期找出可搭乘的班次，
 * 並一併算好開車時間、到達時間、行駛時間與票價供頁面呈現。
 */
final class TrainLookupService
{
    private ScheduleService $scheduleService;

    private SeatService $seatService;

    public function __construct(ScheduleService $scheduleService, SeatService $seatService)
    {
        $this->scheduleService = $scheduleService;
        $this->seatService     = $seatService;
    }

    /**
     * 搜尋符合條件的車次。
     *
     * @param int|null $trainTypeId 車種編號，null 代表搜尋全部車種
     *
     * @return array<int, array{
     *     train: Train,
     *     type_name: string,
     *     origin_name: string,
     *     terminus_name: string,
     *     depart: \DateTimeImmutable,
     *     arrive: \DateTimeImmutable,
     *     duration_text: string,
     *     fare: int,
     *     available_seats: int
     * }>
     */
    public function search(
        Station $fromStation,
        Station $toStation,
        ?int $trainTypeId,
        \DateTimeInterface $travelDate
    ): array {
        $query = Train::active();

        if ($trainTypeId !== null) {
            $query->where('train_type_id', $trainTypeId);
        }

        $results = [];

        foreach ($query->orderBy('depart_time')->get() as $train) {
            // 當日不行駛的班次不列入結果
            if (!$train->runsOn($travelDate)) {
                continue;
            }

            $segment = $this->scheduleService->segmentOf(
                $train,
                $fromStation->id(),
                $toStation->id(),
                $travelDate
            );

            // 未依序行經起訖站的班次不列入結果
            if ($segment === null) {
                continue;
            }

            $fromSequence = (int) $train->stopAtStation($fromStation->id())->stop_sequence;
            $toSequence   = (int) $train->stopAtStation($toStation->id())->stop_sequence;

            $results[] = [
                'train'           => $train,
                'type_name'       => (string) ($train->type()?->name ?? ''),
                'origin_name'     => (string) ($train->originStop()?->station()?->name ?? ''),
                'terminus_name'   => (string) ($train->terminusStop()?->station()?->name ?? ''),
                'depart'          => $segment['depart'],
                'arrive'          => $segment['arrive'],
                'duration_text'   => $this->scheduleService->formatDuration($segment['duration_minutes']),
                'fare'            => $segment['fare'],
                'available_seats' => $this->seatService->availableSeats(
                    $train,
                    $travelDate,
                    $fromSequence,
                    $toSequence
                ),
            ];
        }

        // 依開車時間由早到晚排序
        usort(
            $results,
            static fn (array $left, array $right): int => $left['depart'] <=> $right['depart']
        );

        return $results;
    }
}

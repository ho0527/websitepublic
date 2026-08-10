<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\QueryBuilder;

/**
 * 列車（車次）。
 *
 * @property int         $id
 * @property int         $train_type_id
 * @property string      $code        車次代碼
 * @property string      $depart_time 發車站的發車時間
 * @property string|null $deleted_at  軟刪除時間
 */
final class Train extends Model
{
    protected static string $table = 'train';

    protected static array $fillable = ['train_type_id', 'code', 'depart_time', 'deleted_at'];

    /** 一列車最少的停靠站數（發車站與終點站） */
    public const MIN_STOPS = 2;

    /** 一列車最多的停靠站數 */
    public const MAX_STOPS = 15;

    /** @var array<int, TrainStop>|null 已載入的停靠站，避免重複查詢 */
    private ?array $stopCache = null;

    /** @var array<int, int>|null 已載入的行駛星期 */
    private ?array $serviceDayCache = null;

    /**
     * 只查詢仍在營運（未被刪除）的列車。
     */
    public static function active(): QueryBuilder
    {
        return self::query()->whereNull('deleted_at');
    }

    /**
     * 依車次代碼取得仍在營運的列車。
     */
    public static function findActiveByCode(string $code): ?Train
    {
        return self::active()->where('code', $code)->first();
    }

    /**
     * 車次代碼是否已被其他列車使用（含已刪除者，因為代碼具唯一性）。
     */
    public static function codeExists(string $code, ?int $exceptId = null): bool
    {
        $query = self::where('code', $code);

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->count() > 0;
    }

    /**
     * 取得本列車的車種。
     */
    public function type(): ?TrainType
    {
        return TrainType::find((int) $this->train_type_id);
    }

    /**
     * 依停靠順序取得所有行經車站。
     *
     * @return array<int, TrainStop>
     */
    public function stops(): array
    {
        if ($this->stopCache === null) {
            $this->stopCache = TrainStop::where('train_id', $this->id())
                ->orderBy('stop_sequence')
                ->get();
        }

        return $this->stopCache;
    }

    /**
     * 取得本列車在指定車站的停靠資料。
     */
    public function stopAtStation(int $stationId): ?TrainStop
    {
        foreach ($this->stops() as $stop) {
            if ((int) $stop->station_id === $stationId) {
                return $stop;
            }
        }

        return null;
    }

    /**
     * 發車站。
     */
    public function originStop(): ?TrainStop
    {
        $stops = $this->stops();

        return $stops[0] ?? null;
    }

    /**
     * 終點站。
     */
    public function terminusStop(): ?TrainStop
    {
        $stops = $this->stops();

        return $stops === [] ? null : $stops[count($stops) - 1];
    }

    /**
     * 取得行駛星期（0=日 … 6=六）。
     *
     * @return array<int, int>
     */
    public function serviceWeekdays(): array
    {
        if ($this->serviceDayCache === null) {
            $this->serviceDayCache = array_map(
                static fn (TrainServiceDay $day): int => (int) $day->weekday,
                TrainServiceDay::where('train_id', $this->id())->orderBy('weekday')->get()
            );
        }

        return $this->serviceDayCache;
    }

    /**
     * 本列車是否於指定日期行駛。
     */
    public function runsOn(\DateTimeInterface $date): bool
    {
        return in_array((int) $date->format('w'), $this->serviceWeekdays(), true);
    }

    /**
     * 是否已被刪除。
     */
    public function isRemoved(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * 將本列車標記為已刪除（軟刪除，保留歷史訂票紀錄的完整性）。
     */
    public function markAsRemoved(\DateTimeInterface $moment): void
    {
        $this->deleted_at = $moment->format('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * 重設行駛星期。
     *
     * @param array<int, int> $weekdays
     */
    public function replaceServiceWeekdays(array $weekdays): void
    {
        TrainServiceDay::where('train_id', $this->id())->delete();

        foreach (array_unique($weekdays) as $weekday) {
            TrainServiceDay::create([
                'train_id' => $this->id(),
                'weekday'  => $weekday,
            ]);
        }

        $this->serviceDayCache = null;
    }

    /**
     * 重設行經車站。
     *
     * @param array<int, array<string, int>> $stops 依序排列，每筆含 station_id/travel_minutes/stop_minutes/fare_from_origin
     */
    public function replaceStops(array $stops): void
    {
        TrainStop::where('train_id', $this->id())->delete();

        foreach (array_values($stops) as $index => $stop) {
            TrainStop::create([
                'train_id'         => $this->id(),
                'station_id'       => $stop['station_id'],
                'stop_sequence'    => $index + 1,
                'travel_minutes'   => $stop['travel_minutes'],
                'stop_minutes'     => $stop['stop_minutes'],
                'fare_from_origin' => $stop['fare_from_origin'],
            ]);
        }

        $this->stopCache = null;
    }
}

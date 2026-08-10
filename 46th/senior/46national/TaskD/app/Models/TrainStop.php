<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 列車行經車站與其時刻／票價設定。
 *
 * @property int $id
 * @property int $train_id
 * @property int $station_id
 * @property int $stop_sequence    停靠順序，自 1 起算
 * @property int $travel_minutes   自前一站行駛到本站所需分鐘數
 * @property int $stop_minutes     在本站的停留分鐘數
 * @property int $fare_from_origin 自發車站累計至本站的票價
 */
final class TrainStop extends Model
{
    protected static string $table = 'train_stop';

    protected static array $fillable = [
        'train_id',
        'station_id',
        'stop_sequence',
        'travel_minutes',
        'stop_minutes',
        'fare_from_origin',
    ];

    /**
     * 取得本停靠站對應的車站。
     */
    public function station(): ?Station
    {
        return Station::find((int) $this->station_id);
    }
}

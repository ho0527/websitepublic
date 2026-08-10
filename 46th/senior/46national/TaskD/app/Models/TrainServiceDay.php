<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 列車行駛星期。
 *
 * @property int $id
 * @property int $train_id
 * @property int $weekday 0=星期日、1=星期一 … 6=星期六
 */
final class TrainServiceDay extends Model
{
    protected static string $table = 'train_service_day';

    protected static array $fillable = ['train_id', 'weekday'];

    /** @var array<int, string> 星期的中文名稱 */
    private const WEEKDAY_NAMES = ['日', '一', '二', '三', '四', '五', '六'];

    /**
     * 取得星期的中文名稱。
     */
    public static function nameOf(int $weekday): string
    {
        return self::WEEKDAY_NAMES[$weekday] ?? '';
    }

    /**
     * 取得全部星期的中文名稱，供表單顯示。
     *
     * @return array<int, string>
     */
    public static function allNames(): array
    {
        return self::WEEKDAY_NAMES;
    }
}

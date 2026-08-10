<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 車種（區間列車、快速列車、磁浮列車等）。
 *
 * @property int    $id
 * @property string $name     車種名稱
 * @property int    $capacity 乘客承載量
 */
final class TrainType extends Model
{
    protected static string $table = 'train_type';

    protected static array $fillable = ['name', 'capacity'];

    /**
     * 取得全部車種。
     *
     * @return array<int, TrainType>
     */
    public static function allOrdered(): array
    {
        return self::query()->orderBy('id')->get();
    }

    /**
     * 以編號為索引取得所有車種。
     *
     * @return array<int, TrainType>
     */
    public static function keyedById(): array
    {
        $types = [];

        foreach (self::allOrdered() as $type) {
            $types[$type->id()] = $type;
        }

        return $types;
    }

    /**
     * 判斷是否有列車使用本車種（含已軟刪除的列車，避免破壞歷史資料的外鍵）。
     */
    public function isInUse(): bool
    {
        return Train::where('train_type_id', $this->id())->count() > 0;
    }

    /**
     * 車種名稱是否已被其他車種使用。
     */
    public static function nameExists(string $name, ?int $exceptId = null): bool
    {
        $query = self::where('name', $name);

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->count() > 0;
    }
}

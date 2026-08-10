<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * 車站。
 *
 * @property int    $id
 * @property string $code 英文代碼，做為網址與開放資料的識別字
 * @property string $name 中文名稱
 */
final class Station extends Model
{
    protected static string $table = 'station';

    protected static array $fillable = ['code', 'name'];

    /** @var array<string, Station>|null 依英文代碼建立的快取，避免同一次請求重複查詢 */
    private static ?array $codeIndex = null;

    /**
     * 依中文名稱排序取得所有車站。
     *
     * @return array<int, Station>
     */
    public static function allOrdered(): array
    {
        return self::query()->orderBy('id')->get();
    }

    /**
     * 以英文代碼取得車站（不分大小寫）。
     */
    public static function findByCode(string $code): ?Station
    {
        if (self::$codeIndex === null) {
            self::$codeIndex = [];

            foreach (self::allOrdered() as $station) {
                self::$codeIndex[strtoupper((string) $station->code)] = $station;
            }
        }

        return self::$codeIndex[strtoupper($code)] ?? null;
    }

    /**
     * 以編號為索引取得所有車站，方便在迴圈中查表。
     *
     * @return array<int, Station>
     */
    public static function keyedById(): array
    {
        $stations = [];

        foreach (self::allOrdered() as $station) {
            $stations[$station->id()] = $station;
        }

        return $stations;
    }
}

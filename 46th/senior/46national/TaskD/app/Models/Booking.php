<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\QueryBuilder;

/**
 * 訂票紀錄。
 *
 * @property int         $id
 * @property string      $booking_code
 * @property string      $phone
 * @property int         $train_id
 * @property int         $from_station_id
 * @property int         $to_station_id
 * @property string      $travel_date
 * @property string      $depart_at
 * @property string      $arrive_at
 * @property int         $ticket_count
 * @property int         $unit_price
 * @property int         $total_price
 * @property string      $status
 * @property string|null $cancelled_source
 * @property string|null $cancelled_at
 * @property string      $created_at
 */
final class Booking extends Model
{
    protected static string $table = 'booking';

    protected static array $fillable = [
        'booking_code',
        'phone',
        'train_id',
        'from_station_id',
        'to_station_id',
        'travel_date',
        'depart_at',
        'arrive_at',
        'ticket_count',
        'unit_price',
        'total_price',
        'status',
        'cancelled_source',
        'cancelled_at',
        'created_at',
    ];

    /** 已訂位 */
    public const STATUS_BOOKED = 'BOOKED';

    /** 已取消 */
    public const STATUS_CANCELLED = 'CANCELLED';

    /** 取消來源：乘客自行取消 */
    public const CANCELLED_BY_PASSENGER = 'PASSENGER';

    /** 取消來源：管理員取消 */
    public const CANCELLED_BY_ADMIN = 'ADMIN';

    /** 取消來源：列車被刪除 */
    public const CANCELLED_BY_TRAIN_REMOVED = 'TRAIN_REMOVED';

    /**
     * 只查詢仍有效（未取消）的訂票。
     */
    public static function active(): QueryBuilder
    {
        return self::where('status', self::STATUS_BOOKED);
    }

    /**
     * 以訂票編號查詢（區分大小寫，由資料庫的 utf8mb4_bin 定序保證）。
     */
    public static function findByCode(string $code): ?Booking
    {
        return self::where('booking_code', $code)->first();
    }

    /**
     * 訂票編號是否已存在。
     */
    public static function codeExists(string $code): bool
    {
        return self::where('booking_code', $code)->count() > 0;
    }

    /**
     * 是否已被取消。
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * 是否尚未發車。
     */
    public function isBeforeDeparture(\DateTimeInterface $now): bool
    {
        return new \DateTimeImmutable((string) $this->depart_at) > $now;
    }

    /**
     * 是否可以被取消（未取消且尚未發車）。
     */
    public function isCancellable(\DateTimeInterface $now): bool
    {
        return !$this->isCancelled() && $this->isBeforeDeparture($now);
    }

    /**
     * 標記為已取消。
     */
    public function cancel(string $source, \DateTimeInterface $moment): void
    {
        $this->status           = self::STATUS_CANCELLED;
        $this->cancelled_source = $source;
        $this->cancelled_at     = $moment->format('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * 所屬列車（含已被刪除者，以便顯示歷史紀錄）。
     */
    public function train(): ?Train
    {
        return Train::find((int) $this->train_id);
    }

    /**
     * 起程站。
     */
    public function fromStation(): ?Station
    {
        return Station::find((int) $this->from_station_id);
    }

    /**
     * 到達站。
     */
    public function toStation(): ?Station
    {
        return Station::find((int) $this->to_station_id);
    }
}

<?php
namespace App\Models;

use App\Core\Model;

/**
 * 場次（Seating）。
 *
 * 總座位數      = seats_per_competitor * competitor_count
 * 單一國家上限  = 總座位數 - seats_per_competitor
 *   （扣掉一位餐飲服務選手服務的座位，確保不會有賓客坐在同國選手服務的桌上）
 */
class Seating extends Model
{
    /**
     * 取得全部場次（含所屬餐飲模組名稱與說明）
     */
    public function all(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT s.id, s.dining_module_id, s.name, s.configuration,
                    s.start_time, s.end_time, s.seats_per_competitor,
                    s.competitor_count, s.sort_order,
                    m.name AS module_name, m.description AS module_description
               FROM seating s
               INNER JOIN dining_module m ON m.id = s.dining_module_id
              ORDER BY s.sort_order, s.id'
        );

        return array_map([self::class, 'decorate'], $rows);
    }

    /**
     * 取得以 id 為索引的場次對照表
     */
    public function allKeyedById(): array
    {
        $seatings = [];

        foreach ($this->all() as $seating) {
            $seatings[(int) $seating['id']] = $seating;
        }

        return $seatings;
    }

    /**
     * 依 id 取得單一場次
     */
    public function find(int $id): ?array
    {
        $row = $this->db->fetchOne(
            'SELECT s.id, s.dining_module_id, s.name, s.configuration,
                    s.start_time, s.end_time, s.seats_per_competitor,
                    s.competitor_count, s.sort_order,
                    m.name AS module_name, m.description AS module_description
               FROM seating s
               INNER JOIN dining_module m ON m.id = s.dining_module_id
              WHERE s.id = ?',
            [$id]
        );

        return $row === null ? null : self::decorate($row);
    }

    /**
     * 補上計算欄位：總座位數、單一國家上限、顯示用時間與標題
     */
    private static function decorate(array $row): array
    {
        $seatsPerCompetitor = (int) $row['seats_per_competitor'];
        $competitorCount    = (int) $row['competitor_count'];

        $row['total_seats']      = $seatsPerCompetitor * $competitorCount;
        $row['max_per_country']  = max(0, $row['total_seats'] - $seatsPerCompetitor);
        $row['time_label']       = substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5);
        $row['label']            = $row['module_name'] . ' ' . $row['time_label'];

        return $row;
    }
}

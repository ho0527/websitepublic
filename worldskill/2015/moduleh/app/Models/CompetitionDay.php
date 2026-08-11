<?php
namespace App\Models;

use App\Core\Model;

/**
 * 競賽日（C1 - 04.08.2015 …），資料完全由資料庫設定。
 */
class CompetitionDay extends Model
{
    /**
     * 取得全部競賽日（依排序）
     */
    public function all(): array
    {
        return $this->db->fetchAll(
            'SELECT id, code, day_date, sort_order
               FROM competition_day
              ORDER BY sort_order, day_date'
        );
    }

    /**
     * 取得以 id 為索引的競賽日對照表
     */
    public function allKeyedById(): array
    {
        $days = [];

        foreach ($this->all() as $day) {
            $days[(int) $day['id']] = $day;
        }

        return $days;
    }

    /**
     * 依 id 取得單一競賽日
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT id, code, day_date, sort_order FROM competition_day WHERE id = ?',
            [$id]
        );
    }

    /**
     * 產生顯示用標題，例如「C1 - 04.08.2015」
     */
    public static function label(array $day): string
    {
        return $day['code'] . ' - ' . date('d.m.Y', strtotime($day['day_date']));
    }
}

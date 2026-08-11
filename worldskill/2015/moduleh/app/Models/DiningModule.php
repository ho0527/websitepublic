<?php
namespace App\Models;

use App\Core\Model;

/**
 * 餐飲模組（Casual Dining / Bar Service / Fine Dining / Banquet Dining）。
 * 首頁的「用餐體驗說明」表格即由此資料動態產生。
 */
class DiningModule extends Model
{
    /**
     * 取得全部餐飲模組，並附上該模組所有場次的摘要資訊
     */
    public function allWithSeatings(): array
    {
        $modules = $this->db->fetchAll(
            'SELECT id, name, description, sort_order
               FROM dining_module
              ORDER BY sort_order, id'
        );

        $seatings = $this->db->fetchAll(
            'SELECT id, dining_module_id, name, configuration, start_time, end_time,
                    seats_per_competitor, competitor_count
               FROM seating
              ORDER BY sort_order, id'
        );

        foreach ($modules as &$module) {
            $module['seatings'] = array_values(array_filter(
                $seatings,
                static fn (array $seating): bool
                    => (int) $seating['dining_module_id'] === (int) $module['id']
            ));
        }
        unset($module);

        return $modules;
    }
}

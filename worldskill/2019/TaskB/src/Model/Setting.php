<?php
/**
 * 網站設定（key/value）
 */

declare(strict_types=1);

namespace App\Model;

final class Setting extends BaseModel
{
    /** @var array<string, string> 快取，避免同一次請求重複查詢 */
    private array $cache = [];

    private bool $loaded = false;

    /** 一次載入全部設定 */
    public function all(): array
    {
        if (!$this->loaded) {
            foreach ($this->db->all('SELECT setting_key, setting_value FROM settings') as $row) {
                $this->cache[$row['setting_key']] = $row['setting_value'];
            }
            $this->loaded = true;
        }

        return $this->cache;
    }

    public function get(string $key, string $default = ''): string
    {
        $all = $this->all();

        return $all[$key] ?? $default;
    }

    /** 寫入單一設定（不存在則新增） */
    public function set(string $key, string $value): void
    {
        $this->db->run(
            'INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
            ['k' => $key, 'v' => $value]
        );

        $this->cache[$key] = $value;
    }

    /** 批次寫入 */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set((string) $key, (string) $value);
        }
    }
}

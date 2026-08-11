<?php
/**
 * 外掛管理與掛鉤（hook）機制
 *
 * 模擬 WordPress 的 add_action / add_filter：
 *   - action：在指定位置輸出內容（例如頁尾社群連結）
 *   - filter：讓外掛加工某個值後回傳（例如 SEO 標題）
 * 外掛程式放在 plugins/<slug>/plugin.php，是否載入由資料庫 plugins.is_active 決定。
 */

declare(strict_types=1);

namespace App\Core;

final class PluginManager
{
    /** @var array<string, array<int, callable[]>> action 名稱 => 優先序 => callable */
    private array $actions = [];

    /** @var array<string, array<int, callable[]>> filter 名稱 => 優先序 => callable */
    private array $filters = [];

    /** @var array<string, array> 已啟用的外掛資料 */
    private array $activePlugins = [];

    public function __construct(private string $pluginDir, private App $app)
    {
    }

    /** 載入所有已啟用外掛 */
    public function boot(array $plugins): void
    {
        foreach ($plugins as $plugin) {
            if ((int) $plugin['is_active'] !== 1) {
                continue;
            }

            $file = $this->pluginDir . '/' . $plugin['slug'] . '/plugin.php';
            if (!is_file($file)) {
                continue;
            }

            $this->activePlugins[$plugin['slug']] = $plugin;

            /** @var callable $register 外掛檔案回傳一個註冊函式 */
            $register = require $file;
            if (is_callable($register)) {
                $register($this, $this->app);
            }
        }
    }

    public function isActive(string $slug): bool
    {
        return isset($this->activePlugins[$slug]);
    }

    /** 註冊 action */
    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    /** 觸發 action（直接輸出） */
    public function doAction(string $hook, mixed ...$args): void
    {
        if (empty($this->actions[$hook])) {
            return;
        }

        $byPriority = $this->actions[$hook];
        ksort($byPriority);

        foreach ($byPriority as $callbacks) {
            foreach ($callbacks as $callback) {
                $callback(...$args);
            }
        }
    }

    /** 註冊 filter */
    public function addFilter(string $hook, callable $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = $callback;
    }

    /** 套用 filter，回傳被外掛加工後的值 */
    public function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        if (empty($this->filters[$hook])) {
            return $value;
        }

        $byPriority = $this->filters[$hook];
        ksort($byPriority);

        foreach ($byPriority as $callbacks) {
            foreach ($callbacks as $callback) {
                $value = $callback($value, ...$args);
            }
        }

        return $value;
    }
}

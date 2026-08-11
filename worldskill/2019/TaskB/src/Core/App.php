<?php
/**
 * 應用程式容器
 *
 * 負責建立資料庫連線、模型、佈景主題與外掛，並提供全站共用資料（設定、選單）。
 */

declare(strict_types=1);

namespace App\Core;

use App\Model\Category;
use App\Model\LoginAttempt;
use App\Model\MediaLibrary;
use App\Model\Museum;
use App\Model\Plugin;
use App\Model\Post;
use App\Model\Setting;
use App\Model\User;

final class App
{
    public Database $db;
    public Setting $settings;
    public User $users;
    public Category $categories;
    public Museum $museums;
    public Post $posts;
    public Plugin $plugins;
    public LoginAttempt $loginAttempts;
    public MediaLibrary $media;
    public PluginManager $hooks;
    public Theme $theme;
    public Auth $auth;

    public function __construct(public array $config, public string $basePath)
    {
        $this->db = Database::instance($config['db']);

        Url::configure($config['base_path'], (bool) $config['clean_urls']);

        // 模型
        $this->settings      = new Setting($this->db);
        $this->users         = new User($this->db);
        $this->categories    = new Category($this->db);
        $this->museums       = new Museum($this->db);
        $this->posts         = new Post($this->db);
        $this->plugins       = new Plugin($this->db);
        $this->loginAttempts = new LoginAttempt($this->db);
        $this->media         = new MediaLibrary($basePath);

        // 身分驗證（含暴力破解鎖定參數）
        $this->auth = new Auth(
            $this->users,
            $this->loginAttempts,
            max(1, (int) $this->settings->get('security_max_attempts', '5')),
            max(1, (int) $this->settings->get('security_lockout_min', '15'))
        );

        // 佈景主題：子主題 Kazan_MuseumTour，父主題由 style.css 的 Template 宣告
        $this->theme = new Theme(
            $basePath . '/themes',
            $this->settings->get('active_theme', 'Kazan_MuseumTour')
        );

        // 外掛
        $this->hooks = new PluginManager($basePath . '/plugins', $this);
        $this->hooks->boot($this->plugins->all());
    }

    public function setting(string $key, string $default = ''): string
    {
        return $this->settings->get($key, $default);
    }

    /**
     * 前台主選單
     *
     * Museums 為下拉選單，內容由資料庫的博物館清單動態產生。
     */
    public function menu(): array
    {
        $children = [];
        foreach ($this->museums->published() as $museum) {
            $children[] = [
                'label'    => $museum['title'],
                'url'      => Url::to($museum['slug']),
                'route'    => $museum['slug'],
                'selected' => (int) $museum['is_selected'] === 1,
            ];
        }

        return [
            ['label' => 'Home', 'url' => Url::to(''), 'route' => '', 'children' => []],
            ['label' => 'Museums', 'url' => Url::to('museums'), 'route' => 'museums', 'children' => $children],
            ['label' => 'Seasonal Events', 'url' => Url::to('news/seasonal-events'), 'route' => 'news/seasonal-events', 'children' => []],
            ['label' => 'News', 'url' => Url::to('news'), 'route' => 'news', 'children' => []],
            ['label' => 'Contact', 'url' => Url::to('contact'), 'route' => 'contact', 'children' => []],
        ];
    }

    /** 目前登入者可看到的後台選單 */
    public function adminMenu(): array
    {
        $items = [
            ['label' => 'Dashboard', 'route' => 'admin',            'icon' => '▦', 'cap' => null],
            ['label' => 'Museums',   'route' => 'admin/museums',    'icon' => '▤', 'cap' => 'manage_museums'],
            ['label' => 'News',      'route' => 'admin/posts',      'icon' => '✎', 'cap' => 'manage_posts'],
            ['label' => 'Categories','route' => 'admin/categories', 'icon' => '☰', 'cap' => 'manage_terms'],
            ['label' => 'Media',     'route' => 'admin/media',      'icon' => '▣', 'cap' => 'manage_media'],
            ['label' => 'Plugins',   'route' => 'admin/plugins',    'icon' => '⊕', 'cap' => 'manage_plugins'],
            ['label' => 'Users',     'route' => 'admin/users',      'icon' => '☺', 'cap' => 'manage_users'],
            ['label' => 'Security',  'route' => 'admin/security',   'icon' => '⛨', 'cap' => 'view_security'],
            ['label' => 'Settings',  'route' => 'admin/settings',   'icon' => '⚙', 'cap' => 'manage_settings'],
        ];

        return array_values(array_filter(
            $items,
            fn (array $item) => $item['cap'] === null || $this->auth->can($item['cap'])
        ));
    }
}

<?php
/**
 * 載入設定與所有類別（不使用 Composer，改以簡單的自動載入表）
 */

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Isbn.php';
require_once __DIR__ . '/src/GraphQL/Parser.php';
require_once __DIR__ . '/src/GraphQL/Executor.php';
require_once __DIR__ . '/src/Services/AuthService.php';
require_once __DIR__ . '/src/Services/BookService.php';
require_once __DIR__ . '/src/Services/RentService.php';
require_once __DIR__ . '/src/Schema.php';
require_once __DIR__ . '/src/App.php';
require_once __DIR__ . '/src/Installer.php';

Database::configure($config['db']);
Installer::configure($config['account_domains']);

return $config;

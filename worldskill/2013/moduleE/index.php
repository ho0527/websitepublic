<?php
/**
 * WorldSkills 2013 - Skill 17 - Module E
 * LVB (Leipziger Verkehrsbetriebe) 中間路線管理系統
 *
 * 應用程式進入點：載入題目提供的 Yii 1.1.13 框架並啟動 Web 應用程式。
 */

// 題目提供的框架位置（保留在 material 資料夾內，不另行複製）
$yiiFramework = __DIR__ . '/material/Library Documentation and Framework/yii-1.1.13.e9e4a0/framework/yii.php';
$appConfig    = __DIR__ . '/protected/config/main.php';

// PHP 8.3 下 Yii 1.1.13 會出現大量過時語法警告，僅關閉通知類訊息，錯誤仍會顯示
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);

// 正式展示環境關閉除錯模式；除錯時可改為 true
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 0);

require_once($yiiFramework);
Yii::createWebApplication($appConfig)->run();

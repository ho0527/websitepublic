<?php
/**
 * Module F 共用啟動檔：載入設定與各個類別。
 */
require_once __DIR__ . '/Transport.php';
require_once __DIR__ . '/SoapClientLite.php';
require_once __DIR__ . '/Config.php';
require_once __DIR__ . '/StatisticsGateway.php';
require_once __DIR__ . '/StatisticsRepository.php';
require_once __DIR__ . '/LineChart.php';

/**
 * HTML 跳脫。
 */
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

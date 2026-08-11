<?php
/**
 * 中央統計 Web Service 的 HTTP 進入點
 * （模擬題目提供的 vhost32.skill17.local/Module_F/WSC_Statistics.php）。
 *
 *   GET  ?wsdl  取得服務描述檔
 *   POST        SOAP 1.1 請求
 *
 * 實際的處理邏輯位於 SoapEndpoint。
 */
require_once __DIR__ . '/SoapEndpoint.php';

$endpoint = new SoapEndpoint(__DIR__ . '/data');

// 服務描述檔
if (isset($_GET['wsdl'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $url    = $scheme . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
    header('Content-Type: text/xml; charset=UTF-8');
    echo $endpoint->wsdl($url);
    exit;
}

// SOAP 請求
$result = $endpoint->handle(file_get_contents('php://input'));

header('Content-Type: text/xml; charset=UTF-8', true, $result['status']);
echo $result['body'];

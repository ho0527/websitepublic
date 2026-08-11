@echo off
REM ---------------------------------------------------------------------------
REM 以 PHP 內建網頁伺服器啟動「中央統計 Web Service」，監聽 127.0.0.1:8017。
REM
REM 本機 nginx 只掛了一個 php-cgi 工作行程，PHP 程式對同一台伺服器再發 HTTP
REM 請求會互相等待到逾時；把服務跑在獨立的行程上，Module F 前端就會自動改用
REM 真正的 HTTP SOAP 呼叫（見 lib/Config.php 的 createTransport）。
REM
REM 關掉這個視窗即可停止服務，前端會自動退回同行程呼叫。
REM ---------------------------------------------------------------------------
echo Starting the WSC Statistics web service on http://127.0.0.1:8017/
echo WSDL: http://127.0.0.1:8017/WSC_Statistics.php?wsdl
echo Press Ctrl+C to stop.
php -S 127.0.0.1:8017 -t "%~dp0"

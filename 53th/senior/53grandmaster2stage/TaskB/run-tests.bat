@echo off
REM 執行模組 B 的所有單元測試
REM 用法：直接雙擊或於命令列執行 run-tests.bat
cd /d "%~dp0"
php vendor\phpunit\phpunit\phpunit --testdox %*
pause

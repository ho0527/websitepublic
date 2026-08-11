D16 - API Request Logger
========================

功能
----
提供一個 PHP endpoint，接收 Content-Type: application/json 的 POST 請求，
每次被呼叫時，就把 request body 原文儲存成一個文字檔。

要呼叫的 URL
------------
API endpoint：
    http://127.0.0.1:83/worldskill/2022/modulea/D16/api.php

說明頁（顯示 API 用法、狀態碼，以及目前已記錄的檔案列表與內容預覽）：
    http://127.0.0.1:83/worldskill/2022/modulea/D16/

呼叫方式
--------
方法：POST
標頭：Content-Type: application/json
內容：任何合法的 JSON

curl 範例：
    curl -X POST -H "Content-Type: application/json" -d "{\"name\":\"Alice\",\"score\":100}" http://127.0.0.1:83/worldskill/2022/modulea/D16/api.php

檔案儲存位置
------------
儲存於本資料夾下的 logs/ 子資料夾：
    C:\nginx\skill\worldskill\2022\modulea\D16\logs\
資料夾若不存在，程式會自動以 mkdir 建立。

檔名規則（與題目規格的差異，重要）
----------------------------------
題目規格要求檔名為：
    HH:MM:SS-request.txt

但 Windows 檔案系統不允許檔名包含冒號（:），該字元被保留給磁碟機代號與
NTFS 替代資料流（Alternate Data Stream）使用，因此本實作改為使用連字號：

    HH-MM-SS-request.txt        例如 14-05-09-request.txt

若同一秒內被重複呼叫，會自動在檔名後附加序號以避免覆蓋：

    14-05-09-request-1.txt
    14-05-09-request-2.txt

此差異亦在 api.php 與 index.php 的註解／頁面說明中標註。

回應格式
--------
一律回傳 JSON。

成功（HTTP 200）：
    {
        "success": true,
        "message": "Request body 已儲存。",
        "file": "14-05-09-request.txt",
        "path": "logs/14-05-09-request.txt",
        "bytes": 28,
        "saved_at": "2025-01-01 14:05:09"
    }

錯誤狀態碼：
    400  request body 為空，或 JSON 格式錯誤
    405  使用了 POST 以外的方法
    415  Content-Type 不是 application/json
    500  logs 資料夾無法建立或檔案寫入失敗

檔案清單
--------
    index.php    說明頁，並列出 logs/ 內已記錄的檔案與內容預覽
    api.php      實際接收 POST JSON 並寫檔的 endpoint
    README.txt   本說明檔
    logs/        儲存 request body 的資料夾（首次呼叫時自動建立）

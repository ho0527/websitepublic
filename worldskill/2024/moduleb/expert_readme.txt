WorldSkills 2024 TP17 Web Technologies - Module B (Products Management)
執行說明 / Executing guide
========================================================================

1. 環境需求 (Requirements)
------------------------------------------------------------------------
- PHP 8.3 以上，需啟用 pdo_mysql、mbstring、gd（gd 非必要，僅用於圖片檢查）
- MySQL / MariaDB
- 網頁伺服器：nginx（本機設定為 http://127.0.0.1:83/，網站根目錄 C:\nginx\skill）


2. 建立資料庫 (Database setup)
------------------------------------------------------------------------
資料庫名稱：worldskill2024_moduleb
連線設定寫在 config.php（預設 root / 空密碼 / 127.0.0.1:3306）

方式 A：使用 mysql 指令
    mysql -u root -p < "!SQL/schema.sql"

方式 B：本機沒有 mysql 指令時，用附的 PHP 匯入工具
    cd "!SQL"
    php import.php

注意：schema.sql 會先 DROP DATABASE 再重建，執行後既有資料會被清空。
資料庫結構與 ER 圖說明請見 !SQL/er-diagram.md。


3. 兩種佈署方式 (Two deployment modes)
------------------------------------------------------------------------
本模組的程式碼可以同時支援下列兩種佈署方式，兩者共用同一份程式。

--- 方式 1：nginx 靜態佈署（不需要改任何 nginx 設定，預設可用）---------
網址：http://127.0.0.1:83/worldskill/2024/moduleb/

因為這台 nginx 沒有針對本資料夾設定 rewrite（try_files）規則，
路由改以查詢字串傳遞，格式為：
    <base>/index.php?route=/products/03000123456789

為了讓固定路徑也能直接使用，已在資料夾內建立實體的進入點目錄，
瀏覽下列網址即可（nginx 會自動補上結尾斜線）：
    /worldskill/2024/moduleb/                     首頁（轉到登入頁）
    /worldskill/2024/moduleb/login/               管理員登入
    /worldskill/2024/moduleb/companies/           公司清單
    /worldskill/2024/moduleb/companies/new/       新增公司
    /worldskill/2024/moduleb/companies/deactivated/  已停用公司清單
    /worldskill/2024/moduleb/products/            產品清單
    /worldskill/2024/moduleb/products/new/        新增產品
    /worldskill/2024/moduleb/products/hidden/     已隱藏產品清單
    /worldskill/2024/moduleb/products.json        產品列表 API
    /worldskill/2024/moduleb/gtin/                GTIN 批量驗證頁
    /worldskill/2024/moduleb/01/?gtin=03000123456789   公開產品頁

含動態片段的網址（產品 GTIN、公司 id）在這個模式下請使用 index.php?route=，
例如：
    /worldskill/2024/moduleb/index.php?route=/products/03000123456789
    /worldskill/2024/moduleb/index.php?route=/products/03000123456789.json
    /worldskill/2024/moduleb/index.php?route=/01/03000123456789&lang=fr
頁面上的所有連結都會自動產生成正確的形式，直接點選即可。

--- 方式 2：PHP 內建伺服器 + nginx 反向代理（完全符合試題的乾淨網址）---
nginx.conf 的 83 埠設定中已存在下列區塊（不需另外修改）：
    location /worldskill2024moduleb/ {
        proxy_pass http://127.0.0.1:8942/;
        ...
    }

啟動指令（在本模組資料夾內執行，需保持視窗開著）：
    cd C:\nginx\skill\worldskill\2024\moduleb
    php -S 127.0.0.1:8942 -t . router.php

啟動之後即可使用試題定義的網址：
    http://127.0.0.1:83/worldskill2024moduleb/login
    http://127.0.0.1:83/worldskill2024moduleb/companies
    http://127.0.0.1:83/worldskill2024moduleb/products
    http://127.0.0.1:83/worldskill2024moduleb/products/new
    http://127.0.0.1:83/worldskill2024moduleb/products/03000123456789
    http://127.0.0.1:83/worldskill2024moduleb/products.json
    http://127.0.0.1:83/worldskill2024moduleb/products.json?query=juice&page=2
    http://127.0.0.1:83/worldskill2024moduleb/products/03000123456789.json
    http://127.0.0.1:83/worldskill2024moduleb/gtin
    http://127.0.0.1:83/worldskill2024moduleb/01/03000123456789

--- 方式 3（參考）：若允許修改 nginx 設定 ------------------------------
把下列區塊加進 83 埠的 server 之後，方式 1 的網址也能變成乾淨網址：

    location ^~ /worldskill/2024/moduleb/ {
        try_files $uri $uri/ /worldskill/2024/moduleb/index.php?route=$uri&$args;
        location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }

本次交付沒有修改 nginx.conf，上面只是提供給評分老師參考。


4. 管理員登入 (Admin access)
------------------------------------------------------------------------
登入頁：/login
密碼（passphrase）：admin
未登入而存取任何管理功能（公司、產品的清單與編輯）會回應 HTTP 401。


5. 主要功能與對應網址 (Features)
------------------------------------------------------------------------
公司管理（需登入）
    GET  /companies                     公司清單（含已停用公司，以標籤區分）
    GET  /companies/new                 新增公司表單
    POST /companies                     建立公司
    GET  /companies/deactivated         已停用公司的獨立清單
    GET  /companies/{id}                單一公司 + 該公司的產品
    GET  /companies/{id}/edit           編輯公司表單
    POST /companies/{id}                更新公司
    POST /companies/{id}/deactivate     停用公司（同時把旗下產品標記為隱藏）
    POST /companies/{id}/activate       重新啟用公司
    ※ 依試題要求，網頁介面沒有提供任何刪除公司的功能。

產品管理（需登入）
    GET  /products                      所有產品清單
    GET  /products/new                  新增產品表單
    POST /products                      建立產品
    GET  /products/hidden               已隱藏產品清單（可永久刪除）
    GET  /products/{GTIN}               單一產品管理頁
    GET  /products/{GTIN}/edit          編輯產品表單
    POST /products/{GTIN}               更新產品（含圖片上傳／更換）
    POST /products/{GTIN}/hide          標記為隱藏
    POST /products/{GTIN}/unhide        取消隱藏
    POST /products/{GTIN}/delete        永久刪除（僅限已隱藏的產品，否則回 403）
    POST /products/{GTIN}/image/remove  移除已上傳的圖片

JSON API（公開）
    GET  /products.json                 產品列表，含 pagination（每頁 10 筆）
    GET  /products.json?query=KEYWORD   以關鍵字搜尋 name / name(fr) /
                                        description / description(fr)
    GET  /products.json?page=2          翻頁
    GET  /products/{GTIN}.json          單一產品；不存在或已隱藏一律回 404

公開頁面
    GET  /gtin                          GTIN 批量驗證頁（textarea 逐行輸入）
    POST /gtin                          顯示驗證結果，全部有效時顯示綠色勾勾與
                                        「All valid」
    GET  /01/{GTIN}                     公開產品頁（行動裝置版面）
    GET  /01/{GTIN}?lang=fr             法文版本（html lang 會一併切換）


6. 檔案結構 (Project structure)
------------------------------------------------------------------------
    index.php                單一入口與路由表
    router.php               PHP 內建伺服器用的路由腳本
    config.php               資料庫、密碼、網址前綴設定
    src/Database.php         PDO 連線（單例、prepared statement）
    src/Helper.php           跳脫、網址、JSON 輸出等共用函式
    src/Auth.php             管理員登入與 401 保護
    src/View.php             極簡樣板引擎
    src/ImageUploader.php    產品圖片上傳／刪除
    src/CompanyRepository.php  公司資料存取
    src/ProductRepository.php  產品資料存取
    src/AdminController.php    後台頁面與表單處理
    src/ApiController.php      JSON API
    src/PublicController.php   公開頁面
    views/                   HTML 樣板
    media/placeholder.svg    未上傳圖片時的預設佔位圖
    media/uploads/           產品圖片實際存放位置
    index.css                樣式表
    !SQL/schema.sql          資料庫結構與測試資料（DB dump）
    !SQL/import.php          匯入 schema.sql 的小工具
    !SQL/er-diagram.md       ER 圖與資料庫設計說明
    login/、companies/、products/、products.json/、gtin/、01/
                             nginx 靜態佈署用的固定網址進入點

    舊版純前端原型留下的檔案（*.html 已改寫成轉址頁、*.js 已不再被引用）：
    company.js、companydetail.js、deactivatecompany.js、deactivateproduct.js、
    editcompany.js、editproduct.js、newcompany.js、newproduct.js、product.js、
    gtintest.js、signin.js、initialize.js、index.js
    這些檔案保留是為了不刪除既有內容，實際功能已全部改由 PHP 後端提供。


7. 測試資料 (Sample data)
------------------------------------------------------------------------
公司 3 筆（Euro Expo、Maison Lumiere 為啟用，Vieux Moulin 為停用）
產品 16 筆，其中 Vieux Moulin 的 2 筆與 Euro Expo 的 1 筆為隱藏狀態，
公開可見的產品共 13 筆，正好可以呈現 API 的兩頁分頁。

可直接用來測試的 GTIN：
    03000123456789  Organic Apple Juice（14 碼，開頭有 0）
    3000123456790   Organic Grape Juice（13 碼）
    3000123456794   Country Sourdough Bread（隱藏，API 會回 404）

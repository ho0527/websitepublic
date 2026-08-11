# **製作完成**
## 如有問題請回報 將會進行更新 如有需要就自己使用不用問我

---

### 關於
##### **有**進行前後端分離
##### 類型: 全公開、技能競賽
##### 完成日期: 2026/08/11
##### 製作過程: 約1天
##### 製作人員:
- 小賀chris(DC: chris0527(小賀chris))
##### 參考: 51青年組網頁技術全國賽正式試題.pdf（第五站、第六站 模組D）
##### 特別感謝: 無
##### 使用技術: html css js php mysql
##### 目前版本: v2.0.0

---

### 版本迭帶
#### v2.0.0
##### 完成時間: 2026/08/11
##### 錯誤回報:
- 無
##### 敘述:
依照全國賽正式試題重新製作完成
- 後端改以 PHP + PDO 實作，完成正式試題的 15 支 API（原本僅有前端，且串接的是不存在的 Django 服務）
- 資料表依照試題 D.3 的 ER 圖重建（users / houses / images / applications / ads）
- 前端 8 個頁面全部重寫，改用原生 fetch，移除對站台共用套件的相依，且不引用任何 CDN
- 附上 `!SQL/schema.sql` 與 `!TEST/api-test.sh` 自動測試腳本（64 個案例全數通過）

#### v1.0.0
##### 完成時間: 2024/01/16
##### 錯誤回報:
- 無
##### 敘述:
公告試題製作完成

---
---

# 第51屆全國技能競賽 網頁技術 模組 D - 房屋交易平台

依據 `51青年組網頁技術全國賽正式試題.pdf` 中「第五站 – API 功能開發 - 模組 D」與
「第六站 – API 串接版面設計」的規格實作，包含 15 支 API 以及串接完成的前端畫面。

---

## 1. 執行環境

| 項目 | 設定 |
| --- | --- |
| 網站根目錄 | `C:\nginx\skill` |
| 網址 | <http://127.0.0.1:83/51th/senior/51national/moduled/> |
| PHP | 8.3.7（PDO、pdo_mysql、gd） |
| 資料庫 | MySQL 3306，帳號 `root`，密碼空白 |
| 資料庫名稱 | `worldskill51_moduled` |

### 安裝步驟

```bash
cd C:/nginx/skill/51th/senior/51national/moduled

# 1. 建立資料表與測試資料
"C:/xampp/mysql/bin/mysql" -uroot --default-character-set=utf8mb4 < "!SQL/schema.sql"

# 2. 產生測試用的房屋示意圖（寫入 uploads/）
php "!SQL/generate_images.php"
```

資料庫連線設定位於 `api/config.php`。

---

## 2. 內建帳號

| Email | 暱稱 | 密碼 | 身分 |
| --- | --- | --- | --- |
| admin@localhost | admin | adminpass | 管理員 ADMIN |
| user1@localhost | user1 | user1pass | 會員 USER |
| user2@localhost ~ user5@localhost | user2 ~ user5 | user2pass ~ user5pass | 會員 USER |

Token 為 `sha256(email)` 的小寫 hex，例如 admin 的 token 為
`7ba684f146c9445720e4b9a8d6ed775de4804bc2dbe01fb6490b8cce05db9f43`（與試題範例相同）。

---

## 3. API

進入點為 `api/index.php`，使用 PATH_INFO 形式的網址（**不經過任何轉址**）：

```
http://127.0.0.1:83/51th/senior/51national/moduled/api/index.php/user/login
```

| # | 功能 | Method | URL |
| --- | --- | --- | --- |
| 1 | 會員登入 | POST | `/api/index.php/user/login` |
| 2 | 會員登出 | POST | `/api/index.php/user/logout` |
| 3 | 會員註冊 | POST | `/api/index.php/user/register` |
| 4 | 取得房屋列表 | GET | `/api/index.php/house` |
| 5 | 查看房屋 | GET | `/api/index.php/house/:house_id` |
| 6 | 瀏覽自己刊登的房屋列表 | GET | `/api/index.php/user/house` |
| 7 | 刊登房屋 | POST | `/api/index.php/house` |
| 8 | 編輯房屋 | PUT | `/api/index.php/house/:house_id` |
| 9 | 刪除房屋 | DELETE | `/api/index.php/house/:house_id` |
| 10 | 申請精選房屋 | POST | `/api/index.php/application` |
| 11 | 取消申請 | DELETE | `/api/index.php/application/:application_id` |
| 12 | 取得申請列表 | GET | `/api/index.php/application` |
| 13 | 審核申請 | PUT | `/api/index.php/application/:application_id` |
| 14 | 取得精選房屋列表 | GET | `/api/index.php/ads` |
| 15 | 取消精選房屋 | DELETE | `/api/index.php/ads/:ad_id` |

驗證方式為 `X-User-Token` 標頭（亦相容 `Authorization: Bearer <token>`）。

### 查詢字串

列表類 API（4、6、12、14）共用下列參數：

| 參數 | 說明 |
| --- | --- |
| `title` | 標題關鍵字（模糊比對） |
| `min_price` / `max_price` | 價格區間 |
| `room` | 房數（完全相符） |
| `min_room` / `max_room` | 房數區間（供畫面上「More」使用的延伸參數） |
| `min_age` / `max_age` | 屋齡區間 |
| `sort_by` | `published_at`（預設）、`price`、`square` |
| `order` | `asc` / `desc`（預設 `desc`） |
| `page` | 頁碼，未帶時為第 1 頁，每頁 10 筆 |
| `status` | 僅 API 12：`applied`（預設）、`approved`、`rejected`、`all` |

### 圖片欄位（API 7、8）

以 `multipart/form-data` 送出：

| 欄位 | 說明 |
| --- | --- |
| `images[]` | 新上傳的圖片檔 |
| `cover_index` | 封面索引，由 0 開始，超出範圍回傳 `MSG_INVALID_COVER_INDEX` |
| `keep_paths[]` | 僅編輯時使用，列出要保留的既有圖片；未帶時代表全部保留，沒被列到的圖片會連同檔案一併刪除 |
| `order[]` | 選填，指定最終順序，元素格式為 `keep:<既有路徑>` 或 `new:<images[] 的索引>` |

### 錯誤格式

```json
{ "success": false, "message": "MSG_INVALID_TOKEN", "data": "" }
```

錯誤代碼與狀態碼完全依照試題 D.4 的對照表實作（`MSG_INVALID_LOGIN` 403、
`MSG_USER_EXISTS` 409、`MSG_INVALID_TOKEN` 401、`MSG_PERMISSION_DENY` 403、
`MSG_MISSING_FIELD` / `MSG_WRONG_DATA_TYPE` / `MSG_IMAGE_CAN_NOT_PROCESS` /
`MSG_INVALID_COVER_INDEX` 400、`MSG_HOUSE_NOT_EXISTS` 404、`MSG_HOUSE_APPLIED` 409、
`MSG_HOUSE_ADVERTISED` 409、`MSG_APPLICATION_NOT_EXISTS` 404、`MSG_ALREADY_ADVERTISED` 409、
`MSG_AD_NOT_EXISTS` 404）。

---

## 4. 乾淨網址（rewrite）設定

正式競賽環境要求網址為 `http://cXX.web/XX_Module_D/api/user/login`（不含 `index.php`）。
本機的 `nginx.conf` 未修改，因此改用等效的 PATH_INFO 形式。若要啟用乾淨網址，
將下列設定片段加入 `nginx.conf` 中 `listen 83` 的 server 區塊即可，
程式端（`api/lib/Request.php`）已同時支援兩種形式，不需要改動任何程式碼：

```nginx
# 第51屆全國賽 模組 D - 房屋交易平台 API 乾淨網址
location ^~ /51th/senior/51national/moduled/api/ {
    # 不存在的實體檔案一律交給 api/index.php 處理
    try_files $uri /51th/senior/51national/moduled/api/index.php$is_args$args;

    location ~ \.php(/|$) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        fastcgi_param  PATH_INFO        $fastcgi_path_info;
        include        fastcgi_params;
    }
}
```

若使用 Apache，等效的 `.htaccess`（放在 `api/` 目錄下）為：

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [QSA,L]
```

啟用後只要把 `initialize.js` 中的 `API_BASE` 由 `api/index.php` 改成 `api` 即可。

---

## 5. 前端頁面

| 檔案 | 說明 | 可存取身分 |
| --- | --- | --- |
| `index.html` | 首頁：搜尋表單、搜尋 / 排序條件、升降冪切換器、分頁切換器、房屋列表 | 全部 |
| `house.html?id=` | 查看房屋：詳細資訊與圖片輪播器 | 全部 |
| `signin.html` | 登入 | 訪客 |
| `signup.html` | 註冊 | 訪客 |
| `publish.html` | 刊登列表：刊登按鈕、編輯 / 刪除 / 申請精選 / 取消申請 | 會員、管理員 |
| `newhouse.html[?id=]` | 刊登 / 編輯房屋，可調整圖片順序與指定封面 | 會員、管理員 |
| `application.html` | 申請列表：依審核狀態搜尋、同意 / 拒絕 | 管理員 |
| `ads.html` | 精選房屋列表：顯示到期日、可下架 | 管理員 |

導覽列會依照登入身分顯示對應連結，全站沒有引用任何外部 CDN 資源。

---

## 6. 檔案結構

```
moduled/
├── !SQL/
│   ├── schema.sql              建表與測試資料（6 個帳號、24 間房屋、圖片、申請、精選）
│   └── generate_images.php     產生 uploads/ 內的房屋示意圖
├── !TEST/
│   ├── api-test.sh             API 自動測試腳本（curl，64 個案例）
│   └── session.html            截圖驗證用的登入輔助頁
├── api/
│   ├── index.php               API 單一入口與路由
│   ├── config.php              資料庫與系統設定
│   └── lib/
│       ├── ApiException.php    錯誤例外（訊息代碼 + 狀態碼）
│       ├── Response.php        JSON 回應
│       ├── Database.php        PDO 連線（prepared statement）
│       ├── Request.php         請求解析（JSON / form / multipart）
│       ├── MultipartParser.php PUT 的 multipart 解析
│       ├── Validator.php       欄位驗證
│       ├── Auth.php            Token 驗證與權限
│       ├── ImageService.php    圖片上傳與刪除
│       ├── HouseRepository.php 房屋查詢條件、排序、分頁
│       ├── UserController.php        API 1 ~ 3
│       ├── HouseController.php       API 4 ~ 9
│       ├── ApplicationController.php API 10 ~ 13
│       └── AdController.php          API 14 ~ 15
├── uploads/                    房屋圖片
├── index.css                   全站樣式
├── initialize.js               共用程式（API 呼叫、導覽列、搜尋、分頁、卡片）
└── *.html / *.js               各頁面
```

---

## 7. 測試

```bash
cd C:/nginx/skill/51th/senior/51national/moduled
bash "!TEST/api-test.sh"
```

腳本會重新匯入測試資料，再以 `curl` 逐一驗證 15 支 API 的成功情境與所有錯誤情境，
最後輸出通過 / 失敗統計。最近一次執行結果為 **64 項全數通過**。

---

## 8. 安全性

- 全部 SQL 皆使用 PDO prepared statement；排序欄位與方向以白名單對應，不由使用者字串拼接。
- 前端輸出一律經過 `escapeHtml()` 跳脫，避免 XSS。
- 密碼以 bcrypt 儲存（`password_hash` / `password_verify`）。
- 上傳圖片以 `getimagesize()` 檢查實際內容，非圖片檔回傳 `MSG_IMAGE_CAN_NOT_PROCESS`。

# WorldSkills 2019 TP17 – Web Technologies「PHP and JS」第一階段（Task C）

主辦者後台（Server Side Rendered，PHP + PDO/MySQL）與參加者 REST API。
對應題目 `WSC2019_TP17_PHP_and_JS_actual.pdf` 的 **Phase one – PHP**（規格 A1 ~ A7、B1 ~ B4）。

網址：<http://127.0.0.1:83/worldskill/2019/TaskC/>

---

## 一、環境與安裝

| 項目 | 內容 |
| --- | --- |
| PHP | 8.3.7（需 PDO、pdo_mysql） |
| MySQL | 127.0.0.1:3306，帳號 `root`，密碼空白 |
| 資料庫 | `worldskill2019_taskc` |
| Web Server | nginx（站台根目錄 `C:\nginx\skill`，port 83） |

### 建立資料庫

```sql
CREATE DATABASE worldskill2019_taskc CHARACTER SET utf8mb4;
```

依序匯入 `!SQL/` 內的兩個檔案：

1. `!SQL/db-dump.sql` — 主辦單位提供的原始 dump（**未做任何修改**）
2. `!SQL/schema.sql` — 本專案新增的資料表與初始資料

另外附上：

* `!SQL/database.sql` — 匯入完成後的完整資料庫傾印（最終交付用）
* `!SQL/ERD.svg` — 資料庫實體關聯圖

連線設定寫在 `inc/config.php` 的 `DB_*` 常數。

### 登入帳號（題目指定）

| Email | 密碼 |
| --- | --- |
| demo1@worldskills.org | demopass1 |
| demo2@worldskills.org | demopass2 |

密碼以 `password_hash()`（bcrypt）儲存於 `organizers.password_hash`，登入時用 `password_verify()` 驗證。

### 關於「今天」的基準日期

題目資料集是 2019 年的情境，若直接使用系統當下日期，所有活動都會變成過去式，
「即將舉行的活動」與「Early Bird 票效期」都無法呈現。
因此 `inc/config.php` 提供常數：

```php
const REFERENCE_DATE = '2019-09-01';   // 設為 null 即改用系統真實日期
```

此常數同時影響 `GET /events` 的 upcoming 篩選、票券 date 規則的判斷，以及新報名的時間戳記。

---

## 二、目錄結構

```
TaskC/
├── index.php                 A1  主辦者登入頁（GET 顯示表單 / POST 驗證）
├── logout.php                A1c 登出
├── inc/
│   ├── config.php            PDO 連線、共用查詢函式、跳脫與轉址工具
│   ├── auth.php              Session、登入檢查、多租戶存取控制、flash 訊息
│   ├── model.php             票券效期規則、活動關聯查詢、房間衝突檢查等領域邏輯
│   └── layout.php            後台共用版型（沿用官方模板的 class / id）
├── events/
│   ├── index.php             A2a 活動列表（依日期排序、顯示報名總數）
│   ├── create.php            A2b~A2e 新增活動（含 slug 驗證）
│   ├── edit.php              A2f~A2g 編輯活動
│   └── detail.php            A2h 活動詳細（票券／議程／頻道／房間）
├── tickets/create.php        A3  新增票券（含 date / amount 效期規則）
├── sessions/
│   ├── create.php            A4a~A4c 新增議程（房間時段衝突檢查）
│   ├── edit.php              A4d~A4e 編輯議程
│   └── _form.php             議程表單欄位（新增／編輯共用）
├── channels/create.php       A5  新增頻道
├── rooms/create.php          A6  新增房間
├── reports/index.php         A7  房間使用率長條圖（Chart.js 2.8.0）
├── api/v1/index.php          B1~B4 參加者 REST API（單一入口路由）
├── assets/                   bootstrap.css、custom.css、Chart.bundle.min.js
├── diagram_libs/             官方提供的 chart.js / d3.js 壓縮檔
├── _templates_original/      官方提供的靜態 HTML 模板（原封保留，供對照用）
└── !SQL/                     資料庫檔案與 ERD
```

---

## 三、REST API

### 網址形式

規格要求 `/api/v1/...` 的乾淨網址。因為**不修改 nginx.conf**，實際採用等效的 PATH_INFO 形式
（port 83 的 server 區塊已設定 `location ~ \.php(/|$)` 與 `fastcgi_split_path_info`）：

```
http://127.0.0.1:83/worldskill/2019/TaskC/api/v1/index.php/events
```

若要啟用規格中的乾淨網址，在 nginx 的 port 83 server 區塊加入下列片段即可
（`api/v1/index.php` 也已支援從 `REQUEST_URI` 還原路徑，因此不需要改動 PHP）：

```nginx
location ^~ /worldskill/2019/TaskC/api/v1/ {
    try_files $uri /worldskill/2019/TaskC/api/v1/index.php$is_args$args;

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

### 端點一覽

| 規格 | 方法 | 路徑 | 說明 |
| --- | --- | --- | --- |
| B1a | GET | `/events` | 所有即將舉行的活動，依日期由小到大 |
| B2a | GET | `/organizers/{organizer-slug}/events/{event-slug}` | 活動完整資料（頻道→房間→議程、票券） |
| B3a | POST | `/login` | 以 `lastname` + `registration_code` 登入，回傳 `md5(username)` 作為 token |
| B3b | POST | `/logout?token=…` | 使 token 失效 |
| B4a | POST | `/organizers/{o}/events/{e}/registration?token=…` | 報名活動並購票，`session_ids` 選填 |
| B4b | GET | `/registrations?token=…` | 目前使用者的報名紀錄，依 id 由小到大 |

POST 同時支援 **JSON** 與 **FormData / x-www-form-urlencoded** 兩種請求內容。

### 票券描述與可購買判斷

`event_tickets.special_validity` 儲存 JSON，格式與提供的 dump 一致：

| 規則 | 儲存內容 | `description` | `available` |
| --- | --- | --- | --- |
| 無 | `NULL` | `null` | 永遠 `true` |
| 日期 | `{"type":"date","date":"2019-06-01"}` | `Available until June 1, 2019` | 基準日期 ≤ 指定日期 |
| 數量 | `{"type":"amount","amount":50}` | `50 tickets available` | 已售出數 < 上限 |

### curl 驗證範例

```bash
BASE=http://127.0.0.1:83/worldskill/2019/TaskC/api/v1/index.php

curl -s "$BASE/events"
curl -s "$BASE/organizers/demo1/events/wsc-2019"
curl -s -o /dev/null -w '%{http_code}\n' "$BASE/organizers/nope/events/wsc-2019"   # 404 Organizer not found

TOKEN=$(curl -s -X POST -H 'Content-Type: application/json' \
        -d '{"lastname":"Yakovich","registration_code":"35DGZX"}' "$BASE/login" \
        | php -r 'echo json_decode(file_get_contents("php://stdin"), true)["token"];')

curl -s -X POST -H 'Content-Type: application/json' \
     -d '{"ticket_id":1,"session_ids":[4,6]}' \
     "$BASE/organizers/demo1/events/wsc-2019/registration?token=$TOKEN"

curl -s "$BASE/registrations?token=$TOKEN"
curl -s -X POST "$BASE/logout?token=$TOKEN"
```

---

## 四、資料庫擴充（第三正規化）

依規格新增兩張資料表，既有資料表完全未更動（除了題目要求更新的兩組主辦者密碼）：

* **`event_ratings`** — 參加者對「參加過的活動」評分（1~5 整數）與留言，並記錄評分時間
* **`session_ratings`** — 參加者對「參加過的議程」評分，規則同上

兩張表的非鍵欄位都完全相依於主鍵、且無遞移相依，並以
`(attendee_id, event_id)` / `(attendee_id, session_id)` 唯一鍵避免重複評分，符合 3NF。

---

## 五、安全性

* 所有 SQL 一律使用 **PDO prepared statement**（`inc/config.php` 的 `db_all()` / `db_one()` / `db_exec()`），
  且關閉模擬預處理（`ATTR_EMULATE_PREPARES => false`）
* 所有輸出經 `e()`（`htmlspecialchars`）跳脫，避免 XSS
* 密碼使用 bcrypt 雜湊，不以明碼比對
* **多租戶隔離**：所有活動相關頁面都經過 `require_own_event()`，
  非本主辦者的活動一律回應 404；房間／頻道／議程的下拉選單也只列出本活動的資料
* 登入後的頁面送出 `Cache-Control: no-store`，登出後按上一頁無法回到後台
* 登入成功時呼叫 `session_regenerate_id(true)` 防止 session fixation
* 前端資源（Bootstrap、Chart.js）全部放在本機 `assets/`，**沒有引用任何外部 CDN**

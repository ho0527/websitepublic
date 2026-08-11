# WorldSkills 2015 TP17 Web Design — Server Side B（Module H）

**Restaurant Service 訂位申請系統**

> 題目：`WSC2015_TP17_ServerSideB_actual.pdf`（Day 3 / Server Side B，3 小時）
> 網址：<http://127.0.0.1:83/worldskill/2015/moduleh/>

---

## 1. 快速開始

```bash
# 1) 建立資料庫（含結構與測試資料）
mysql --default-character-set=utf8mb4 -u root < "!SQL/schema.sql"

# 2) 直接以瀏覽器開啟模組目錄即可（nginx 的 index 已含 index.php）
#    http://127.0.0.1:83/worldskill/2015/moduleh/
```

資料庫連線設定在 `app/config.php`（預設 `127.0.0.1:3306`、`root`、空密碼、
資料庫名稱 `worldskill2015_moduleh`）。

> 匯入時務必加上 `--default-character-set=utf8mb4`，否則 `bistro/café` 這類
> 重音字元會在 MySQL CLI 端變成亂碼。`schema.sql` 開頭已加上 `SET NAMES utf8mb4;`。

---

## 2. 主要頁面

| 功能 | 網址 |
| --- | --- |
| 首頁（用餐體驗說明） | `/worldskill/2015/moduleh/` |
| 訂位聯絡人 + 賓客規範 | `/index.php/booking/contact` |
| 個人訂位 | `/index.php/booking/individual` |
| 團體訂位 | `/index.php/booking/group` |
| 送出確認 | `/index.php/booking/confirmation` |
| **訂位管理（WSI 工作人員）** | `/management/ReservationManagement.php` |
| 賓客名單（畫面） | `/management/GuestList.php` |
| 賓客名單（CSV） | `/management/GuestList.php?format=csv` |
| 資料庫關聯圖 | `/db-diagram.svg` |

訂位流程有前後相依性：未在「Booking contact」頁勾選同意賓客規範並通過驗證，
直接開啟 `/booking/individual` 或 `/booking/group` 會被導回第一步。

---

## 3. 路由與「乾淨網址」

依規定**沒有修改 `nginx.conf`**。路由使用兩種等效寫法，兩者都已實測可用：

1. `PATH_INFO`：`index.php/booking/individual`（預設，`Url::to()` 產生的形式）
2. 查詢字串：`index.php?r=booking/individual`

若日後要啟用乾淨網址（`/worldskill/2015/moduleh/booking/individual`），
把下列片段加進 `nginx.conf` 的 `server { }`，並將 `app/config.php` 的
`clean_urls` 改成 `true` 即可，程式不需其他改動：

```nginx
# 乾淨網址（clean URL）設定片段 —— 本專案未套用，僅供參考
location ^~ /worldskill/2015/moduleh/ {
    # 實體檔案（assets、management/*.php、db-diagram.svg …）優先
    try_files $uri $uri/ /worldskill/2015/moduleh/index.php$uri;

    location ~ \.php {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass   127.0.0.1:9000;
        include        fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        fastcgi_param  PATH_INFO        $fastcgi_path_info;
    }

    # 不對外開放的目錄
    location ~ ^/worldskill/2015/moduleh/(app|work|!SQL|dbdump|emails)/ {
        deny all;
    }
}
```

`.htaccess`（Apache 版本的等效設定）也一併附上，方便移植。

---

## 4. 目錄結構

```
moduleh/
├─ index.php                        前端控制器（唯一的公開 PHP 進入點）
├─ .htaccess                        Apache 乾淨網址設定（nginx 版見上方）
├─ db-diagram.svg                   資料庫關聯圖（題目要求的 db-diagram.xxx）
├─ README.md
├─ !SQL/
│   └─ schema.sql                   建表 + 設定資料 + 測試資料（可重建整個資料庫）
├─ dbdump/
│   └─ worldskill2015_moduleh.sql   mysqldump 產生的完整傾印
├─ app/
│   ├─ bootstrap.php                自動載入 / 資料庫 / session 啟動
│   ├─ config.php                   設定檔
│   ├─ Core/                        自製微框架
│   │   ├─ Database.php             PDO 單例，全部使用 prepared statement
│   │   ├─ Model.php  Controller.php  Router.php  Request.php
│   │   ├─ View.php                 樣板引擎 + View::e() XSS 跳脫
│   │   ├─ Url.php                  網址產生器（支援 PATH_INFO / 乾淨網址）
│   │   └─ Countries.php            靜態國家清單（規格要求寫在程式碼中）
│   ├─ Models/                      CompetitionDay / DiningModule / Seating /
│   │                               BookingContact / Booking / Reservation
│   ├─ Services/
│   │   ├─ BookingService.php       訂位商業邏輯（人數上限、候補判定、寫入）
│   │   └─ EmailService.php         Send emails（寫出 /emails 文字檔）
│   └─ Views/                       layout / home / contact / individual /
│                                   group / confirmation / management /
│                                   guestlist / error
├─ management/
│   ├─ ReservationManagement.php    題目指定的管理頁（必須在 /management 目錄）
│   └─ GuestList.php                Generate Guest List（畫面 + CSV）
├─ emails/                          「Send emails」產生的通知文字檔
├─ assets/                          官方樣板（Bootstrap 3 + WorldSkills 佈景）
│   ├─ dist/                        bootstrap.min.css / bootstrap.min.js /
│   │                               jquery.min.1.11.1.js / fonts …（全部本機檔案）
│   ├─ restaurantapp.css  restaurantapp.js  6215177259.jpg
└─ work/                            工作檔（不列入評分）
    ├─ make-db-diagram.py           產生 db-diagram.svg 的小工具
    └─ preview-group.html           團體訂位驗證錯誤畫面的截圖用快照
```

---

## 5. 需求對應說明

### 資料設定（H9）
競賽日與場次資訊**全部存在資料庫**，沒有寫死在程式裡：

* `competition_day`：`C1 – 04.08.2015` … `C4 – 07.08.2015`
* `dining_module`：模組名稱與首頁顯示的說明文字
* `seating`：場次名稱、桌型設定（`1 table of 4 and 1 table of 2`）、
  起訖時間、每位選手服務座位數 `seats_per_competitor`、選手人數 `competitor_count`

由此推導：

* **總座位數** = `seats_per_competitor × competitor_count`
  （Casual/Bar/Banquet = 6 × 6 = 36，Fine Dining = 4 × 6 = 24）
* **單一國家上限** = 總座位數 − `seats_per_competitor`
  （Casual = 30，Fine Dining = 20），確保賓客不會坐在同國選手服務的桌上

國家清單依規格「static and provided in the HTML code」，放在
`app/Core/Countries.php`，不進資料庫。

### 剩餘座位與候補
* 剩餘座位 = 總座位數 − （`confirmed` + `requested` 筆數），於頁面載入當下計算
* 系統**永遠接受**四個競賽日的訂位申請（含過去日期）
* 送出時若該場次已無剩餘座位，該筆自動存成 `waitlisted`，
  確認頁會另外提醒賓客已被排入候補名單

### 團體訂位驗證
送出後於**伺服器端**統計「每個場次 × 每個國家」的人數（已存在的非 declined 筆數 + 本次送出），
超過上限時整頁重新顯示、**保留所有已輸入資料**、把出錯的競賽日分頁自動切成 active，
並顯示 `Too many guests / from country XX for … / Please edit your booking request.`。
只有選了國家的列才會被寫入資料庫（未選國家的列直接忽略）。

### 訂位管理
* 排序：競賽日 → 場次 → 狀態（1. confirmed 2. requested 3. waitlisted 4. declined）→ 訂位編號
* 每個「競賽日 + 場次」重新編號（賓客姓名前的流水號）
* 單選鈕**只在 `requested` 狀態顯示**；伺服器端也會再次確認狀態，
  避免被偽造的表單改到已定案的資料
* 勾選 `Reschedule` 並儲存後，該列的「Day / Seating」會變成下拉選單；
  再選擇新的日期與場次並設為 `Confirm` 儲存，即完成改期
* 訂位編號欄位的 `title` 會顯示聯絡人資訊（姓名、單位、電話、Email、國家）

### Send emails
不真的寄信，改為在 `emails/` 產生文字檔（規格指定的競賽做法），
內容依「競賽日 + 場次」分組列出每位賓客的狀態。
重複按下按鈕會重複產生；但若某位聯絡人的**所有**賓客都已是 `confirmed` 或 `declined`
且先前已通知過，就會被略過（`booking_contact.notified_at`）。

### 賓客名單匯出
`Generate Guest List` → `/management/GuestList.php`：
只顯示 `confirmed`，依「競賽日 → 場次」分組、組內依訂位編號排序，
欄位為 Booking No / Booking Contact Name / Booking Contact Organization /
Guest Name / Guest Country；另可下載 CSV（含 UTF-8 BOM）。

---

## 6. 關於「必須使用 Laravel 或 Yii」

題目要求使用當年（2015）預先安裝的 Laravel 或 Yii。**本機沒有提供這兩套框架**
（`WSC2015_TP17_resources_serverside` 內只有 PHP 手冊與樣板，沒有框架檔案），
而且機器離線、沒有 Composer 套件快取可用；即使取得 2015 年版本，
Laravel 5.1 / Yii 2.0.x（當年版本）也**無法在 PHP 8.3.7 上執行**
（大量 PHP 7 之前的語法與已移除的函式）。

替代做法：自行實作一套小型 MVC 微框架（`app/Core/`），
提供與框架相同的分層與安全預設值：

* Front controller + Router（路由表 → 控制器/方法）
* PSR-4 風格自動載入（`spl_autoload_register`）
* PDO 單例 + **全部 prepared statement**（`ATTR_EMULATE_PREPARES = false`）
* Model / Service / Controller / View 分層，樣板引擎統一以 `View::e()` 做 XSS 跳脫
* Request 物件封裝輸入與 session

官方提供的**前端樣板則完全沿用**（Bootstrap 3 + WorldSkills 佈景、`restaurantapp.css`、
`restaurantapp.js`），只把靜態內容換成動態輸出，外觀維持原樣。
`restaurantapp.js` 僅做一處必要修改：動態新增賓客欄位時，
國家清單改為複製頁面上的 `#guest-country-template`，以便與伺服器端的靜態清單一致。

**沒有引用任何需要連外的 CDN**，CSS / JS / 字型全部是本機檔案。

---

## 7. 測試結果

測試環境：nginx 1.27.0 + PHP 8.3.7（FastCGI 127.0.0.1:9000）+ MariaDB 10.4.28。
`php -l` 對全部 PHP 檔案皆通過。

| # | 測試項目 | 方式 | 結果 |
| --- | --- | --- | --- |
| 1 | 首頁 / 各頁面 HTTP 狀態 | `curl -o /dev/null -w '%{http_code}'` | 首頁、contact、management、GuestList、CSV 皆 `200`；不存在的路由 `404` |
| 2 | 兩種路由寫法等效 | `index.php/booking/contact` vs `index.php?r=booking/contact` | 皆 `200`，輸出相同 |
| 3 | 聯絡人欄位驗證 | POST `email=bad` 且未勾同意 | 回傳表單並顯示「Email must match the pattern xxx@yyy.zzz.」「You must accept the guest regulations…」 |
| 4 | 同意後導向 | POST 正確資料 + `agree-individual` | `302 → /index.php/booking/individual` |
| 5 | 剩餘座位動態計算 | 個人訂位頁 | Casual C2 顯示 32（36 − 2 confirmed − 2 requested）、Bar C2 顯示 31、Bar C4 顯示 33、Fine Dining 24 — 與資料庫一致 |
| 6 | 個人可選多場次、姓名國家沿用聯絡人 | 勾選 C1+C2 Casual 送出 | 產生 `201500004`，確認頁列出兩個場次，賓客皆為 `Sarah Rogers US` |
| 7 | 團體國家人數上限 | C1 Fine Dining 送出 21 位 AU（上限 20） | 顯示 `Too many guests / from country AU for Fine Dining on C1 - 04.08.2015, 13:00 - 15:15 (maximum 20 …)`，21 組姓名與國家全部保留，HTTP `200`（未寫入資料庫） |
| 8 | 未選國家的列被忽略 | 送出 20 筆有國家 + 1 筆 `NoCountry` 無國家 | 資料庫中 `guest_name='NoCountry'` 筆數 = 0 |
| 9 | 額滿自動候補 | Fine Dining C1 已用 20/24，再送 6 人 | 前 4 筆 `requested`、後 2 筆 `waitlisted`，確認頁出現候補提醒 |
| 10 | 管理頁排序與編號 | 截圖 | 依 日 → 場次 → confirmed/requested/waitlisted/declined → 訂位編號；每個場次由 1 重新編號 |
| 11 | 單選鈕只在 requested 顯示 | 截圖 | confirmed / waitlisted / declined 三列的 Action 欄為空白 |
| 12 | 確認 / 婉拒 儲存 | POST `action[1]=confirm&action[4]=decline` | 資料庫狀態正確更新為 `confirmed` / `declined` |
| 13 | 偽造表單防護 | 對 `declined` 的項目 POST `action[5]=confirm` | 被忽略，狀態仍為 `declined` |
| 14 | 改期流程 | `action[13]=reschedule` → 重新載入 → `reschedule_day[13]=4&reschedule_seating[13]=5&action[13]=confirm` | 儲存後頁面出現 `reschedule_day[13]` / `reschedule_seating[13]` 下拉；再次儲存後該筆變成 `C4 / Banquet Dining`、狀態 `confirmed`、`needs_reschedule=0` |
| 15 | Send emails | 連按兩次 | 第一次寫出 6 個檔案；第二次寫出 5 個、略過 1 個（該聯絡人所有賓客均已 confirmed/declined） |
| 16 | 通知檔內容 | 檢視文字檔 | 依競賽日 + 場次分組，逐位賓客列出狀態與訂位編號 |
| 17 | 賓客名單 CSV | `GuestList.php?format=csv` | 欄位齊全，依 日 → 場次 → 訂位編號排序，僅 `confirmed` |
| 18 | 版面一致性 | headless Chrome 截圖（首頁 / 團體訂位 / 管理頁 / 關聯圖） | 與官方樣板外觀一致，無外連資源、主控台無 404 |

---

## 8. 已知限制 / 未完成事項

* **沒有使用 Laravel / Yii**，原因與替代做法見第 6 節（H8「Working with Libraries」
  的「Use of framework」項目會受影響；「Integration of template」則完全沿用官方樣板）。
* 管理區**沒有登入機制**。題目明確寫出「protecting this directory e.g. by .htaccess is
  not required and not part of the competition」，因此僅以獨立目錄區隔，未實作驗證。
* 「剩餘座位」把 `confirmed` 與 `requested` 都視為已佔位。規格對這點的描述
  （"exceed already confirmed seatings can be waitlisted"）可有不同解讀；
  若要改成只計 `confirmed`，只需修改 `Reservation::occupancyMap()` 的 `WHERE` 條件。
* 管理頁每個場次的流水號是**依畫面排序**重新編號（與官方樣板一致）。
  題目示意圖中的編號順序看起來另有規則（同一場次內先出現 1、之後才是 15/16/17），
  規格文字未定義，故採用樣板的做法。
* 團體訂位頁的「+ Add guest」沿用官方樣板的 jQuery，沒有提供移除單列的按鈕
  （樣板本身也沒有）；未填國家的列送出時會被忽略，等同於取消該列。
* 沒有實作真正的寄信（依規格改為輸出文字檔）。

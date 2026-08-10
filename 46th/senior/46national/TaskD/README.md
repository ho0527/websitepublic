# 模組 D — 列車訂票系統

第 46 屆全國技能競賽暨第 44 屆國際技能競賽國手選拔賽 · 17 網頁設計

以原生 PHP 8 實作的 MVC 架構網站，含自製輕量 ORM，不依賴任何外部套件。

## 目錄結構

```
TaskD/
├── index.php                   單一入口（Front Controller）
├── .htaccess                   Apache 環境的網址重寫設定
├── db01_module_D_db_schema.sql 匯出的資料庫（結構 + 註解 + 索引 + 外鍵 + 測試資料）
├── config/
│   └── config.php              資料庫連線、分頁筆數、驗證碼等設定
├── routes/
│   └── web.php                 路由定義
├── app/
│   ├── Core/                   框架核心
│   │   ├── Autoloader.php      PSR-4 自動載入
│   │   ├── Config.php          設定存取
│   │   ├── Database.php        PDO 連線（一律使用預備語句）
│   │   ├── QueryBuilder.php    查詢建構器（參數綁定）
│   │   ├── Model.php           ORM 基底（Active Record）
│   │   ├── Controller.php      控制器基底
│   │   ├── Router.php          路由器
│   │   ├── Request.php         請求封裝
│   │   ├── View.php            樣板渲染（輸出一律跳脫）
│   │   ├── Session.php         Session 與快閃訊息
│   │   ├── Auth.php            後台身分驗證
│   │   ├── Paginator.php       分頁
│   │   └── ServiceContainer.php 服務容器
│   ├── Models/                 每張資料表對應一個模型
│   │   ├── Station.php  TrainType.php  Train.php  TrainStop.php
│   │   ├── TrainServiceDay.php  Booking.php  AdminUser.php
│   │   └── CaptchaQuestion.php  CaptchaAnswerRegion.php
│   ├── Services/               商業邏輯
│   │   ├── ScheduleService.php      時刻與票價計算
│   │   ├── SeatService.php          區間座位計算
│   │   ├── TrainLookupService.php   車次查詢
│   │   ├── BookingService.php       訂票流程與檢核
│   │   ├── BookingCodeGenerator.php 訂票編號產生
│   │   ├── CaptchaService.php       問答驗證碼
│   │   ├── SmsService.php           模擬簡訊
│   │   ├── StatisticsService.php    搭乘人數統計與開放資料
│   │   └── TrainRemovalService.php  刪除列車與連帶取消訂票
│   └── Controllers/            前台與後台控制器
├── resources/views/            樣板（layouts / partials / front / admin / errors）
├── assets/                     css、js、圖片、驗證碼題目圖
├── material/captcha/           驗證碼題目敘述與參考答案（不對外提供）
├── database/
│   ├── schema.sql              建立資料庫的原始腳本
│   └── seed-bookings.php       產生示範用的歷史訂票紀錄
└── SMS/                        模擬簡訊輸出（手機號碼.txt）
```

## 安裝

### 1. 建立資料庫

```bash
mysql -u root < database/schema.sql
```

資料庫名稱為 `46_national_moduled`，連線設定可於 `config/config.php` 調整。

### 2. 網址重寫設定

**nginx**（本機開發環境所使用，設定在 `C:\nginx\conf\nginx.conf` 的 :83 server 區塊）：

```nginx
location ^~ /46th/senior/46national/TaskD/ {
    try_files $uri $uri/ /46th/senior/46national/TaskD/index.php?route=$uri&$args;

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```

**Apache**：專案內已附 `.htaccess`，只需啟用 `mod_rewrite` 並允許 `AllowOverride All`。

### 3. 產生示範統計資料（選用）

「資料統計及開放資料」只統計昨天（含）以前的訂票，需要歷史資料才看得到圖表內容：

```bash
php database/seed-bookings.php 7
```

## 使用

| 功能 | 網址 |
| --- | --- |
| 首頁（車次查詢） | `/` |
| 車次查詢結果（SEO 網址） | `/train-lookup/{日期}/{起程站}/{到達站}/{車種}` |
| 列車資訊（SEO 網址） | `/train-info/{車次代碼}` |
| 預訂車票 | `/booking` |
| 訂票查詢 | `/bookings` |
| 搭乘人數統計 | `/statistics` |
| 開放資料 JSON | `/statistics/export.json` |
| 後台登入 | `/login` |

後台內建帳號：`admin` / `1234`

## 實作說明

### 資料庫

- 每張資料表與每個欄位都有中文註解，並建立適當的索引與外鍵
- 訂票編號欄位使用 `utf8mb4_bin` 定序，使大小寫視為不同字元
- 列車採軟刪除（`deleted_at`），保留歷史訂票與統計資料的完整性

### 時刻與票價

`train` 只儲存發車站的發車時間，`train_stop` 儲存各站的行駛分鐘數、停留分鐘數與自發車站起算的累計票價。
各站實際時刻與區間票價一律由 `ScheduleService` 推算，時刻表只有單一資料來源。

### 區間座位

一張車票只佔用起訖站之間的座位。`SeatService` 把路線拆成相鄰區間分別累計已售張數，
所要訂的區間中「最壅塞的一段」即為限制。例如載客 100 人的列車經過 A-B-C-D，
已有 90 人購買 A→C 時，B→C 只剩 10 席，但 C→D 仍可訂滿 100 張。

### 問答驗證碼

作答視窗直接覆蓋在訂票頁上，不另開新頁面。點擊圖片會以游標為中心畫出框線 5px 的紅色矩形；
換題會清除目前選取；驗證時把座標送回伺服器與 `captcha_answer_region` 比對，
必須每個目標物件都剛好被選到一次、且沒有多選才算通過，未通過會顯示錯誤訊息並自動換題。

> 競賽原始素材（`01.jpg`~`06.jpg`、`Questions.txt`、`01 - Marked.jpg`~`06 - Marked.jpg`）
> 不在本機磁碟上，因此改以等效的示意圖重新製作，題目圖片放在 `assets/captcha/`，
> 題目敘述與參考答案放在 `material/captcha/`。

### 佈景主題

網站**預設為深色**，右上角的按鈕可切換為淺色，選擇會存進 `localStorage`，換頁與重新整理後仍保留。

- 所有顏色都以 CSS 自訂屬性定義在 `assets/css/app.css` 最上方的兩個區塊
  （`:root` 為深色、`:root[data-theme="light"]` 為淺色），其餘樣式一律引用變數，不寫死顏色
- `<head>` 內嵌 `partials/theme-boot.php`，在頁面繪製前就套用佈景，避免載入時閃爍
- LOGO 改以行內 SVG 呈現（`partials/logo.php`），文字才能跟著佈景換色
- 統計長條圖的顏色以 CSS class 指定（`.bar-entrance` / `.bar-exit`），同樣會跟著切換
- `color-scheme` 一併設定，讓日期／時間選擇器等瀏覽器原生元件也套用對應佈景

若要改成「跟隨系統設定」而非固定預設深色，只需在 `partials/theme-boot.php` 中，
於沒有存過偏好時改為讀取 `window.matchMedia('(prefers-color-scheme: light)')` 的結果。

### 安全性

- **SQL Injection**：所有資料庫操作都經由 ORM 與 `QueryBuilder`，值一律以 `?` 佔位符綁定；
  識別字以反引號包覆並移除反引號，比較運算子採白名單
- **XSS**：樣板中所有動態內容都以 `View::e()` 輸出，把 HTML 特殊字元轉為實體
- **後台存取控管**：未登入時無法存取任何後台功能，會導回登入頁並顯示錯誤訊息
- 密碼以 `password_hash()` 儲存，登入成功後重新產生 Session ID

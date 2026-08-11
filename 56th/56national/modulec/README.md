# 模組 C － 應用系統．行動應用（南港展覽館服務）

第 56 屆全國技能競賽　17 網頁技術

## 這是什麼

題目名稱雖然是「行動裝置開發」，但規格書內容明確要求的是**行動網頁應用程式**
（`manifest.json`、meta 標籤、Chrome Lighthouse 無障礙評分、前端 + 後端 + 資料庫、後台管理），
並且明文禁止使用 Node.js / Laravel / Vue / React 等框架。
因此本專案以**原生 HTML + CSS + JavaScript（前端）** 與 **原生 PHP + MySQL（後端）** 實作，
沒有引用任何需要連外的 CDN 或套件。

## 執行方式

1. **建立資料庫（第一次執行必做）**
   瀏覽 <http://127.0.0.1:83/56th/56national/modulec/api/install.php>
   會自動建立資料庫 `56national_modulec`、三張資料表並匯入種子資料。
   加上 `?reset=1` 會先清空再重新匯入。
2. **前台**：<http://127.0.0.1:83/56th/56national/modulec/>
3. **後台管理**：<http://127.0.0.1:83/56th/56national/modulec/admin/>

連線設定寫在 `api/db.php`（預設 `127.0.0.1:3306`、帳號 `root`、密碼空白）。

## 檔案結構

```
modulec/
├─ index.html          前台版面（頁首欄 / 主內容 / 底部導覽列）
├─ index.css           前台樣式（含淺色 / 深色 / 跟隨系統三種主題）
├─ index.js            前台邏輯（全部資料皆來自 api/）
├─ manifest.json       Android 行動網頁應用程式設定
├─ icon.svg            應用程式圖示
├─ svn icons/          題目提供的天氣 SVG 素材（圖形路徑已內嵌於 index.js）
├─ api/
│  ├─ db.php           PDO 連線與共用函式
│  ├─ install.php      建立資料庫、資料表與種子資料
│  ├─ parking.php      停車場清單 / 單筆查詢
│  ├─ events.php       活動查詢（日期篩選 + 分頁）
│  └─ weather.php      未來 7 天天氣
└─ admin/
   ├─ index.php        後台管理（三張資料表的 CRUD 與特殊功能）
   └─ admin.css        後台樣式
```

## 資料庫結構

| 資料表 | 主要欄位 |
| --- | --- |
| `parking_lots` | `id`, `name`, `address`, `total_spaces`, `available_spaces`, `latitude`, `longitude`, `updated_at` |
| `events` | `id`, `title`, `description`, `start_date`, `end_date`, `image_color`, `image_url` |
| `weather` | `id`, `forecast_date`(唯一), `condition_text`, `icon`, `high_temp`, `low_temp`, `rain_chance` |

競賽當天若主辦提供指定的資料表結構，只要調整 `api/*.php` 中的欄位對應即可，前端不需更動。

## API

| 端點 | 參數 | 說明 |
| --- | --- | --- |
| `GET api/parking.php` | － | 全部停車場 |
| `GET api/parking.php?id=1` | `id` | 單一停車場（詳情頁每 10 秒輪詢） |
| `GET api/events.php` | `start`, `end`, `offset`, `limit` | 活動查詢；回傳 `data`, `total`, `offset`, `limit`, `has_more` |
| `GET api/weather.php` | － | 自今日起連續 7 天 |

活動的日期篩選採「區間重疊」判斷：`end_date >= start` 且 `start_date <= end`。
排序固定為 `start_date ASC, id ASC`，確保分頁穩定，不會重複或遺漏記錄。

## 功能對照

### 版面
- 頁首欄 `position: fixed` 於頂端，顯示目前檢視標題、返回按鈕與目前經緯度。
- 導覽列 `position: fixed` 於底部，四個按鈕：停車場、活動、天氣、設定。
- 只有 `main.content` 可捲動，頁首與導覽列不動。
- 版面以 `max-width: 520px` 置中，並使用 `env(safe-area-inset-*)` 處理瀏海與底部安全區。

### 停車場
- 從 `api/parking.php` 取得清單，顯示名稱、距離、可用／總車位與空位狀態標籤。
- **排序切換**位於「設定」檢視：按字母排序 ⇄ 按目前位置距離排序。
- **置頂**：每列右側的星號按鈕；置頂清單存進 `localStorage`（鍵名 `modulec-pinned`），重新整理後保留，且不論排序方式都排在最前面。
- **詳情**：點擊任一停車場進入詳情頁，顯示名稱、地址、距離、可用車位、總車位與更新時間；此頁每 **10 秒**向後端重新取得最新資料，頁首欄的返回按鈕可回到清單。

### 地理位置
- 優先讀取網址查詢參數，例如
  `?latitude=45.755051&longitude=4.846358`，並於頁首欄與設定頁**顯示該經緯度**。
- 沒有查詢參數時改用 `navigator.geolocation`（可搭配 Chrome DevTools 的 Sensors 模擬）。
- 兩者都失敗則退回預設座標（南港展覽館 25.0561, 121.6178）。
- 距離計算使用 Haversine 公式（`distanceBetween()`），地球半徑 6371 公里。

### 活動
- 提供「開始日期」「結束日期」兩個 `input[type=date]`，任一改變即重新查詢。
- **無限捲動**以 `IntersectionObserver` 監看清單底部哨兵元素，`rootMargin: 240px`，在使用者捲到底之前就先取下一頁；另外保留 scroll 事件作為備援。
- 以 `Set` 記錄已載入的 `id`，確保不會出現重複記錄；後端以固定排序 + `LIMIT/OFFSET` 分頁，確保不會遺漏。
- 若首次載入後清單還填不滿可視範圍，會自動再補一頁，避免無法觸發捲動。
- 活動圖片：資料表有 `image_url` 就用它，否則以該筆的 `image_color` 即時產生一張 SVG 佔位圖。

### 天氣
- 顯示未來 7 天，水平排列。
- `scroll-snap-type: x mandatory` + `scroll-snap-align: center` + `scroll-snap-stop: always`，捲動時會對齊到某一天。
- 每天一個 SVG 圖示（晴 / 多雲 / 雨），圖形路徑取自題目素材 `svn icons/*.svg`。
- 依規格：`fill: none`、`stroke: #1c3e60`、`stroke-width: 1`；
  滑鼠懸停時觸發 2 秒的描邊動畫，`stroke-dasharray` 由 50 到 200、`stroke-dashoffset` 由 200 到 0。

### 設定
- 佈景主題：淺色 / 深色 / 跟隨系統，儲存於 `localStorage`（鍵名 `modulec-theme`）。
  「跟隨系統」透過 `@media (prefers-color-scheme: dark)` 生效，系統設定變更時會即時反應。
- 停車場排序方式切換。
- 目前定位資訊與重新定位按鈕。
- 後台管理入口。

### 後台管理
`admin/index.php` 提供三張資料表的新增／編輯／刪除，另有各自的「特殊功能」：

| 資料表 | 特殊功能 |
| --- | --- |
| 停車場 | 隨機模擬即時空位（驗證詳情頁 10 秒自動更新）、全部設為滿場 |
| 活動 | 複製活動並自動順延一年、批次清除過期活動 |
| 天氣 | 將整份預報平移至今日起算 |

所有 SQL 皆使用 PDO 預備語句，輸出一律經 `htmlspecialchars()` 逸出。

## 無障礙
- `lang="zh-Hant"`、完整的 `meta description`、跳至主要內容連結。
- 所有按鈕都有可讀名稱（文字或 `aria-label`），圖示 SVG 以 `aria-hidden` 排除。
- 清單使用 `ul`/`li`，詳情使用 `dl`/`dt`/`dd`，設定使用 `fieldset`/`legend`。
- `aria-live` 狀態提示、`aria-current` 標示目前分頁、`:focus-visible` 明顯外框。
- 支援 `prefers-reduced-motion`。

## 與原題目的差異／已知限制
- 題目類別名稱為「行動裝置開發」，但本機無 Android Studio 等原生開發環境，且規格書內容本身就是行動網頁（manifest + Lighthouse + 禁用前後端框架），因此交付的是**可安裝為 PWA 的行動版網頁**，非原生 App。
- 未註冊 Service Worker（題目未要求離線能力，且離線快取會干擾評分時的資料更換）。
- 種子資料為自製的合理範例；競賽時主辦會提供指定的資料表結構與天氣資料，屆時只需替換資料或調整 `api/` 的欄位對應。

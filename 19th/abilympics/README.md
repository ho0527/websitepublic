# 芬蘭極光旅遊資訊平台

第19屆全國身心障礙者技能競賽　A04 網頁設計　練習作品

## 環境與安裝

1. 匯入資料庫（三選一）
   - phpMyAdmin：選「匯入」上傳 `19thabilympics.sql`
   - 指令：`mysql -u root -p < 19thabilympics.sql`
   - PHP：`php -r "$db=new PDO('mysql:host=localhost;charset=utf8mb4','root','');$db->exec(file_get_contents('19thabilympics.sql'));"`

   SQL 檔內含 `CREATE DATABASE` 與 `DROP TABLE`，可重複執行重置資料。

2. 連線設定在 `initialize.php`：`localhost / root / 空密碼 / 19thabilympics`

3. 用瀏覽器開啟 `index.php`（本機 nginx 路徑為 `http://localhost:83/19th/abilympics/`）

4. 後台帳密：**admin / 1234**（存在 `admin` 資料表）

## 檔案結構

| 檔案 | 說明 |
|---|---|
| `index.php` | 首頁：主視覺、今晚推薦地點、最新日記 |
| `report.php` / `report.js` | 模組三：極光預報查詢 |
| `diary.php` / `diary.js` | 模組四：日記投稿與展示、極光祝福 |
| `signin.php` / `signin.js` | 登入（比對 `admin` 資料表） |
| `admin.php` / `admin.js` | 模組五：後台搜尋／篩選／排序／刪除 |
| `api.php` | 資料寫入：`newdiary`、`bless`、`deletediary` |
| `initialize.php` | 資料庫連線與共用函式 |
| `initialize.js` | localStorage 登入狀態、登出、手機版收合選單 |
| `index.css` | 全站樣式（含 RWD） |
| `block/` | `header.php`、`nav.php`、`footer.php` |
| `media/` | 圖片、圖示、Logo、假資料 |
| `19thabilympics.sql` | 資料庫建立與範例資料 |

## initialize.php 共用函式

| 函式 | 用途 |
|---|---|
| `query($db,$sql,$data)` | PDO 預處理查詢 |
| `maskemail($email)` | Email 遮蔽 → `a***@example.com` |
| `cutstr($str,$len)` | 長文字截斷加省略號 |
| `stars($rating)` | 輸出 1~5 星 |
| `recommendtag($r)` | 高／中／低色塊，空值顯示「無資料」 |
| `photo($path)` | 照片為空時改用佔位圖 |

## 資料表

| 資料表 | 筆數 | 說明 |
|---|---|---|
| `admin` | 1 | 管理者帳密 |
| `location` | 9 | 觀測地點 |
| `forecast` | 9 | 極光預報，`kemi` 一筆為 NULL 用於測試「無資料」 |
| `diary` | 12 | 旅人日記 |
| `blessing` | 1318 | 極光祝福，一次點擊一列（與 `wishlike` 相同做法） |

## 對應試題模組

- **模組一 Logo**：`block/header.php` 使用 `media/logo/logo-square-512.png`，另有 SVG 向量版與深色底版
- **模組二 框架 RWD**：`block/` 三個區塊 + `index.css`；≤790px 出現漢堡選單、日記卡片改直式、後台表格改堆疊卡片
- **模組三 預報**：下拉選單查詢、未選提示、無資料提示、長備註展開收合、高中低色塊
- **模組四 日記**：前後端雙重驗證、Email 遮蔽、心得截斷、祝福即時加一（fetch 不重整頁面）
- **模組五 後台**：未登入擋下、搜尋（暱稱／心得）、地點篩選、日期／評分／祝福排序、點 Email 列出該人所有投稿、分頁、刪除

## 已知事項

- 登入狀態沿用上一屆的 localStorage 做法（`admin.js` 擋未登入），帳密比對已改為向 `admin` 資料表查詢。
  若要更嚴謹可改用 `$_SESSION` 在 PHP 端擋。
- 頁面輸出未做 `htmlspecialchars`，與上一屆檔案的寫法一致。若要防 XSS 可在輸出日記欄位時加上。

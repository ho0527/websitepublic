# WorldSkills 2024 - Module D - Lyon Mobile Web Service

行動裝置網頁：里昂停車場空位、活動列表、一週天氣與設定。

## 執行方式

不需要編譯，也不需要資料庫，直接由 nginx + PHP 提供靜態檔與 API 即可。

- 頁面： <http://127.0.0.1:83/worldskill/2024/moduled/>
- 模擬地理位置： <http://127.0.0.1:83/worldskill/2024/moduled/?latitude=45.755051&longitude=4.846358>

## 檔案

| 檔案 | 說明 |
| --- | --- |
| `index.html` | 應用程式外框：頁首、五個檢視、底部導覽列，以及 Android / iOS 的 meta 設定 |
| `index.css` | 版面與深/淺色主題、天氣 SVG 描邊動畫 |
| `index.js` | 全部前端邏輯（apiClient / geolocation / carpark / event / weather / setting / viewRouter） |
| `manifest.json` | Android 行動網頁 App 設定 |
| `module_d_api.php` | 模擬資料 API 伺服器 |
| `material/image/` | 題目提供的活動示意圖 |
| `material/icon/` | App 圖示（192 / 512 / maskable） |

## 關於 API 伺服器

題目原本提供 `dl.worldskills.org/module_d_api_server.zip` 內的 `module_d_api.php`，
但本機為離線環境無法下載，因此依規格自行重建一份等效的模擬 API。

規格上的呼叫形式為 `module_d_api.php/carparks.json`，
但本機 nginx 的 `location ~ \.php$` 不會把 `.php` 之後的路徑交給 PHP 執行（會回 404），
因此改用等效的查詢字串形式，兩者回傳內容完全相同：

| 資源 | 網址 |
| --- | --- |
| 停車場 | `module_d_api.php?path=carparks.json` |
| 活動 | `module_d_api.php?path=events.json&page=2&beginning_date=YYYY-MM-DD&ending_date=YYYY-MM-DD` |
| 天氣 | `module_d_api.php?path=weather.json` |

`module_d_api.php` 本身同時支援 `PATH_INFO` 形式，
若把它放到有支援 `PATH_INFO` 的伺服器上，`module_d_api.php/events.json` 也可正常運作。

活動 API 每頁 8 筆、共 90 筆，回應中的 `pages.next` / `pages.prev` 供無限捲動使用。
停車場的空位數每 10 秒變動一次，方便驗證單一停車場檢視的自動更新。

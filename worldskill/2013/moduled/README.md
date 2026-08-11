# WorldSkills 2013 – Skill 17 Web Design / Module D（Introducing Leipzig）

線上位置：<http://127.0.0.1:83/worldskill/2013/moduled/>

> 註：網站索引把本模組標為「API」，但依 `WSC2013_TP17_Module_D_actual_EN.pdf`，
> Module D 實際是「以 CSS/JS 為既有 HTML 設計版面與多媒體整合」的題目，
> 本目錄依 PDF 規格實作。

## 題目重點

主辦方提供固定的 `index.html`（**不得修改**），選手只能提供
`css/website.css`、`js/website.js` 與額外圖片（放在 `extraimages/`），
為「Introducing Leipzig」展覽頁做出美觀且具互動性的版面。

## 檔案

| 路徑 | 說明 |
| --- | --- |
| `index.html` | 官方提供，**未做任何修改**（由 `material/Site Layout/index.html` 原樣複製） |
| `css/website.css` | 本次撰寫的樣式表（含繁體中文註解、列印樣式） |
| `js/website.js` | 本次撰寫的互動腳本（原生 JavaScript） |
| `js/jquery-2.0.1.js`、`js/mootools-core-1.4.5-full-nocompat.js` | 官方提供的函式庫，HTML 有引用故保留 |
| `images/`、`videos/`、`pdf/` | 官方提供素材，未修改 |
| `extraimages/diagram.jpg` | **自行產生**的統計圖（原檔只是藍底寫著 "Diagram" 的佔位圖） |
| `material/` | 原始素材備份 |

## 已實作的評分項目

* **標題與圖片風格**：深酒紅 + 金銅色的古典音樂調性，`h1`/`h2` 疊在滿版輪播照片上。
* **特色照片動畫**：四張 `photo1~4.jpg` 淡入淡出 + 緩慢放大（Ken Burns）。
  * 無 JavaScript 時由 CSS `@keyframes photo-cycle` 以 12 秒循環播放，
    確保 15 秒內所有照片都會出現。
  * 有 JavaScript 時改由 `website.js` 控制（每張 3.5 秒，一輪 14 秒），
    `#feature-control-1~4` 為切換鈕、`#feature-control-5` 為播放／暫停鈕
    （這五個 div 在原始 HTML 中是空的，數字與圖示以 CSS `::after` 產生）。
* **下拉選單**：`header nav ul li:hover > ul`（另加 `:focus-within` 供鍵盤操作），
  以 `opacity` + `visibility` + `translateY` 轉場展開。
* **影片**：`aside` 內兩支影片 `width:100%`、原生 `controls`、完整可見可播放；
  JS 另外做「同時只播一支」避免聲音重疊。
* **表格**：`caption`、`thead`、`tbody` 三者樣式各自設計；
  `tbody` 奇偶列底色不同；滑鼠移到列上時**底色與文字色同時改變**；
  點擊可鎖定該列（金色外框）方便對照數據。
* **統計圖**：以 PHP GD 產生 `extraimages/diagram.jpg`，
  X 軸為年份（2006–2011）、Y 軸為參觀人次（visitors 欄），長條圖 + 趨勢折線 + 數值標籤。
* **Leipzig Tweet 互動**：卡片 hover 時浮起、頭像放大 1.35 倍；
  點擊（或鍵盤 Enter/Space）展開／收合完整推文，右下角箭頭同步翻轉。
* **聖多馬教堂圖片**：白邊框 + 投影，`figcaption` 平時滑出視野外，
  滑鼠移入圖片時由下往上滑入顯示說明。
* **非 HTML 連結圖示**：`a[href$=".pdf"]` 以內嵌 SVG data URI 顯示 PDF 圖示，
  **純 CSS、未使用 JavaScript**；另對 `title` 含 `external` 的連結加上 ↗ 符號。
* **關圖後文字仍可見**：所有文字均為真實文字節點，圖片僅作背景／插圖，
  沒有以圖片呈現的文案；`figcaption`、推文內容等隱藏內容都能經由互動或列印顯示。
* **列印樣式**：`@media print` 隱藏頁尾連結列、語言切換、影片與輪播控制鈕；
  取消底色與陰影、縮小相片、展開所有隱藏說明文字、避免表格與圖跨頁，
  實測輸出為 **4 頁**（未套用列印樣式前為 5 頁且大量留白）。
* **CSS 註解**：全檔以繁體中文分節註解，遠超過最低 5 段的要求。

## 取捨與說明（現代寫法 vs. 2013 年做法）

1. **版面使用 CSS Grid**：`index.html` 的 `header / .title / .main / aside / footer`
   是 body 的直接子元素，2013 年的做法多半是 float + 固定寬度 + clearfix。
   本作品直接把 `body` 當作格線容器（`grid-template-columns`），
   讓 `.main` 與 `aside` 左右並排且不需要額外包裹層（HTML 不能改）。
   視覺結果相同，但程式碼更短且 RWD 更容易。
2. **原生 JavaScript 取代 jQuery / MooTools**：HTML 同時引用了 jQuery 2.0.1 與
   MooTools（nocompat），兩者並存容易產生混淆；`website.js` 改用原生 DOM API，
   不依賴任何函式庫，行為完全相同。兩個函式庫檔案仍保留以免 404。
3. **輪播採「CSS 動畫 + JS 強化」雙軌**：純 CSS 版本保證關閉 JavaScript
   仍符合「15 秒內看完所有照片」；JS 載入後加上 `js-slider` 旗標停用 CSS 動畫，
   改為可點選、可暫停的版本。
4. **統計圖為靜態圖檔**：評分要求「Create a static image」，
   因此以 PHP GD 離線產生 JPG（`extraimages/diagram.jpg`），未使用任何圖表函式庫或 CDN。
5. **字型**：離線環境不可載入 Google Fonts，改用系統 serif（Georgia）與
   sans-serif（Segoe UI）字族堆疊表現古典感。

## 驗證

* `node --check js/website.js` 通過。
* `curl` 檢查 `index.html`、`css/website.css`、`js/website.js`、
  `extraimages/diagram.jpg`、`videos/leipzig-1.mp4`、`pdf/a_file.pdf` 皆回 200。
* Chrome headless 於 1400px 與 800px 寬度截圖，確認寬版兩欄、窄版單欄、
  輪播自動切換（截圖時停在第 2 張且第 2 顆控制鈕呈作用中狀態）。
* Chrome `--print-to-pdf` 輸出列印版 PDF，逐頁檢查：無影片、無語言切換、
  無頁尾連結列，說明文字皆展開，共 4 頁。
* 以暫時強制展開的除錯頁面截圖確認下拉選單外觀後已移除該除錯檔。

## 尚未完成 / 不確定

* 未實際跑 W3C CSS validator（本機離線）。CSS 依 CSS3 規範撰寫並補上當年需要的廠商前綴。
* 評分表有兩項註明「Marked in IE」（聖多馬教堂圖片 hover 說明、推文圖片 hover 放大）。
  本機沒有 IE 可供測試，兩項效果皆以標準 `transform` / `transition` 實作，
  在 IE10+ 可運作，IE9 以下會退化為無動畫但功能仍在。
* 影片 `poster` 圖未設定（素材未提供），首幀在載入前為黑底。
* 「網頁對目標客群是否吸引人」等主觀分項（S 類）無法自我評分。

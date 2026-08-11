# WorldSkills 2013 – Skill 17 Web Design / Module C（LAYOUT）

線上位置：<http://127.0.0.1:83/worldskill/2013/modulec/>

## 題目重點

依設計稿 `material/picture/Layouts/1280.png`、`720.png`、`480.png` 實作
「Imaginary Store – J.S. Bach Top 25 Collection」商品頁，
並在 **800px** 斷點做寬／窄版切換，同時完成規格書中列出的所有互動效果。

## 檔案

| 檔案 | 說明 |
| --- | --- |
| `index.html` | HTML5 結構，語意化標籤與繁體中文註解 |
| `style.css` | 單一樣式表，含寬版基礎樣式與 `max-width: 800px` 窄版覆寫 |
| `material/` | 官方提供素材（字型、設計稿、效果示意圖、封面圖） |

## 已實作的評分項目

* **版面**：1280 / 720 / 480 三種寬度皆比對設計稿；800px 以下版面大幅改變
  （導覽連結與搜尋列隱藏、改為頂端漢堡圖示 + 頁尾導覽區塊、內容單欄、
  試聽清單單欄、推薦商品兩欄並排）。
* **字型**：`@font-face` 載入題目提供的 `pacifico.woff`（標題手寫體）與
  `ptsans.woff`（內文），無外部 CDN。
* **選取色**：`::selection` / `::-moz-selection` 紅底白字。
* **搜尋列**：一般狀態無外框，`:focus` 時輸入框與 search 按鈕同時出現白底灰框。
* **標題列**：麵包屑與 H1 皆使用 `text-shadow: 0 3px 3px rgba(0,0,0,.5), 0 2px 0 #fff`，
  背景 `#CC3333`，符合 `Title Bar.png` 標註。
* **Add to Cart**：純 CSS 漸層 + 圓角 + 內外陰影，具備 normal / hover / active 三態，未使用圖片。
* **唱片封面翻面**：`perspective` + `rotateY(180deg)` + `backface-visibility`，
  背面為半透明面板與曲目 `<ol>`；`ON SALE` 為真實文字（`.cover__ribbon`），可被搜尋引擎索引。
* **Recommends 封面翻面**：同上，背面另有 `Go to Album` 按鈕，
  一般 100% 不透明、hover 80% 不透明（`opacity` 轉場）。
* **Back to Top**：底色由透明轉為淡粉紅、箭頭同步放大的 transition；
  窄版頂端漢堡圖示 `href="#bottom-nav"`、Back to Top `href="#top"`，錨點雙向可跳轉。
* **無障礙 / 語意標籤**：`<del>` + `<ins>` 表示價格調整、`<form>` + `<label class="sr-only">`
  處理搜尋與電子報輸入、`<header>/<nav>/<main>/<aside>/<section>/<article>/<footer>`、
  `<blockquote>` + `<cite>`、`<figure>` + `<figcaption>`、`<time datetime>` 標示試聽長度。
* **陰影與圓角**：全部以 CSS3 `box-shadow` / `border-radius` 完成，無切圖。
* **文字可索引**：除了唱片封面圖與 `PREVIEW` 角標旁的封面圖外，其餘（含 `ON SALE`）皆為真實文字。
* **註解**：HTML 與 CSS 各超過 5 段繁體中文註解。

## 取捨與說明（現代寫法 vs. 2013 年做法）

1. **Flexbox 取代 float 網格**：2013 年多以 float + clearfix 實作 12 欄格線，
   本作品改用 flexbox（並保留 `-webkit-box` / `-ms-flexbox` 舊版前綴），
   視覺結果與 `1280-grid.png` 一致，但 reflow 更平滑（對應評分「resize 平滑」項目）。
2. **Logo 以 CSS 繪製**：素材資料夾內的 `material/icon/mainicon.png` 是一張手繪塗鴉，
   並非設計稿中的黑膠唱片標誌，因此改用純 CSS 圓形 + Pacifico 店名重建，
   同時讓店名文字可被索引。
3. **底紋以內嵌 SVG data URI 產生**：離線環境不可連外，改用 `feTurbulence` 雜訊
   內嵌於 CSS，取代原稿的雜訊點陣圖。
4. **CSS 變數（Custom Properties）**：2013 年尚不存在，但屬 CSS 標準且不影響視覺，
   用於集中管理主色。若需嚴格符合 2013 年語法，可將 `var(--x)` 展開為色碼。
5. **廠商前綴**：依評分要求，對 `transform`、`transition`、`transform-style`、
   `backface-visibility`、`linear-gradient`、`box-shadow`、`box-sizing`、`::selection`
   等加上當年仍需要的前綴；不存在的前綴屬性一律未使用。
6. **原有的 `index1280.css` / `index720.css` / `index480.css` 已合併為 `style.css`**：
   原檔僅有骨架（`index720.css`、`index480.css` 內容為空的 media query），
   拆成三個 min-width 檔案與題目「單一 800px 斷點」的需求不符，故整併。

## 驗證

* Chrome headless 於 1280 / 720 / 480 寬度截圖，與 `material/picture/Layouts/*.png` 逐區比對。
* 另以強制 `rotateY(180deg)` 的除錯頁面截圖，確認封面與推薦卡片背面內容
  與 `JSBach Transition.png`、`Recommends Cover Transition.png`、`Go to Album.png` 相符。

## 尚未完成 / 不確定

* 未實際跑 W3C HTML / CSS validator（本機離線，validator 需連外）。
  程式碼依 HTML5 / CSS3 規範撰寫，但無法出具驗證報告。
* 設計稿的紙張雜訊底紋、字距與行高為肉眼比對，非像素級完全相同。

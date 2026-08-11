# 第 46 屆全國技能競賽 網頁技術 模組 B － 杜拜板球委員會（Dubai Cricket Council）

進入點：<http://127.0.0.1:83/46th/senior/46national/TaskB/index.html>

---

## 一、重要聲明：題目原始素材沒有找到

題目（`G:\testquestion\46青年組網頁技術全國賽正式試題.pdf`，第 8～11 頁）說明中心會提供
「未經格式化的靜態 HTML 檔案 ＋ JavaScript ＋ 圖片」，選手只要寫 CSS。

**這批杜拜板球委員會版本的 HTML／圖片，在本機找遍後確認不存在**：

- `G:\testquestion\` 下與第 46 屆相關的檔案只有 `46青年組網頁技術全國賽正式試題.pdf`
  與 `word\46青年組網頁技術全國賽正式試題.docx`，沒有任何素材資料夾或 zip。
- 以 ripgrep 掃過 `G:\testquestion\`（所有 HTML）與 `C:\nginx\skill\`，沒有任何檔案含
  `cricket` 字樣。

不過可以確定這一題是 **WorldSkills 2015 São Paulo，Skill 17，Layout B「Movie Store」**
的中文改編版，原始素材確實存在於本機：

```
G:\testquestion\worldskill\2015\WSC2015_TP17_actual\WSC2015_TP17_resources_layout\
  Thomas - Layout\Layout B - 2hrs - Movie Store\Media\
```

對照證據：五個頁面一一對應（index／all-movies→tickets／movie→crickets／about／contact）、
同一組 `all.js`（切換 class 的 popup／tabs／表單提示）、同一組 CSS 類別名稱
（`.rent-now.button`、`#rental-dialog`、`.rental-steps`、`.login`、`.tabs > a`、
`.tab-content`、`.success.label`、`.alert.label`），而且題目中文譯本還殘留原題的
「**租借**對話框」字樣（原題是租 DVD）。

### 因此本作品的做法

1. **JavaScript 完全沿用中心提供的原檔**：`javascripts/all.js` 與 `javascripts/jquery.js`
   直接由上述 WorldSkills 2015 素材複製，**一個字都沒有改**。
2. **HTML 由我依題目敘述重建**（因為原檔缺席），但嚴格沿用原素材的 DOM 結構與
   **所有 `all.js` 依賴的 class／id**，並依題目把主題換成杜拜板球委員會、頁面改名為
   `index / tickets / about / contact / crickets`，首頁補上題目指定的五個區塊
   （DCC News、Ranking、Final match、DCC Services、Recent Comments）。
3. **圖片全部自行以 SVG 程式產生**（`images/`），純本機、無任何外部連結。
4. 本作品的評分重點——**CSS**——是完整、原創的（`stylesheets/website.css`，約 1,600 行）。

> 若之後拿到官方的杜拜板球 HTML 素材，把 `stylesheets/website.css` 直接套上去即可，
> 因為所有互動用的選擇器都與原素材一致；只有我另外新增的區塊樣式（`.dcc-news`、
> `.ranking`、`.services-list`、`.facts`、`.pagination` 等）會用不到。

---

## 二、檔案結構

```
TaskB/
├─ index.html          首頁：DCC News / Ranking / Final match / DCC Services / Recent Comments
├─ tickets.html        所有賽事與購票清單（含分頁）
├─ crickets.html       單一賽事詳情、比分、相片集（含購票對話框）
├─ about.html          委員會簡介與 5 個分會（地圖／地址／電話）
├─ contact.html        3 組表單，以頁籤切換：Cricket Review / Membership Application / Others
├─ stylesheets/
│   └─ website.css     ★ 本模組唯一的樣式檔（繁體中文註解）
├─ javascripts/
│   ├─ all.js          中心提供，未修改
│   └─ jquery.js       中心提供，未修改
├─ images/             自行以 SVG 產生的 LOGO、賽事主視覺、地圖、相片、圖示（共 23 檔）
├─ extra-images/       題目允許的額外圖檔放置處（目前為空）
├─ screenshots/        驗收截圖（見第五節）
└─ README.md
```

---

## 三、設計說明

| 項目 | 說明 |
| --- | --- |
| 品牌調性 | 「古典 ＋ 摩登」：襯線標題（Georgia）配無襯線內文、金色細線與圓角卡片 |
| 色票 | 杜拜夜空深藍 `#0d2b45`／沙金 `#d8b45a`／球場綠 `#1b6b4c`／沙色底 `#faf7f0` |
| 字體 | 只用系統內建字體（Georgia／Segoe UI／微軟正黑），**不引用任何外部字型或 CDN** |
| 電腦版 | 內容最大寬 1160px，首頁與 crickets 為「主內容 ＋ 側欄」兩欄，側欄 `position: sticky` |
| 手機版 | ≤ 899px 全部改單欄；導覽列與頁籤改為可左右捲動；Right Now 按鈕滿版；表單欄位加大 |
| 動態 | 皆以 CSS `transition` / `@keyframes` 完成，並支援 `prefers-reduced-motion` |

### 對照評分表的互動效果

| 評分項 | 實作位置 |
| --- | --- |
| 2、3：對話框顯示／關閉 | `#rental-dialog` 預設 `opacity:0; visibility:hidden`，`.show` 時淡入＋卡片上浮；關閉鈕旋轉 90° |
| 4：Right Now hover／active | `.rent-now.button:hover` 上浮＋光暈掃過；`:active` 下壓、內陰影 |
| 5：成功／警告提示 | `.success.label` 綠底打勾、`.alert.label` 紅底驚嘆號，皆有 `label-in` 滑入動畫；`.hidden` 時隱藏 |
| 6：頁籤作用狀態 | `.tabs > a.active` 換白底、金色頂線與底線 |
| 7：頁籤內容切換 | `.tab-content` 預設 `display:none`，`.active` 顯示並套 `tab-in` 淡入上移 |

### 無障礙（ARIA）

- Right Now 按鈕：`role="button"`
- 購票對話框：`role="dialog" aria-modal="true"`，`aria-labelledby` 指向「How to Buy」標題、
  `aria-describedby` 指向購票摘要段落（含賽事、日期、場地、票價）
- 對話框內的登入表單：`<form aria-labelledby="rental-login-title">`（螢幕讀取器辨識為表單）
- 關閉（X）按鈕：`aria-label="close the dialog"`
- 分頁：`<nav class="pagination" aria-label="Match list pagination">`，
  上一頁 `aria-label="Go to the previous page of matches"`、
  下一頁 `aria-label="Go to the next page of matches"`，目前頁 `aria-current="page"`
- 頁籤：`role="tablist" / role="tab" / aria-controls / aria-selected`，內容 `role="tabpanel" aria-labelledby`
- 其他：主／次導覽 `aria-label`、頁尾表單 `role="search"`、隱藏標籤 `.visually-hidden`

> 注意：中心提供的 `all.js` 只切換 class，不會更新 `aria-selected`，
> 依題目「不可修改 JavaScript」的規定，此處維持靜態值。

---

## 四、驗證方式與結果

| 檢查 | 方法 | 結果 |
| --- | --- | --- |
| CSS 語法 | 大括號／註解狀態機檢查 | 266 `{` / 266 `}`，深度歸零，無錯誤 |
| CSS 被瀏覽器完整解析 | Chrome CDP 讀 `document.styleSheets[0]` 遞迴計算規則數 | 266 條，與大括號數相同 → **沒有任何規則被瀏覽器丟棄** |
| 對話框開關 | CDP 點 `.rent-now` → `hasClass('show')` | `false → true → false`（點 X 後關閉）✔ |
| 頁籤切換 | CDP 點 `#tab2`、`#tab3` | active tab／active panel 同步為 `tab3` / `tabcontent3` ✔ |
| 成功／警告提示 | CDP 移除 `.hidden` | 兩種樣式都正確呈現 ✔ |
| 手機版 | CDP `Emulation.setDeviceMetricsOverride` 390×844、DPR 2、touch | 五頁皆單欄且可操作 ✔ |
| 外部連線 | 全站只引用 `stylesheets/`、`javascripts/`、`images/` 之相對路徑 | 無任何 CDN／外部字型 ✔ |

---

## 五、截圖對照（`screenshots/`）

| 檔名 | 內容 |
| --- | --- |
| `desktop-index.png` | 首頁，1200×2600 全頁 |
| `desktop-tickets.png` | 購票清單，桌機 3 欄卡片 ＋ 分頁 |
| `desktop-crickets.png` | 賽事詳情 ＋ 側欄相關賽事 |
| `desktop-about.png` | 分會地圖卡片 |
| `desktop-contact.png` | 聯絡表單（第一個頁籤，桌機雙欄欄位） |
| `desktop-dialog.png` | 購票對話框開啟（crickets.html） |
| `desktop-index-dialog.png` | 首頁點 Right Now 後的對話框（CDP 實際點擊） |
| `desktop-contact-tab2-success.png` | 切到第二頁籤 ＋ 成功提示 |
| `desktop-contact-tab3-alert.png` | 切到第三頁籤 ＋ 警告提示 |
| `mobile-*.png` | 390×844 行動版（CDP `setDeviceMetricsOverride`，非 `--window-size`） |

---

## 六、已知限制

1. **最大的一點**：HTML 是我依題目文字重建的，不是中心原檔（原因見第一節）。
   若評分要求「HTML 一字未改」，需拿到官方素材後把 CSS 重新套上。
2. `crickets.html` 最上方那則 `This page is a mock-up.` 提示，會被中心提供的 `all.js`
   （`$('.label').addClass('hidden')`）在載入時隱藏——這是原素材的既有行為，我沒有改 JS。
3. 圖片是程式產生的向量示意圖（球場俯視、街廓地圖），不是真實照片。
4. 頁籤的 `aria-selected` 為靜態值，原因見第三節說明。

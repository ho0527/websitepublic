# WorldSkills 2013 · Skill 17 Web Design · Module A — EnviroFund

網址：<http://127.0.0.1:83/worldskill/2013/moduleA/>

## 題目重點

- 為虛擬組織 **EnviroFund**（環境專案群眾募資平台）設計 **標誌** 與 **三個頁面**。
- 目標客群：關心環境、願意小額付費的年輕族群。
- 交付格式：PNG / JPG / SVG 三個檔案，命名 `XX_index`、`XX_project_enrollment`、
  `XX_project_presentation`（XX 為國碼，本作品採用 **TW**）。

## 檔案結構

```
moduleA/
├─ index.html                     首頁設計稿（Front page）
├─ project-enrollment.html        專案登錄頁設計稿
├─ project-presentation.html      專案展示頁設計稿
├─ assets/
│  ├─ css/design.css              設計代幣、共用元件（按鈕、表單、頁首頁尾、社群列）
│  ├─ css/pages.css               三個頁面各自的版面
│  ├─ js/export-mode.js           ?export=1 時隱藏設計稿導覽列，供匯出 PNG 使用
│  ├─ img/logo.svg                EnviroFund 標誌（向量）
│  ├─ img/logo-white.svg          深色底專用標誌
│  ├─ img/social/                 Facebook / Twitter / Google+ 圖示（自題目 .eps 取出）
│  └─ img/…                       由 material/picture/Photos 裁切的圖片
├─ export/                        交付用 PNG（由 headless Chrome 以 1440px 視埠輸出）
│  ├─ TW_index.png                     1440 × 3372
│  ├─ TW_project_enrollment.png        1440 × 1828
│  └─ TW_project_presentation.png      1440 × 2797
└─ material/                      題目提供素材（未納入版控，見 .gitignore）
```

## 設計說明

### 標誌

`assets/img/logo.svg` = **硬幣（募資）＋葉片（環境）＋ 文字「EnviroFund」＋ 標語**。
文字部分 `Enviro` 使用深墨綠、`Fund` 使用品牌綠，讓兩個語意清楚分開。
標誌出現在三個頁面的頁首與頁尾（頁尾使用深底版本 `logo-white.svg`）。

### 配色

| 用途 | 色碼 |
| --- | --- |
| 主要文字 | `#0E2A22` |
| 品牌綠（按鈕、連結） | `#1FA97A` |
| 深綠（工具列、頁尾） | `#0B3B2C` |
| 亮綠（進度、CTA 帶狀區） | `#C2E86B` |
| 琥珀（倒數警示） | `#FFB347` |
| 頁面底色 | `#F4F8F5` |

### 各頁對應評分項目

**首頁 `index.html`**

- 標誌 ✔（頁首、頁尾）
- 專案登錄連結 ✔（頁首「Start a project」按鈕、主視覺次要按鈕、CTA 帶狀區共三處）
- 登入功能 ✔（頂端工具列：Username 欄、Password 欄、LOG IN 按鈕）
- 專案列表 ✔ 共 **6** 個（>3），每張卡片皆含 **圖片 / 標題 / 說明文字 / FUND THIS PROJECT 按鈕**

**專案登錄頁 `project-enrollment.html`**

- 標誌 ✔
- 輸入欄位 ✔ Project title、Project image（檔案上傳區）、Project description、Email address、Password
- 送出按鈕 ✔ `SUBMIT PROJECT`

**專案展示頁 `project-presentation.html`**

- 標誌 ✔
- 專案標題 ✔ `Project 1 — Save the polar bears`
- 專案圖片 ✔ 主視覺一張＋內文一張
- 內容區標題／次標題 ✔ 主標題 34px、次標題 28px、第三層 20px
  （相鄰層級差 6px 以上，符合「至少 4px 差距」）
- 專案摘要盒 ✔ 累積金額 `€ 39,120`、剩餘天數 `12`、FUND THIS PROJECT 按鈕
- 登入功能 ✔（同首頁的頂端工具列）
- 留言功能 ✔ textarea ＋ `POST COMMENT` 按鈕，並示範兩則既有留言
- 社群分享 ✔ Facebook、Twitter、Google+ 三個圖示

## 技術取捨與說明

1. **交付格式**
   題目要求交付「圖檔」。本作品以 HTML/CSS 精確排版後，用 headless Chrome 以
   1440px 視埠輸出整頁 PNG 至 `export/`，等同於設計稿的點陣輸出；同時保留可互動的
   HTML 版本供瀏覽。**題目另要求繳交含圖層的原始檔（PSD/XCF/ID）；本環境沒有相對應的
   點陣繪圖軟體，這一項（0.25 分）無法提供**，改以 HTML/CSS 原始碼與向量 SVG 標誌作為
   可編輯來源。

2. **社群圖示的 .eps 素材**
   題目提供的 `.eps` 為 Adobe Illustrator 的 DOS EPS，內含二進位 AI 私有資料，本機沒有
   Ghostscript / ImageMagick / Illustrator 可以直接光柵化。作法是解析檔案內嵌的 XMP
   `<xmpGImg:image>` 縮圖（256px base64 JPEG）取出正確的官方圖形，再裁切為 128×128 使用：
   - Facebook → `FB-fLogo-online-broadcast.eps`
   - Twitter → `twitter-bird-white-on-blue.eps`
   - Google+ → `icons_gPlus_logo.eps`

   因此圖示為 **256px 來源的點陣圖**而非向量，於 40px 顯示尺寸下無可見劣化。

3. **文字內容**
   標題取自 `material/Module_A_Example_Text.docx` 的範例專案名稱，內文混用該檔的
   Lorem ipsum 與符合情境的英文說明（題目允許自行增補，且文字只評設計不評語意）。

4. **不使用 CDN**
   全部樣式、腳本、字型皆為本機檔案或系統字型（Segoe UI stack），離線可完整呈現。

5. **2013 年做法 vs. 現代寫法**
   原題時代普遍需要 IE8 相容。本作品以現代 CSS（自訂屬性、Flexbox、Grid、`object-fit`）
   達成相同視覺，取代當年的浮動排版與切圖，程式碼較短且易維護；視覺結果與 2013 年
   用切圖能做到的一致。

## 重新產生交付 PNG

網站需在 <http://127.0.0.1:83/> 運作中，於本機執行（`cdpshot.py` 為以 CDP 控制視埠的
截圖工具，因為 headless Chrome 在 Windows 的 `--window-size` 有最小視窗寬度限制）：

```powershell
& "C:\Program Files\Google\Chrome\Application\chrome.exe" --headless=new --disable-gpu `
  --no-sandbox --hide-scrollbars --screenshot="export\TW_index.png" `
  --window-size=1440,3600 --virtual-time-budget=8000 `
  "http://127.0.0.1:83/worldskill/2013/moduleA/index.html?export=1"
```

`?export=1` 會隱藏右下角的設計稿導覽列，使輸出只包含設計本身。

## nginx 乾淨網址（未修改 nginx.conf，僅提供設定片段）

本模組全為靜態頁，不需要 rewrite；若要讓 `/moduleA/enrollment` 對應到
`project-enrollment.html`，可於 server 區塊加入：

```nginx
location /worldskill/2013/moduleA/ {
    try_files $uri $uri/ $uri.html /worldskill/2013/moduleA/index.html;
}
```

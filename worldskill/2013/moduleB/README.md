# WorldSkills 2013 · Skill 17 Web Design · Module B — FASHION4YOU 響應式改版

網址：

- 實際響應式頁面 <http://127.0.0.1:83/worldskill/2013/moduleB/>
- 三裝置稿件對照 <http://127.0.0.1:83/worldskill/2013/moduleB/mockups.html>

## 題目重點

把虛擬公司 **FASHION4YOU**（線上服飾店）的舊網站改成響應式，並針對三種裝置各交一張
實際像素尺寸的稿件：

| 裝置 | 解析度 | 檔名 |
| --- | --- | --- |
| Computer | 1440 × 900 | `TW_Computer_mockup.png` |
| Tablet | 768 × 1024 | `TW_Tablet_mockup.png` |
| Smartphone | 320 × 480 | `TW_Smartphone_mockup.png` |

企業色：`#126080`、`#AFE1F2`、`#CD4D58`。內容須與舊站相同。

## 檔案結構

```
moduleB/
├─ index.html                     響應式首頁（單一頁面，三種裝置共用）
├─ mockups.html                   三裝置稿件對照頁（iframe 實際尺寸 + 紅框）
├─ assets/
│  ├─ css/site.css                Mobile first，640 / 1024 兩個斷點
│  ├─ js/site.js                  主選單展開；?flat=1 匯出模式
│  ├─ fonts/JuliusSansOne-Regular.ttf   標誌所附字型（本機內嵌）
│  ├─ img/logo.svg                自 logo_vector.ai 抽出的向量標誌
│  ├─ img/logo-white.svg          深底版本（深藍字改白）
│  ├─ img/hero-model.png          主視覺人物（白底已去背）
│  ├─ img/welcome-1.png / -2.png  歡迎區兩張人物（已去背）
│  ├─ img/products/sample-1~4.jpg 商品照
│  └─ img/social/                 Facebook / Twitter / YouTube（素材第 5 組）
├─ export/                        交付 PNG（含紅色解析度框）
└─ material/                      題目提供素材（未納入版控，見 .gitignore）
```

## 內容對照（Site Content Structure）

| 題目要求 | 實作位置 |
| --- | --- |
| Header：Logo、top menu(home / sitemap / contact us)、文字、照片 | 頂端工具列 + 頁首 + 主視覺 |
| Search：文字輸入 + 搜尋按鈕 | 頁首 `.search` |
| Main menu：5 個連結 | `.mainmenu`（Categories / New products / Popular products / Sale items / About us） |
| Welcome：標題、文字、2 張圖 | `#about` 區 |
| New products：圖片、價格、文字、Buy now、NEW 強調 | `#new-products`，4 件，皆有紅色 NEW 標籤 |
| Featured products：圖片、價格、文字、Buy now | `#featured-products`，4 件 |
| Shopping cart：Items in Bag - 0 Item(s) | 頂端工具列購物車鈕；手機另有底部固定列 `Bag · 0` |
| Shop by category：連結 | `#categories`，12 個分類連結 |
| Social media：3 個社群按鈕 | `.social`（Facebook / Twitter / YouTube） |
| Footer：公司資訊 + 版權 | `.footer`，含原站地址、電話、Copyright © 2013 |

舊站文案（Lorem ipsum、$99.99、公司地址、版權字樣）完全沿用。
依題目「除標誌外不得沿用原設計元素」，只保留了 `logo`，其餘裝飾（放射狀背景、
灰色選單條、Facebook / Twitter / YouTube 文字圖）全部重做。

## 響應式策略

| 斷點 | 版面 |
| --- | --- |
| < 640px（手機） | 單欄；主選單收合成漢堡；商品改成 **橫向滑動列**（scroll-snap，卡片寬 62%），把 4 件商品從 4 個垂直畫面壓成 1 個橫向滑動；底部固定四格操作列（Home / Shop / Search / Bag） |
| 640 – 1023px（平板） | 主視覺左文右圖；商品 2 × 2 網格；分類 3 欄；保留底部固定操作列（觸控） |
| ≥ 1024px（桌機） | 主選單完整展開；商品 4 欄；分類 6 欄；頁尾 4 欄；不顯示漢堡與底部固定列 |

**觸控最佳化**：`--tap` 變數在手機／平板為 48px、桌機縮為 44px，所有按鈕、選單項、
分類連結、社群圖示皆以此為最小點擊高度；商品滑動列使用 `scroll-snap-type: x mandatory`
讓滑動會停在卡片邊界。

## 交付圖上的紅框

題目要求「每張圖含 **小於 4px** 的清楚邊框標示該裝置的螢幕解析度，用來指出 page fold」。
`export/` 內三張圖皆為 **整頁** 稿件，並加上：

- **3px 紅色實線外框**：框住第一個畫面（例如 1440 × 900），即 page fold 之上的範圍
- **紅色虛線**：之後每滿一個螢幕高度畫一條，標示第 2、3、4… 個畫面的分界
- **右上角紅色標籤**：文字寫明該裝置解析度（例如 `1440 x 900`）

`mockups.html` 則以 iframe 實際尺寸呈現同樣三個框，內容是活的頁面而非圖片。

## 技術取捨與說明

1. **標誌向量化**
   `material/picture/LOGO/logo_vector.ai` 其實是 PDF 容器，用 PyMuPDF 開啟後把最大一組
   圖形的區域轉成 SVG（`assets/img/logo.svg`），因此桌機的標誌是真向量、不會糊。
   深底版本 `logo-white.svg` 只把深藍填色換成白色，紅色的「4」與淺藍的「YOU」保留。

2. **人物照去背**
   素材的人物照為純白棚拍，用 PIL 依最暗通道推算 alpha 去背成 PNG，讓人物可以直接
   壓在淺藍色主視覺上，不會出現白色方塊。

3. **字型**
   標題使用素材附的 `JuliusSansOne-Regular.ttf`（以 `@font-face` 內嵌本機檔案），
   與標誌字型一致；內文使用系統 Segoe UI stack。**未使用任何 CDN**。

4. **截圖工具**
   Windows 版 headless Chrome 的 `--window-size` 有約 500px 的最小視窗寬度，直接指定
   320px 會以 500px 排版後裁切成 320px（版面錯誤）。因此 320 / 768 兩張稿件改用
   Chrome DevTools Protocol 的 `Emulation.setDeviceMetricsOverride` 精確設定視埠，
   再用 `Page.captureScreenshot(captureBeyondViewport=true)` 取整頁。

5. **`?flat=1` 匯出模式**
   手機／平板的底部操作列是 `position: fixed`，整頁截圖時會固定在視窗底部造成大片空白。
   加上 `?flat=1` 會把它改成一般文件流，讓整頁稿件的高度等於真實內容高度。

6. **2013 年做法 vs. 現代寫法**
   原題時代的響應式多以 float + `@media` 手刻，並需 IE8 的 polyfill。本作品改用
   Flexbox / Grid / CSS 自訂屬性 / `aspect-ratio` / `scroll-snap` 達成同樣（並更好）的
   結果；若真的要支援 IE8，需退回 float 版面並加上固定像素備援值，視覺差異主要在
   圓角、陰影與滑動列。

## 重新產生交付 PNG

網站需在 <http://127.0.0.1:83/> 運作中。1440 寬可直接用 Chrome CLI：

```powershell
& "C:\Program Files\Google\Chrome\Application\chrome.exe" --headless=new --disable-gpu `
  --no-sandbox --hide-scrollbars --screenshot="export\TW_Computer_mockup.png" `
  --window-size=1440,4200 --virtual-time-budget=8000 `
  "http://127.0.0.1:83/worldskill/2013/moduleB/index.html?flat=1"
```

768 / 320 兩種寬度請改用 CDP（見上方第 4 點），否則版面會以 500px 排版後被裁切。

## nginx 乾淨網址（未修改 nginx.conf，僅提供設定片段）

```nginx
location /worldskill/2013/moduleB/ {
    try_files $uri $uri/ $uri.html /worldskill/2013/moduleB/index.html;
}
```

如此 `/worldskill/2013/moduleB/mockups` 即可對應到 `mockups.html`。

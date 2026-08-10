# WorldSkills 2024 — Web Technologies 模組 C「Lyon Heritage Sites」

以純檔案為資料來源的里昂遺產地點網站。所有文章都放在 `content-pages/` 底下的
`.html` / `.txt` 檔案中，程式只負責讀取、解析前置資料（front-matter）並渲染成網頁，
**完全不使用資料庫**（規格書明訂本模組不需要資料庫）。

- 進入點：`index.php`
- 本機網址：<http://127.0.0.1:83/worldskill/2024/modulec/>

---

## 一、資料夾結構

```
modulec/
├── index.php                  單一入口控制器（路由 → 取資料 → 選樣板）
├── app/
│   ├── config.php             路徑、站台資訊、網址模式等設定
│   ├── helpers.php            輸出跳脫、網址組裝、slug / 標題轉換等共用函式
│   ├── Router.php             路由解析（PATH_INFO / route 參數 / 重寫後的 REQUEST_URI）
│   ├── ContentPage.php        單篇文章模型：front-matter、標題規則、草稿、日期、封面
│   ├── ContentRepository.php  掃描 content-pages、列表排序、標籤篩選、搜尋、路徑安全檢查
│   └── ContentRenderer.php    .html / .txt 兩種內容的渲染策略與圖片路徑重寫
├── views/
│   ├── layout.php             共用版型（含社群分享 meta 標籤）
│   ├── listing.php            索引列表 / 子資料夾列表
│   ├── single.php             單篇遺產頁（封面、標題、附註、主要內容）
│   ├── tag.php                標籤查詢結果
│   ├── search.php             搜尋結果
│   ├── not-found.php          404
│   └── partials/page-card.php 列表用的文章卡片
├── assets/
│   ├── css/style.css          全部樣式（無任何外部 CDN）
│   └── js/app.js              封面聚光效果、照片放大檢視（原生 JS，無函式庫）
├── content-pages/             編輯撰寫的文章，結構保持原樣不動
│   ├── images/                全站唯一的圖片資料夾
│   ├── basilicas/
│   │   └── interiors/         巢狀子資料夾範例
│   ├── museums/
│   └── parks/
└── .gitignore
```

## 二、路由

| 路由 | 說明 |
| --- | --- |
| `/` | 索引列表（content-pages 最上層的子資料夾與頁面） |
| `/heritages/{slug}` | 最上層的單篇文章 |
| `/heritages/{folder}` | 子資料夾列表 |
| `/heritages/{folder}/{slug}` | 子資料夾內的單篇文章 |
| `/heritages/{folder}/{folder2}/{slug}` | 巢狀子資料夾內的單篇文章 |
| `/tags/{tag}` | 列出含有該標籤的所有頁面 |
| `/search?q={keyword}` | 搜尋標題或內容，`/` 可分隔多個關鍵字（OR 邏輯） |

### 網址模式（重要）

規格書允許選手把專案放在子資料夾或不同埠號，只要能從 `/XX_module_c/` 進入即可。
本模組的路由器同時支援三種寫法：

1. `index.php/heritages/slug`（PATH_INFO）
2. `index.php?route=heritages/slug`（查詢參數）
3. `heritages/slug`（伺服器已設定網址重寫）

`app/config.php` 的 `MC_CLEAN_URL` 決定**產生連結時**採用哪一種：

- `false`（目前的預設值）→ 輸出 `index.php?route=...`
- `true` → 輸出規格書要求的乾淨路徑 `/worldskill/2024/modulec/heritages/...`

這台練習機的 nginx 沒有替本目錄設定重寫規則（`location ~ \.php$` 也不會轉發 PATH_INFO），
因此預設使用 `false`，讓網站在不動伺服器設定的情況下就能完整瀏覽。
若要啟用乾淨網址，在 nginx 的 `server` 區塊加入下列設定後，把 `MC_CLEAN_URL` 改成 `true`：

```nginx
location ^~ /worldskill/2024/modulec/ {
    try_files $uri $uri/ /worldskill/2024/modulec/index.php?route=$uri&$args;

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }
}
```

（Apache 環境則等同於把不存在的實體檔案 rewrite 到 `index.php`。）

## 三、內容撰寫規則

檔名：`YYYY-MM-DD-title-in-lower-case-with-hyphens.html` 或 `.txt`。

前置資料（選用）以單獨一行的 `---` 包住，可用的鍵值：

| 鍵 | 說明 |
| --- | --- |
| `title` | 標題（選用） |
| `tags` | 標籤，以逗號或「逗號加空白」分隔（選用） |
| `draft` | 值為 `true`（不分大小寫）時視為草稿，不會出現在任何列表（選用） |
| `summary` | 一行摘要，列表時顯示（選用） |
| `cover` | 封面圖片檔名（選用） |

### 標題挑選順序

1. front-matter 的 `title`
2. 內容中第一個 `<h1>` 的純文字
3. 檔名（去掉 `YYYY-MM-DD-` 與副檔名，連字號換成空白，每個單字首字母大寫）

### 列表規則

- 子資料夾在前（字母順序），文章在後（檔名反向字母順序 → 最新在上）
- 不列出：日期在今天之後的頁面、`draft: true` 的頁面、檔名前 11 碼不是 `YYYY-MM-DD-` 的頁面
- 標題與摘要都是連往該篇文章的連結

### 內容渲染

- `.html`：原樣輸出，只重寫 `<img src>` 讓圖片指向 `content-pages/images/`，並在缺少 `alt` 時依檔名補上
- `.txt`：每一行文字轉成 `<p>`；不含空白且以圖片副檔名結尾的整行，轉成 `<p><img></p>`
- 封面圖片：優先使用 front-matter 的 `cover`，未定義時改用 `images/` 內與檔名同名的圖片

## 四、示範內容一覽

| 檔案 | 用意 |
| --- | --- |
| `2024-09-01-example-page.html` | 規格書的 .html 範例（完整 front-matter） |
| `2024-10-20-greatest-lyon-heritage-site.html` | 對應規格書示意圖的主要展示頁 |
| `2024-08-15-hidden-draft-of-the-croix-rousse.html` | `draft: TRUE`（大寫）→ 驗證草稿不分大小寫且不被列出 |
| `2099-01-01-a-post-for-future-posting.txt` | 未來日期 → 不被列出 |
| `about-this-site.html` | 檔名沒有日期 → 不被列出 |
| `2024-07-04-traboules-of-old-lyon.txt` | 無 `title`、無 `cover` → 標題與封面都由檔名推導 |
| `2024-06-18-place-bellecour.html` | 無 `title` 但有 `<h1>` → 標題取自 h1 |
| `basilicas/…` | 子資料夾中的頁面 |
| `basilicas/interiors/…` | 巢狀子資料夾中的頁面 |
| `museums/2024-05-30-musee-des-beaux-arts.html` | 完全沒有 front-matter（前置資料為選用） |
| `parks/2024-05-30-parc-de-la-tete-dor.txt` | 無 `title`、無 `cover` 的 .txt |

> `content-pages/images/` 內的圖片是離線環境下自行產生的漸層佔位圖，
> 正式比賽時可直接換成主辦單位提供的素材，檔名沿用即可。

## 五、版面與互動細節

- **封面聚光**：上層疊一張同樣的封面，套用
  `radial-gradient(circle 300px at var(--spotlight-x) var(--spotlight-y), rgb(0,0,0) 0, rgba(255,255,255,0) 300px)`
  遮罩，圓心由 JS 隨滑鼠更新。
- **標題排版**：`font-variant-ligatures: common-ligatures`。
- **附註資訊**：`position: sticky`，向下捲動時固定在頂端。
- **內文照片**：滿版於主要內容容器，點擊放大；再次點擊或捲動即關閉還原（Esc 也可關閉）。
- **首字下沉**：第一段首字母佔三行。
- **社群分享**：每一頁動態輸出 `og:title` / `og:type` / `og:image` / `og:url` / `og:description` 與 `twitter:card`。
- **無障礙**：跳至主要內容連結、語意化地標、麵包屑、所有圖片皆有 `alt`、放大檢視具備鍵盤操作與 `role="dialog"`。

## 六、安全性

- 路由中的資料夾與檔名一律經過 `ContentRepository::resolve()` 檢查，
  出現 `..` 或解析結果跳出 `content-pages` 一律回傳 404。
- 所有動態輸出都經過 `mc_e()`（`htmlspecialchars`）跳脫；
  `.html` 文章是編輯自行撰寫的可信內容，依規格「原樣渲染」。
- 本模組沒有資料庫查詢，因此不涉及 SQL injection。

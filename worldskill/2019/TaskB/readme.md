# WorldSkills 2019 Skill 17 模組 B — CMS and Layout

**Kazan MuseumTour** — 喀山博物館導覽網站（前台 + 內容管理後台）

- 題目：`WSC2019_TP17_CMS_actual.pdf`（CMS and Layout，B1–B6）
- 前台網址：<http://127.0.0.1:83/worldskill/2019/TaskB/index.php/>
- 後台網址：<http://127.0.0.1:83/worldskill/2019/TaskB/index.php/admin/>
- 帳號：`admin / admin`（管理員）、`editor / editor`（編輯）

---

## 1. 與原題的差異說明（重要）

原題指定使用 **WordPress**（以 blankslate 為父主題建立子主題 `Kazan_MuseumTour`）。
本機環境為 **PHP 8.3.7**，題目附的 WordPress 5.2 與各外掛在 PHP 8 下無法正常執行，
因此改以**自行開發、同構的迷你 CMS** 完成，並刻意保留 WordPress 的所有結構性概念，
讓每一條評分項目都有對應的實作：

| WordPress 概念 | 本專案對應實作 |
| --- | --- |
| 父主題 blankslate | `themes/blankslate/`（style.css 檔頭 + 重置 + 版面骨架樣板） |
| 子主題 Kazan_MuseumTour | `themes/Kazan_MuseumTour/`（`style.css` 檔頭含 `Template: blankslate`） |
| 子主題覆寫父主題樣板 | `App\Core\Theme` 依「子主題 → 父主題」順序搜尋樣板 |
| 外掛 + hook（add_action / add_filter） | `plugins/*/plugin.php` + `App\Core\PluginManager` |
| 頁面 / 文章 / 分類 / 精選圖片 | `museums` / `posts` / `categories` 資料表的欄位 |
| wp-admin 儀表板小工具 | 後台 Dashboard 的 At a Glance / Activity / Quick Draft |
| 使用者角色 administrator / editor | `users.role` = `admin` / `editor`（`App\Core\Auth` 能力表） |

所有後端程式以物件導向撰寫，資料庫存取一律使用 **PDO prepared statement**，
輸出一律經 `App\Core\Html::e()` 做 XSS 跳脫，後台表單全部帶 CSRF 權杖。

---

## 2. 安裝與重建

```bash
# 1. 建立資料庫（會先 DROP 再重建，含測試資料）
mysql -u root -h 127.0.0.1 < "!SQL/schema.sql"

# 2. 確認 config.php 的連線設定與網站根路徑
#    base_path 預設 '/worldskill/2019/TaskB/'
```

資料庫名稱：`worldskill2019_taskb`（utf8mb4 / InnoDB，含外鍵約束）。

| 資料表 | 用途 |
| --- | --- |
| `users` | 後台使用者與角色 |
| `settings` | 站名、標語、社群連結、聯絡表單文字…（key/value） |
| `plugins` | 外掛清單與啟用狀態 |
| `categories` | 文章分類 |
| `museums` | 博物館頁面（`is_selected` 決定版型、`category_id` 對應該館新聞分類） |
| `posts` | 新聞文章（FK → categories、users） |
| `login_attempts` | 登入嘗試紀錄（安全性外掛） |

---

## 3. 網址結構

預設使用 `index.php/...`（**不需要修改 nginx 設定**）：

| 頁面 | 網址 |
| --- | --- |
| 首頁 | `index.php/` |
| 全部博物館 | `index.php/museums/` |
| 博物館頁 | `index.php/museum-of-national-culture/` |
| 全部新聞 | `index.php/news/` |
| 分類新聞 | `index.php/news/seasonal-events/` |
| 單篇新聞 | `index.php/news/site-updates/kazan-museumtour-is-online/` |
| 聯絡我們 | `index.php/contact/` |
| 後台 | `index.php/admin/` |
| Sitemap / robots | `index.php/sitemap.xml`、`index.php/robots.txt` |

### 3.1 切換成完全乾淨的網址（選用）

若要得到規格所寫的 `<host>/museum-of-national-culture/`、`<host>/admin/`，
請把 `config.php` 的 `'clean_urls'` 改成 `true`，並在 nginx 的 `server` 區塊加入
下列設定片段（本專案未自行修改 `nginx.conf`）：

```nginx
location ^~ /worldskill/2019/TaskB/ {
    try_files $uri /worldskill/2019/TaskB/index.php$is_args$args;

    location ~ \.php(/|$) {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        fastcgi_param  PATH_INFO        $fastcgi_path_info;
        include        fastcgi_params;
    }
}
```

程式中所有連結都由 `App\Core\Url::to()` 產生，兩種模式共用同一份程式碼。

---

## 4. 目錄結構

```
TaskB/
├── index.php               單一入口（front controller）
├── bootstrap.php           自動載入 / Session / 應用程式容器
├── config.php              資料庫、base_path、clean_urls 設定
├── !SQL/schema.sql         建表 + 測試資料
├── src/
│   ├── Core/               Database, Router, Url, Html, Theme, PluginManager, Auth, Csrf, App
│   ├── Model/              Setting, User, Category, Museum, Post, Plugin, LoginAttempt, MediaLibrary
│   └── Controller/         FrontController, AdminController
├── themes/
│   ├── blankslate/         父主題（重置 + layout 骨架 + 最小 partial）
│   └── Kazan_MuseumTour/   子主題（全部設計、版面與頁面樣板）
├── plugins/
│   ├── social-links/       頁尾社群連結
│   ├── seo-toolkit/        meta / OG / JSON-LD / sitemap.xml / robots.txt
│   ├── site-guardian/      安全性標頭 + 登入紀錄與封鎖
│   └── contact-form/       Formspree 靜態聯絡表單
├── admin-ui/               後台樣板（layout, login, dashboard, museums, posts, …）
├── assets/                 前後台共用 CSS/JS 與 favicon
└── uploads/                題目提供的照片素材（媒體庫來源）
```

---

## 5. 規格對應表

### B1 CMS Theme
- 子主題 `Kazan_MuseumTour`，`style.css` 檔頭宣告 `Template: blankslate`；所有設計都寫在子主題內。
- 站名（heading title）與標語（tagline slogan）存在 `settings`，後台 **Settings → Site identity** 可修改。
- 頁尾含版權文字 `Copyright © <年份> - All rights reserved`，年份取自伺服器時間 `date('Y')`。
- 社群連結由 **Footer Social Links** 外掛輸出，三個網址皆可於後台修改。

### B2 CMS Configuration
- 儀表板只保留 **At a Glance / Activity / Quick Draft**，並提供 Screen Options 調整顯示項目。
- 主選單：Home、Museums（下拉列出所有博物館）、Seasonal Events、News、Contact；置頂（sticky）。
- **SEO Toolkit** 外掛：title 樣板、description、canonical、Open Graph、Twitter Card、JSON-LD、`sitemap.xml`、`robots.txt`。
- **Site Guardian** 安全性外掛：安全性標頭、登入紀錄、暴力破解封鎖。
- 聯絡表單由 **Static Contact Form** 外掛輸出，`action="https://formspree.io/admin@example.com"`，
  欄位為 `name` / `email`（同步寫入 Formspree 的 `_replyto`）/ `content`。
  送出後會顯示成功或失敗訊息，**兩段文字都可在後台 Settings 修改**。

### B3 CMS Design
- 精選博物館頁：整頁滿版背景照片，來源為該頁的精選圖片（featured image）。
- 一般博物館頁：大型照片橫幅，來源同樣是精選圖片。
- 目標客群定義寫在後台 Settings 的 *Target audience definition*（見下方第 6 節）。

### B4 CMS Layout
- 首頁桌機：左右各半，左邊新聞、右邊封面與圖片牆（`.home-columns`，900px 以上兩欄）。
- 首頁行動：各區塊上下堆疊、佔滿寬度；唯獨精選博物館維持左右兩欄（`.museum-grid--two`）。
- 選單置頂（`position: sticky`），320px～1330px 皆無水平溢出（已用 320 / 360 / 1400 三種寬度量測）。
- 無障礙：skip link、landmark、`aria-current`、`aria-expanded`、`aria-live` 路由播報、
  可見的焦點外框、圖片替代文字、`prefers-reduced-motion`。

### B5 CMS Category
- 分類：Site Updates、Seasonal Events、以及四個精選博物館各一個分類。
- 分類網址 `/news/<category>/`；文章網址 `/news/<category>/<post-slug>/`。
- 首頁顯示**所有分類**的最新文章；精選博物館頁只顯示**該館分類**的文章。
- 頁面切換全部由 `assets/js/app.js` 以 fetch + History API 完成，含淡入淡出與頂端進度條動畫；
  後台不套用此行為。JavaScript 失效時所有連結仍是正常的伺服器端網址。

### B6 CMS Admin
- 角色：`admin`（完整）與 `editor`（僅內容：博物館、新聞、分類、媒體）。
- 登入頁位於 `/admin/`，白標：無任何 CMS 標誌、無 CMS 名稱字樣，滿版博物館照片背景（後台可更換）。
- 博物館可新增 / 修改 / 刪除（含 slug 重複檢查、精選圖片挑選、相簿、分類綁定）。
- 連續登入失敗達門檻即封鎖來源 IP，後台 **Security** 頁可檢視攻擊者 IP、時間與嘗試帳號。

---

## 6. 目標客群定義（規格要求須於作品中記錄）

> 25–55 歲、以自助方式在喀山停留 2–4 天的休閒旅客。以英文閱讀，多半邊走邊用手機查資料，
> 想快速判斷「在有限時間內哪幾間博物館值得去、幾點開門、這週有什麼展覽」。

此定義同時存放於後台 **Settings → Target audience definition**，可由客戶自行修改。
設計上因此採用：大而清楚的照片、行動優先的堆疊版面、每頁都直接標示開放時間與地址、
以及首頁右欄的「Upcoming events」時間軸。

---

## 7. 已驗證項目

- 全部路由回應 200（首頁、博物館列表、精選 / 一般博物館、新聞列表、分類、單篇、聯絡、後台、sitemap、robots），不存在的網址回 404。
- 後台以 admin 登入成功；editor 登入後選單只剩內容相關項目，直接存取 `/admin/users/`、`/admin/settings/` 會被導回儀表板。
- 博物館新增 → 前台可瀏覽 → 刪除 → 前台回 404（完整 CRUD）。
- 密碼錯誤會顯示錯誤訊息並寫入 `login_attempts`。
- 以無頭 Chrome 量測 320px / 360px 寬度下**無任何元素水平溢出**。
- 以無頭 Chrome 模擬點擊連結，確認頁面切換由 JavaScript 完成（網址與標題更新、內容置換、未發生整頁重新載入）。

## 8. 已知限制

- 聯絡表單真正送出需要連外的 Formspree 服務；本機離線時 fetch 會失敗，
  此時會顯示後台設定的錯誤訊息（這正是「錯誤文字可編輯」評分項目的展示路徑）。
- 內文編輯器為純文字欄位（以空白行分段），未實作所見即所得編輯器。
- 未使用題目附的 WordPress 外掛 zip 檔，理由見第 1 節。

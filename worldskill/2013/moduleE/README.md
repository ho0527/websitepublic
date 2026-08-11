# WorldSkills 2013 — Skill 17 — Module E（LVB Intermediate Lines）

依 `WSC2013_TP17_Module_E_actual_EN.pdf` 實作的 LVB（Leipziger Verkehrsbetriebe）
中間路線管理系統。使用題目提供的 **Yii 1.1.13** 框架，以 MVC 架構開發，
並沿用題目 `ui/` 樣板的 CSS 與圖片維持企業識別。

- 進入點：<http://127.0.0.1:83/worldskill/2013/moduleE/>
- 管理者帳號：`webmaster` / `leipzig`
- 畫面截圖：`docs/screenshots/`

---

## 1. 安裝

```bash
php C:\nginx\skill\worldskill\2013\moduleE\setup\install.php
```

腳本會：

1. 建立資料庫 `ws2013_lvb`（MySQL / MariaDB，`root`、空密碼）
2. 匯入題目提供的 `material/Data/db_lvb.sql`
   （原始 dump 為 latin1，匯入時轉成 UTF-8，德文字母才會正確顯示）
3. 依 `Routes Intermediate Lines.docx` 建立 5 條示範中間路線
   （T22、A33、A35、R87、N45），各含 7 個站點與對應車輛

也可以用瀏覽器開 `setup/install.php` 執行。

---

## 2. 功能對照

| 題目要求 | 實作位置 |
| --- | --- |
| 管理者 `webmaster` / `leipzig` 可登入 | `SiteController::actionLogin` + `UserIdentity`（密碼沿用資料庫的 md5） |
| 管理者可建立其他管理者 | `UserController` |
| 未登入時不顯示任何功能 | `Controller::accessRules()`；版型的主選單在未登入時為空 |
| 首頁含 header / 互動選單 / 內容 / footer | `views/layouts/main.php`，使用題目 `ui/` 的 CSS 與圖片 |
| Lines / Stations / Vehicles / Drivers 各自獨立的檢視 | `views/line`、`views/station`、`views/vehicle`、`views/driver` |
| 新增中間路線 | `LineController::actionCreate` |
| 修改 / 刪除中間路線 | `actionUpdate` / `actionDelete`；刪除時解除所有站點與車輛的關聯（`Line::beforeDelete`） |
| 一次指派 7 個站點（起站 + 5 中間站 + 終站） | `LineController::actionStations`，7 個欄位必須同時填妥才會存檔 |
| 一個站點只能屬於一條路線 | `validateStationSlots()` + 下拉選單只列出未指派的站點 |
| 一條路線最多 10 台車，且車種必須相同 | `LineController::actionVehicles` |
| 一台車只能屬於一條路線 | `Vehicle::available()` 只列出 `line_id = 0` 的車輛 |
| 一位司機只能有一台車，車種必須相符 | `Driver::validateVehicle()` + 表單以 JavaScript 依車種過濾車輛選單 |
| 路線圖顯示於路線檢視頁 | `views/line/view.php` |
| 上傳路線圖與司機大頭貼 | `UploadHelper`，存到 `uploads/maps` 與 `uploads/avatars` |
| Lines / Drivers 報表 | `views/line/index.php`、`views/driver/index.php` |
| 表單使用框架提供的方法（非純 HTML） | 全部使用 `CHtml::activeTextField` / `activeDropDownList` / `activeFileField` / `errorSummary` 等 |
| 可下載的 XML，且通過 XML Schema 驗證 | `XmlController` + `LvbXmlBuilder`（內容來自資料庫，畫面上會即時顯示驗證結果） |

### 2.1 XML

`XmlController::actionIndex` 每次都由資料庫重新產生 XML，並用題目提供的
`material/Data/lvb_system.xsd` 以 `DOMDocument::schemaValidate()` 驗證，
畫面上直接顯示結果。目前示範資料的產出為 **valid**。

- `xml/download` 下載 `lvb_system.xml`
- `xml/display` 以 `application/xml` 直接顯示

XSD 規定的順序（`code` → `type` → `start_time_operation` → `end_time_operation`
→ `count_vehicles` → `map` → `start_station?` → `end_station?` →
`intermediate_stations{0,5}` → `vehicles_line{0,10}`）在 `LvbXmlBuilder` 中嚴格遵守。

---

## 3. 檔案結構

```
moduleE/
├── index.php                     進入點，啟動 Yii 應用程式
├── setup/install.php             建立資料庫與示範資料
├── assets/                       題目 ui/ 樣板的 CSS 與圖片（+ module_e.css）
├── uploads/maps, uploads/avatars 上傳的路線圖與大頭貼
├── protected/
│   ├── config/main.php
│   ├── components/
│   │   ├── Controller.php        控制器基底（版型、麵包屑、Operations）
│   │   ├── UserIdentity.php      登入驗證
│   │   ├── UploadHelper.php      檔案上傳
│   │   ├── IsoDateValidator.php  日期驗證（取代 PHP 8 下會壞掉的 CDateValidator）
│   │   └── LvbXmlBuilder.php     XML 產生與 XSD 驗證
│   ├── controllers/              Site / Line / Station / Vehicle / Driver / User / Xml
│   ├── models/                   Line / Station / Vehicle / Driver / User / LoginForm
│   └── views/                    layouts + 每個元件一個資料夾
├── material/                     題目提供的素材（含 Yii 框架，未修改）
└── docs/screenshots/
```

---

## 4. 取捨與說明（2013 年題目 vs. 現在的環境）

### 4.1 框架：直接使用題目提供的 Yii 1.1.13

題目附了 CodeIgniter 2.1.3、CakePHP 2.3.6 與 Yii 1.1.13，而 `ui/` 樣板的
class 名稱（`span-19`、`span-5`、`portlet`、`breadcrumbs`、`errorSummary`）
與表單欄位命名（`Line[name]`、`ytLine_map`）都是 **Yii 1.1 產生的**，
因此選用 Yii。

實測 Yii 1.1.13 在 PHP 8.3.7 下可以正常啟動、使用 `CActiveRecord`、關聯查詢、
`CHtml` 表單輔助方法與 `CAccessControlFilter`。**框架原始碼完全沒有修改**，
只在兩個地方繞過 PHP 8 的不相容：

1. `index.php` 以 `error_reporting()` 關閉過時語法的通知（錯誤仍然會顯示）。
2. Yii 的 `date` 驗證器依賴 `CDateTimeParser`，該檔案使用 PHP 8 已移除的
   字串大括號索引語法（`$str{$i}`），一呼叫就會 Fatal error。因此自行撰寫
   `IsoDateValidator`（`CValidator` 子類別）取代，而非去改題目提供的框架。

### 4.2 網址

使用 Yii 的 `path` 格式搭配 `PATH_INFO`（`index.php/line/create`），
**不需要修改 nginx 設定**，也不需要 rewrite。

### 4.3 現代寫法

- 版型改用 HTML5 `<!DOCTYPE html>`（題目樣板為 XHTML 1.0 Transitional），
  但 class 結構、CSS 與圖片完全沿用，視覺與題目樣板一致。
- 表單的日期欄位使用 `CHtml::activeDateField`（HTML5 `type="date"`），
  取代 2013 年的 jQuery UI datepicker，避免引用需要連外的資源。
- 司機表單的車輛選單以少量原生 JavaScript 依車種即時過濾；
  **沒有引用任何 CDN**，而且伺服器端仍然會再驗證一次車種是否相符。
- 站點的下拉選單只列出「未指派」或「本路線目前使用中」的站點，
  從介面上就避免違反「一個站點只能屬於一條路線」。

### 4.4 資料庫維持題目原樣

`driver` / `line` / `station` / `user` / `vehicle` 五張表的欄位完全沿用
題目的 `db_lvb.sql`，未新增欄位。`station.position_station` 以
`START` / `INTER` / `END` 表示站點在路線中的位置，`line_id = 0` 代表未指派。
密碼沿用 md5（題目資料庫即為 `md5('leipzig')`）；正式產品應改用
`password_hash()`，此處為了與題目資料相容而保留。

### 4.5 工作階段

`protected/config/main.php` 設定工作階段有效期 1 小時（`LVBSESSID`），
讓關閉瀏覽器後短時間內仍維持登入；`allowAutoLogin` 為 `false`，不使用永久 cookie。

---

## 5. 已驗證的行為

```bash
php C:\nginx\skill\worldskill\2013\moduleE\tests\functional_test.php
```

`tests/functional_test.php` 會以真正的 HTTP 請求操作整個應用程式
（登入 → 建立站點／車輛／路線 → 指派 → 驗證錯誤情境 → XML → 刪除），
再直接查資料庫確認伺服器端的實際狀態，最後把自己建立的資料清乾淨，
因此可以重複執行。最新結果：**PASSED: 44 / FAILED: 0**（見 `docs/test-results.txt`）。

涵蓋的行為：

| 測試 | 結果 |
| --- | --- |
| `webmaster` / `leipzig` 登入 | 成功導向首頁 |
| 未登入存取 `line/index` | 導向登入頁 |
| 建立路線（含上傳 SVG 路線圖） | 成功，導向站點指派頁 |
| 只選 2 個站點就送出 | 拒絕：「All 7 stations must be selected at the same time」 |
| 指派 7 個站點 | 成功 |
| 指派已屬於其他路線的站點 | 拒絕：「already belongs to another Intermediate Line」 |
| 同一站點重複選取 | 拒絕：「Each station can be used only once in a line」 |
| 指派 11 台車 | 拒絕：「maximum of 10 vehicles」 |
| 把 Tram 車輛指派給 Autobus 路線 | 拒絕：「cannot run on a Autobus line」 |
| 指派車種相符的車輛 | 成功；該車不再出現在其他路線的可選清單 |
| 建立司機（含上傳大頭貼） | 成功，檔案出現在 `uploads/avatars/` |
| Tram 司機指派給 Autobus 車輛 | 拒絕：「A Tram driver can only be assigned to a Tram」 |
| 不存在的日期（1990-13-45） | 拒絕 |
| 建立第二個管理者並登入 | 成功，密碼以 md5 儲存 |
| 修改路線 | 營運時間更新成功 |
| 刪除路線 | 其站點與車輛的 `line_id` 全部歸零 |
| 刪除車輛 | 其司機的 `vehicle_id` 歸零 |
| XML 對 `lvb_system.xsd` 驗證 | valid，且內容含新建立的路線／車輛／司機 |

---

## 6. 已知限制

- 清單頁未做分頁與排序（資料量小，一次列出）。
- 沒有 CSRF token（Yii 1.1 的 `enableCsrfValidation` 未開啟），
  正式環境應開啟。
- 站點只能整批 7 個一起指派，這是題目的明確要求
  （「Stations can only be added at the same time」），
  因此站點的編輯頁不提供單獨改變所屬路線的功能，只提供「Remove from line」。

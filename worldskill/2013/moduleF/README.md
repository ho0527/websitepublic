# WorldSkills 2013 — Skill 17 — Module F（WorldSkills Statistics）

依 `WSC2013_TP17_Module_F_actual_EN.pdf` 實作的互動式統計網站：由中央
Web Service 取得歷屆競賽資料，產生「各國歷年獎牌表」與「各職類歷年成績折線圖」。

- 進入點：<http://127.0.0.1:83/worldskill/2013/moduleF/>
- 中央 Web Service：<http://127.0.0.1:83/worldskill/2013/moduleF/service/>
- 測試結果：`docs/test-results.txt`
- 畫面截圖：`docs/screenshots/`

---

## 1. 功能

### 1.1 Medals by country over the years（表格）

- 下拉選單列出所有國家，格式為 `ISO - 國名`，依 ISO 代碼排序。
- 尚未選擇國家前表格不存在（初始為空），選擇後才於伺服器端產生。
- 一列一個職類，欄位為 `職類編號 - 職類名稱` 與 2007 / 2009 / 2011 三個年度的獎項。
- 依職類編號排序（純數字在前依數值大小，`D1`、`HM1` 等代碼排在後面）。
- 列與列交錯底色，與題目樣板一致。
- 原始資料的獎項大小寫不一致（`Medallion For Excellence` / `Medallion for Excellence`），
  在 `StatisticsRepository::awardLabel()` 統一標準化。

### 1.2 Performance by trade over the years（折線圖）

- 兩個下拉選單：職類（依編號排序）與國家（含 `All countrys`）。
- 圖片由 `chart.php` 以 **GD 於伺服器端動態產生 PNG**，未使用題目提供的樣板圖片。
- 圖上包含：
  - 具刻度標籤的 X / Y 軸與水平格線（Y 軸範圍依實際資料自動計算）
  - 每個國家一條顏色不同的折線
  - 每個資料點都有符號（菱形／方形／三角形／圓形／叉形；顏色與符號組合最多 100 種不重複）
  - 折線末端直接標註該國 ISO 代碼，即使線很多也能立刻分辨
  - 綠色三角形的 **Average** 折線（該職類全部國家的年度平均，與是否篩選國家無關）
  - 圖例，每列為「線條 + 符號 + `ISO - 國名`」
- 篩選組合：`一個職類 + 全部國家`、`一個職類 + 一個國家` 都支援。

### 1.3 錯誤處理

| 情況 | 行為 |
| --- | --- |
| 未選國家 / 職類就按 Show | 表單旁顯示紅色訊息，畫面其餘部分照常 |
| 選到資料中不存在的職類或國家 | 圖片改為一張說明錯誤原因的圖 |
| 該篩選條件沒有任何成績 | 圖上顯示「No results available for this selection.」 |
| Web Service 無法使用 | 顯示黃色警告，自動改讀中央伺服器的純 XML 檔 |
| 資料檔損毀 / 不存在 | 顯示可讀的錯誤訊息，不會出現 PHP 例外或空白頁 |

---

## 2. 檔案結構

```
moduleF/
├── index.php                  前端進入點：處理表單、取資料、交給樣板
├── chart.php                  動態產生折線圖（PNG）
├── assets/style.css           樣式（沿用題目樣板的 .ok / .error）
├── views/page.php             畫面樣板
├── lib/
│   ├── bootstrap.php          共用載入
│   ├── Config.php             設定與傳輸方式選擇
│   ├── Transport.php          SOAP 傳輸層（HTTP / 同行程）
│   ├── SoapClientLite.php     手寫 SOAP 1.1 用戶端
│   ├── StatisticsGateway.php  取得國家 / 職類 / 成績（含備援與錯誤處理）
│   ├── StatisticsRepository.php  聚合與排序
│   └── LineChart.php          GD 折線圖繪製
├── service/                   模擬題目的中央伺服器
│   ├── WSC_Statistics.php     HTTP 進入點（?wsdl 與 SOAP POST）
│   ├── SoapEndpoint.php       SOAP 請求處理核心
│   ├── StatisticsDataSource.php  讀取 data/ 下的 XML
│   ├── WSC_Statistics.wsdl    題目提供的服務描述檔
│   ├── serve.bat              以獨立行程啟動服務（port 8017）
│   └── data/*.xml             題目提供的原始資料
├── tests/run_tests.php        驗證腳本
├── docs/test-results.txt      測試輸出
└── docs/screenshots/          畫面截圖
```

---

## 3. 取捨與說明（2013 年題目 vs. 現在的環境）

### 3.1 沒有 `ext-soap`，因此 SOAP 完全手寫

題目原本的中央服務用 `SoapServer`，選手則用 `SoapClient`。本機 PHP 8.3.7
**沒有安裝 soap 擴充**。這其實與題目要求一致——Module F 明文規定
「Server-side libraries may NOT be used in this module - the code needs to be
written from scratch and by hand」。因此：

- `lib/SoapClientLite.php`：自行組出 SOAP 1.1 (rpc/encoded) 請求信封、
  設定 `SOAPAction` 標頭、以 DOM 解析回應、把 `SOAP-ENV:Fault` 轉成例外。
- `service/SoapEndpoint.php`：自行解析請求信封、派送操作、產生回應信封。
  回應格式與題目提供的 `WSC-CountrysSOAP.xml` 範例、以及 `WSC_Statistics.wsdl`
  的型別定義完全一致（`getList(listType)`、`getResults()`）。
- `?wsdl` 仍會回傳題目提供的 WSDL，並把 `soap:address` 換成實際位址。

### 3.2 中央伺服器改架在本機

題目的 `http://vhost32.skill17.local/Module_F/` 已不存在，因此把題目附的
`WSC-*.xml`、`WSC_Statistics.wsdl` 放進 `service/`，在本機重建同樣的服務。
**評分時只要修改 `service/data/*.xml`，前端立即會呈現新的資料**
（每次請求都重新讀取，沒有任何快取），`tests/run_tests.php` 第 5 節有自動驗證。

### 3.3 兩種 SOAP 傳輸方式

本機 nginx 只掛了 **一個** php-cgi 工作行程。若前端 PHP 再對同一台伺服器
發 HTTP 請求，會因為沒有空閒的工作行程而互相等待到逾時（實測整頁 30 秒才回應）。
因此 `Config::createTransport()` 會先偵測 `127.0.0.1:8017`：

- **有在監聽** → 使用 `HttpTransport`，發出真正的 HTTP SOAP POST。
  執行 `service/serve.bat` 即可啟動（PHP 內建伺服器，獨立行程）。
- **沒有監聽** → 使用 `LoopbackTransport`，在同一個行程內把 SOAP 信封交給
  `SoapEndpoint` 處理。

兩者送出與解析的 SOAP 信封完全相同，資料一樣是每次請求即時讀取；差別只在
傳輸方式。`docs/screenshots/` 中的截圖是在 `serve.bat` 執行中（HTTP 模式）拍的，
畫面下方的「Data source」會顯示目前實際使用的方式。

### 3.4 其他

- **不使用任何需要連外的 CDN**，圖表以 GD 繪製，沒有 JavaScript 圖表函式庫。
- 圖表文字優先使用系統的 `arial.ttf`；找不到時自動退回 GD 內建點陣字型，
  不會因為缺字型而失敗（`LineChart::locateFont()`）。
- 原始資料的 `WSC-Skills.xml` 有重複的職類編號（例如 `D1` 出現兩次），
  下拉選單只保留第一筆。
- 成績資料中有些國家（例如 `OM`）不在 `WSC-Countrys.xml` 內，
  圖例會標示為「OM - country not listed」而非讓程式出錯。

---

## 4. 執行與驗證

```bash
# 前端
start http://127.0.0.1:83/worldskill/2013/moduleF/

# 可選：以獨立行程啟動中央服務，讓前端改走真正的 HTTP SOAP
C:\nginx\skill\worldskill\2013\moduleF\service\serve.bat

# 驗證（44 項檢核）
php C:\nginx\skill\worldskill\2013\moduleF\tests\run_tests.php
```

`tests/run_tests.php` 會用「直接解析 XML 的第二套獨立實作」重新算一次答案，
再與經過 SOAP + 聚合後的結果比對，涵蓋：

1. SOAP 往返（2491 筆成績逐筆比對）
2. 下拉選單內容與排序
3. 獎牌表數值、排序、未知國家
4. 折線圖各國數值與年度平均（獨立重算比對）
5. 中央資料異動（修改分數、新增一筆）是否即時反映
6. 錯誤處理（未知操作、格式錯誤、連不上服務、資料檔損毀）

最新結果：**PASSED: 44 / FAILED: 0**（見 `docs/test-results.txt`）。

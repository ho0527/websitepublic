# WorldSkills 2019 TP17 – Web Technologies「PHP and JS」第二階段（Task E）

參加者前端（HTML / CSS / 原生 JavaScript SPA），
對應題目 `WSC2019_TP17_PHP_and_JS_actual.pdf` 的 **Phase two – JavaScript**（規格 C1 ~ C5）。

網址：<http://127.0.0.1:83/worldskill/2019/TaskE/>

> **關於這個資料夾原本的內容**
> `TaskE/` 原本放的是另一個測試專案（模組 E「ISP / Knowledge Explorer」）的簡報路線編輯器，
> 與「PHP and JS」模組無關。原始檔案已完整保留在 `isp-legacy/`（`index.html`、`index.css`、`index.js`），
> 題目素材 `media/` 也原封未動，沒有刪除任何東西。

---

## 一、資料來源

本前端所有資料都來自 Task C 實作的參加者 REST API：

```js
// js/api.js
var API_BASE = '../TaskC/api/v1/index.php';
```

因為沒有修改 nginx.conf，API 使用等效的 PATH_INFO 網址。
若之後在 nginx 加上 Task C README 內的 rewrite 片段，把 `API_BASE` 改成 `'../TaskC/api/v1'` 即可。

啟動前請先依 `../TaskC/README.md` 建好資料庫。

測試用登入資訊（取自資料庫 dump）：

| Lastname | Registration Code |
| --- | --- |
| Yakovich | 35DGZX |
| Darthe | UP243M |

---

## 二、路由

採用 hash 路由，**所有畫面狀態都保存在網址中**，重新整理或把網址分享給別人都會看到同一個畫面，
而且不需要修改 nginx 設定就能支援深層連結：

| 網址 | 畫面 | 規格 |
| --- | --- | --- |
| `#/` | 即將舉行的活動列表 | C1a |
| `#/events/{organizer}/{event}` | 活動議程（頻道／房間泳道） | C2a ~ C2c |
| `#/events/{organizer}/{event}/sessions/{id}` | 議程詳細 | C3a |
| `#/events/{organizer}/{event}/register` | 報名購票（未登入會先導向登入頁） | C4a ~ C4e |
| `#/login?next=…` | 參加者登入 | C5a ~ C5c |
| 其他 | 錯誤頁 | — |

若要改用 History API 的乾淨網址（例如 `/TaskE/events/demo1/wsc-2019`），
需要在 nginx 加入下列片段讓所有路徑回到 `index.html`：

```nginx
location ^~ /worldskill/2019/TaskE/ {
    try_files $uri $uri/ /worldskill/2019/TaskE/index.html;
}
```

---

## 三、規格對照

| 規格 | 實作方式 |
| --- | --- |
| C1a | 活動依日期由 API 排序後列出，每筆是 `a.event`，顯示主辦者名稱與日期 |
| C2a | 頻道為橫向泳道（`.channel`），內含房間子泳道（`.row` + `.room`） |
| C2b | 時間軸固定 09:00–17:00，顯示 9:00 / 11:00 / 13:00 / 15:00 刻度；議程方塊依起訖時間定位 |
| C2c | 已報名該活動時，所有 `talk` 以及自己加購的 `workshop` 會加上 `.registered`（綠色外框） |
| C3a | 標題顯示「Title - Type」，並列出說明、講者、開始、結束，有費用時才顯示 Cost |
| C4a | `.ticket` 卡片、`.workshop` 勾選清單、`#event-cost` / `#additional-cost` / `#total-cost` 費用摘要、預設停用的 `#purchase` |
| C4b/C4c | 未販售的票券加上 `unavailable` 並 `disabled`，無法選取，購買鈕維持停用（可購買性由 API 決定） |
| C4d | 勾選工作坊後即時把費用加入「Additional workshops」與總計 |
| C4e | 送出報名後回到議程頁並顯示「Registration successful」 |
| C5a | 登入成功後回到先前的頁面（`?next=`），頁首改顯示使用者名稱與 Logout |
| C5b | 失敗時停留在登入頁並顯示「Lastname or registration code not correct」 |
| C5c | 登出後導向登入頁，且使用 `location.replace()`，按上一頁回不到原本的頁面 |

必要的選擇器（`class: event / channel / room / row / session / registered / ticket / workshop`、
`id: register / event-cost / additional-cost / total-cost / purchase / lastname / registration_code / login`）
都依照 mockup 的黃色標註實作。

---

## 四、檔案結構

```
TaskE/
├── index.html          單一入口
├── css/style.css       版面與樣式（最小視窗寬度 1024px）
├── js/api.js           REST API 用戶端（token 存於 localStorage）
├── js/app.js           路由與各畫面渲染
├── media/              題目提供的素材（原封保留）
└── isp-legacy/         原本放在此資料夾的模組 E（ISP）程式，完整保留
```

沒有使用任何外部 CDN 或建置工具，直接以靜態檔案提供服務即可。

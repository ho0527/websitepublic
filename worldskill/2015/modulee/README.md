# WorldSkills 2015 — Client Side A（Custom Puzzle）

WSC2015_TP17 Web Design 職類，Module E「Client Side A」的實作。

- 進入點：<http://127.0.0.1:83/worldskill/2015/modulee/>
- 對應題目：`WSC2015_TP17_ClientSideA_actual.pdf`（Version 3.1）
- 素材來源：`WSC2015_TP17_resources_clientside/Marcelo - Client Side/Client Side  A - 3hrs - Puzzle/Media`

---

## 一、檔案結構

```
modulee/
├─ index.html              進入點（沿用主辦提供的版面骨架）
├─ css/main.css            上半段＝提供的 main.css，下半段＝本次追加樣式
├─ js/
│  ├─ imageslicer.js       ImageSlicer：讀檔、JPG 驗證、置中裁切、切片
│  ├─ timer.js             GameTimer：mm:ss 計時、暫停 / 繼續
│  ├─ storage.js           GameStorage：localStorage 存讀
│  ├─ ranking.js           RankingService：與後端 API 溝通 + 表格渲染
│  ├─ piece.js             Piece：單一碎片（角度、座標、狀態）
│  ├─ puzzle.js            Puzzle：盤面（洗牌、選取、旋轉、拖放判定、完成偵測）
│  └─ app.js               PuzzleApp：整體流程控制器
├─ api/
│  ├─ db.php               PDO 連線 + 資料表自動建立
│  └─ ranking.php          成績寫入與排行查詢（JSON）
├─ imgs/                   主辦提供的示範圖
├─ sql/competitorXX_db01.sql  主辦提供的原始 SQL
└─ lib/                    主辦提供的 jQuery / jQuery UI 本機版（本專案未使用，保留備查）
```

## 二、資料庫

- 資料庫名稱：**`worldskill2015_modulee`**（MySQL/MariaDB `127.0.0.1:3306`，帳號 `root`、密碼空）
- 結構完全比照主辦提供的 `competitorXX_db01.sql`：
  - `difficult(id, name)`：1=EASY、2=MEDIUM、3=HARD
  - `ranking(id, name, difficult_id, time)`，`difficult_id` 外鍵指向 `difficult`
- `api/db.php` 在第一次呼叫時會**自動建立資料庫、資料表與難度基本資料**，不需要手動匯入。
  （原始 SQL 使用的資料庫名 `competitorXX_db01` 帶有 `XX` 佔位符，因此改用上述名稱，欄位與約束不變。）

### API

| 方法 | 路徑 | 說明 |
| --- | --- | --- |
| `POST` | `api/ranking.php` | body: `{name, difficult_id, seconds}`，寫入成績並回傳該難度排行 |
| `GET` | `api/ranking.php?difficult_id=1[&id=12]` | 只查詢排行 |

回傳 `rows` 已經是「前三名 + 目前玩家」的顯示清單；名次採**同時間同名次**（1,1,3,…），
若目前玩家已在前三名則不會重複附加。

## 三、需求對照

| 題目 | 實作方式 |
| --- | --- |
| 1. 開始視窗（姓名 / 難度 / 圖片 / START） | `#start` 模態視窗 |
| 1(b) 難度 4 / 9 / 16 片 | EASY=2×2、MEDIUM=3×3、HARD=4×4 |
| 1(c) 拖放區＋預覽 | `#drop` 支援 HTML5 drag & drop，也可點擊開檔；只接受 JPG |
| 2. 欄位必填、友善錯誤訊息、圖片不上傳 | 三個欄位各自的錯誤訊息（含晃動動畫）；圖片只用 `FileReader` + Canvas，**完全不上傳** |
| 3. 玩家名稱 / 計時器 / PAUSE / RESTART | `#playername`、`#timer`、`#pauseButton`、`#restartButton` |
| 4. 正方形置中裁切 | `ImageSlicer.cropCenteredSquare()`，取短邊、左右（或上下）各裁一半 |
| 5. 依難度切片 | `ImageSlicer.slice()` 以 Canvas 切成 n×n |
| 6. 隨機位置 + 隨機旋轉 | 起始區使用同樣的 n×n 格點，格位以 Fisher–Yates 洗牌，角度隨機 0/90/180/270 → **保證不重疊** |
| 7. 計時 mm:ss | `GameTimer.format()` |
| 8. 暫停 | 時間停止、兩個區域 `visibility:hidden`、按鈕文字改 RESUME、暫停期間所有互動被擋掉 |
| 9. 點選、抬升、可旋轉可拖曳 | 點選加 `.selected`（`scale(1.12)` + 陰影 + `z-index`，有轉場動畫） |
| 10 / 11. ← → 旋轉 90° | 角度以累加值儲存，動畫方向正確（不會出現 270°→0° 的倒轉） |
| 12. 位置或角度錯誤 → 退回原位 | 以動畫飛回原座標並還原拖曳前的角度，同時取消選取 |
| 13. 正確 → 吸附 + 顯色動畫 | 碎片平時 `filter: saturate(.45)`，放對時播放 `revealColor` 顯色動畫，該格半透明預覽 `opacity → 0` |
| 14. 抬升 / 旋轉皆有動畫 | CSS transition + keyframes |
| 15. localStorage 保存 | 存 `{name, difficultId, grid, image, elapsed, paused, pieces[]}`；重新整理後位置、角度、已完成碎片、經過時間都會還原 |
| 16. 完成時停錶 | `Puzzle.solved` → `timer.stop()` |
| 17. 寫入 MySQL | `POST api/ranking.php`（姓名、難度、耗時） |
| 18. 結果視窗 + 動畫 | `#end` 使用 `modalIn` 彈入動畫 |
| 19. 排行榜 | 前三名 + 目前玩家名次，並列同名次 |
| 20. RESTART APPLICATION | 關閉結果視窗、回到開始視窗 |
| 21. OOP | 7 個獨立類別（見檔案結構），各司其職 |
| 22. Console 無錯誤 | 自動化測試已檢查 |

## 四、實作取捨（2015 → 現代寫法）

1. **不使用 jQuery / jQuery UI。** 題目說「可以使用」而非必須。改用原生
   `class`、`Pointer Events`、`CSS transform / keyframes`，行為與視覺相同，
   且沒有 jQuery UI Draggable 在 `transform: rotate()` 元素上座標偏移的老問題。
   主辦提供的本機版 jQuery 仍保留在 `lib/`。
2. **拖曳採用 Pointer Events 而非 HTML5 Drag and Drop。**
   HTML5 DnD 的拖曳影像無法呈現旋轉後的碎片，也難以做「飛回原位」的動畫。
   Pointer Events 同時支援滑鼠與觸控。
3. **切片使用 Canvas 而非 CSS `background-position`。**
   碎片需要獨立旋轉，Canvas 產生的獨立圖片最單純；同時裁切後重新編碼為
   500×500 JPEG（約 60–100 KB），才能安全放進 localStorage。
4. **起始區採用格點配置。** 題目要求「隨機位置且不重疊」，
   在 500×500 的區域內要塞下 16 片 125px 的碎片，唯一不重疊的排法就是 4×4 格點，
   因此以「格位洗牌」達成隨機性（HARD 時尤其明顯）。
5. **後端加了伺服器端驗證**（姓名長度、難度值、秒數範圍）與 PDO 預備語句，避免 SQL Injection。
6. **`api/db.php` 會自動建表**，方便在任何機器直接啟動；正式比賽時可改為手動匯入 SQL。
7. 版面在 1200px 以下改用 flex 排列並隱藏側邊縮圖，避免橫向捲動（原始版面是固定 1140px）。

## 五、已知限制

- 題目指定的評分瀏覽器是 Firefox；本次僅在 Chrome（含 headless）驗證。
  所用 API（Pointer Events、`FileReader`、Canvas、CSS 動畫）在 Firefox 皆為標準支援。
- localStorage 只保存「一局」進度；同時開多個分頁會互相覆蓋。
- 圖片非常大時（例如 6000×4000），裁切需要約 0.5 秒，期間沒有載入指示。

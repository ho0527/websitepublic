# WorldSkills 2013 – Module H（WorldSkills 版型互動）

網址：<http://127.0.0.1:83/worldskill/2013/moduleh/>

## 檔案

| 檔案 | 說明 |
| --- | --- |
| `index.html` | 交件主頁（沿用既有檔案，改寫 head／導覽／輪播／結果區外框，368 筆得獎資料原封保留） |
| `index.css` | 全部重寫 |
| `index.js` | 全部重寫，純原生 JavaScript（題目規定不得使用任何 JS 函式庫） |
| `material/Layout/i/` | 題目素材（3 張輪播圖、about.png / 41.png / 40.png、手勢標誌、logo、3 張照片） |
| `material/picture/Animation Path.jpg` | 題目提供的手勢標誌動線圖 |

## 接手前的狀態

原本已有 `index.html` / `index.css` / `index.js` 三個檔，完成度大約 3 成：

- 有題目提供的版型與**完整的 368 筆得獎名單資料**（兩屆競賽、92 個職類）。
- 有很陽春的輪播（直接抽換 `img.src`，沒有動畫，間隔是 1.5 秒不是 5 秒）與 Continue reading（高度直接跳成 100%）。
- 導覽的平滑捲動 `smoothscroll()` 來自站外檔案 `/chrisplugin/js/chrisplugin.js`，模組本身無法獨立運作。
- **完全沒有**：手勢標誌動線、區塊背景圖與視差、自製按鈕的三種狀態、結果區的新版檢視與所有搜尋功能。

## 這次補上的內容

1. **導覽**：4 顆自製按鈕，normal / hover-focus / active 三種樣式，另加「目前所在區塊」高亮；點擊以自製 easing 平滑捲動到對應區塊（移除對站外 script 的依賴）。
2. **輪播**：3 張圖淡入 + 位移動畫，每 **5 秒**自動換下一張並循環；左右各一顆自製按鈕（三種狀態）可上一張 / 下一張，另加頁碼圓點；手動切換後重新計時。輪播圖片本身未做任何修改。
3. **區塊背景**：`about.png` / `41.png` / `40.png` 以 `::before` 疊在三個區塊上（淡化 18%），捲動時以 `transform: translateY()` 產生視差位移。
4. **Continue reading**：以 `max-height` 轉場做 slide-down，可再次收合（按鈕文字切換為 Show less），三個區塊皆適用。
5. **手勢標誌**：`position: fixed`，永遠停在畫面上；捲動或點擊導覽到 About / 41st / 40th 時，依 `Animation Path.jpg` 分別移動到 A（右側）、B（左側）、C（右側）；點擊標誌平滑回到頁首；定位點以視窗寬高百分比計算，任何解析度都不會跑出畫面。
6. **結果區改為新版檢視**：以 JavaScript 讀取原始巢狀清單，重新輸出成卡片式列表，一列同時顯示題目要求的 6 個欄位：**競賽年份與主辦國、職類編號、職類名稱、獎牌、選手姓名、選手國家**，並依年份分組、依獎牌上色。
7. **搜尋功能**：
   - 邊打字邊即時篩選（不需按鈕或 Enter），右上顯示命中數 / 總數。
   - 以 `+` 串接多個關鍵字時採 **AND** 條件。
   - **Case Sensitive** 核取方塊，勾選後大小寫視為不同（搜尋與自動完成都遵守）。
   - **自動完成**清單最多 5 筆，開頭相符者優先；點選後填入目前這一段關鍵字。
   - 點擊清單中的姓名／國家／職類／獎牌即以該值搜尋；**按住 SHIFT 點擊**則以 `+` 追加（並抑制文字反白）。
   - 查無資料時顯示 `There is no result for keyword "…"`。
   - 搜尋框有焦點時，手勢標誌移到 Results 標題左側並**鎖定不動**（捲動也不會跑掉）。
   - 在搜尋框按 **CTRL + S** 會把搜尋字串存入 `localStorage`，重開瀏覽器後自動還原並套用。
8. 順手修掉原始資料最後一個 `<li>` 少一個結束標籤的問題，並在解析時略過資料中一筆空白的分隔項目（368 筆得獎紀錄）。

## 現代寫法的取捨

- 題目寫於 2013 年，動畫允許 JavaScript 或 CSS3；本模組**動畫一律用 CSS3 transition**，只由 JS 切換 class 與定位，效能與流暢度比當年逐格 `setInterval` 好，視覺效果相同。
- 平滑捲動用 `requestAnimationFrame` + easeInOutCubic 自行實作（不用 `scroll-behavior`，才能與手勢標誌動線同步）。
- 版面用 Flexbox 取代當年的 float hack，但保留原版型 866px 內容寬度與照片旋轉、白框樣式。
- 未引用任何 CDN 或函式庫，完全離線可用（也符合題目「不得使用 JavaScript library」）。

## 驗證（headless Chrome 1400×1000）

- 首頁：導覽按鈕、輪播框、手勢標誌起點 ✔
- About 展開全文：Continue reading → Show less ✔
- 41st 區塊：背景 UK 國旗視差、手勢標誌移到 B 點（left=292px, top=362px, class=at-b）✔
- 結果區：368 / 368 筆、依年份分組、6 欄位齊全 ✔
- `GOLD+Web Design` → 2 筆（AND 條件）；`gold+web design` 不勾選大小寫 → 2 筆、勾選 → 0 筆 ✔
- 輸入 `web` → 自動完成 3 筆（上限 5）；輸入 `Ge` → 5 筆 ✔
- SHIFT + 點擊職類 → 搜尋框變成 `Japan+Autobody Repair` 且結果同步更新 ✔
- 一般點擊國家 → 搜尋框變成 `Japan`、38 筆 ✔
- CTRL + S → `localStorage` 存入搜尋字串 ✔
- 搜尋框取得焦點 → 手勢標誌移到 Results 標題左側（left=203px，標題 x=251）✔
- Console 無 JavaScript 錯誤 ✔

## 尚未處理 / 不確定

- headless Chrome 捲動後截圖會出現空白（瀏覽器的合成問題），因此 A、C 兩個定位點與輪播動畫是以「隱藏其他區塊 + 關閉 transition」的方式間接驗證，未取得完整捲動過程的連續截圖；請在一般瀏覽器中實際捲動確認動線是否符合喜好。
- 手勢標誌 A / B / C 的座標是依動線圖比例自訂的，題目並未給精確數值。
- 得獎資料中「NATÃ BARBOSA」等字串在原始素材即為亂碼，未擅自更動資料。

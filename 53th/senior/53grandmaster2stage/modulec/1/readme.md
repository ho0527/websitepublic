# 模組 C 子項目 1 － Star Battle

第 47 屆國際技能競賽第 2 階段國手選拔賽（53 屆國手選拔二階）模組 C 的第 1 小題，
對應正式試題的「項目 2：Star Battle - DESIGN AND INITIAL LAYOUT」與「項目 3：Star Battle - GAME FUNCTIONALITIES」。

## 執行網址

```
http://127.0.0.1:83/53th/senior/53grandmaster2stage/modulec/1/
```

遊戲解析度為 **960 x 600**（平板），在較大的畫面上會水平與垂直置中。

## 檔案結構

```
modulec/1/
├── index.html                     單一頁面，內含三個畫面（遊戲說明 / 遊戲畫面 / 排行榜）
├── css/style.css                  全部版面與動畫樣式
├── js/
│   ├── audio.js                   音效管理（背景音樂、射擊、爆炸、整體開關）
│   ├── ranking.js                 排行榜：AJAX 送出成績、前端排序與繪製
│   ├── game.js                    遊戲引擎：實體、碰撞、燃料、計時、難度、視差
│   └── main.js                    畫面切換、按鈕與鍵盤事件、感應區、字級調整
├── php/
│   ├── register.php               接收 name / time / score，回傳未排序的排行榜 JSON
│   └── ranking.sql                排行榜資料表（register.php 也會自動建立）
├── design/                        三個畫面的設計稿（960 x 600 PNG）
│   ├── design-01-game-instructions.png
│   ├── design-02-game-board.png
│   └── design-03-ranking-table.png
└── material/                      提供的素材（另外補上 ripple/ripple.gif 參考檔）
```

## 三個畫面

1. **遊戲說明**：logo、12 條遊戲規則（逐條淡入的動畫）、標語與 Start Game 按鈕。
2. **遊戲畫面**：資訊列（logo、計時器、分數、燃料數字＋圖形化長條、字級大小、音效、暫停按鈕）、
   遊戲場地與十字感應區。
3. **排行榜**：logo、排行榜表格（position、name、score、time，依此順序）與 Start Game 按鈕。

## 試題要求對照

| 要求 | 實作方式 |
| --- | --- |
| 15 項元素 | 主飛船、背景行星、敵方飛船、小行星、友方飛船、燃料圖示、燃料計數器、分數計數器、計時器、音效按鈕、字級按鈕、暫停按鈕、感應區、logo、子彈，皆呈現於遊戲畫面 |
| Start Game 按鈕 hover | `.btn:hover` 背景色為 `#f19e0d` |
| Start Game 按鈕 active | `.btn:active::after` 以 `@keyframes ripple` 呈現水波紋（參考 `material/ripple/ripple.gif`） |
| 遊戲說明動畫 | 每一條規則以不同延遲淡入（`instruction-in`），logo 由上方彈入 |
| 燃料 | 起始 15 點、上限 30 點、每秒 −1；碰撞或被擊中 −15；收集燃料 +15；歸零即結束 |
| 燃料計數器動畫 | 數字＋長條，增加時亮起、減少時變紅並震動，長條寬度以 transition 平滑變化 |
| 計時器 / 分數 | 由 0 開始；分數可為負值（負值以警示色顯示） |
| 射擊 | 空白鍵射擊，需放開才能再次發射；一發子彈只能摧毀一個目標（不會穿透） |
| 感應區 | 十字分佈，`mouseenter` 啟動、`mouseleave` 停止，飛船不能飛出畫面 |
| 敵我判定 | 敵方飛船 1 發（+5）、小行星 2 發（+10，第一發顯示受損）、友方飛船（−10） |
| 敵方射擊 | 敵方飛船由右往左發射子彈，被擊中燃料 −15 |
| 元素動畫 | 敵我飛船使用素材的四格連續圖以 `steps(4)` 播放；小行星自轉；燃料左右擺動並由上往下掉落 |
| 難度 | 每 5 秒提升一級（最高 10 級）：出現間隔變短、移動變快、敵方開火更頻繁 |
| 視差 | 8 顆行星大小不同，尺寸越大速度越快 |
| 暫停 | 暫停按鈕或鍵盤 `P`；暫停時所有動畫（`animation-play-state: paused`）、音效、互動與計時全部停止，再按一次從原處繼續 |
| 遊戲結束 | 顯示 Name 欄位與 Continue 按鈕，Name 未填寫前 Continue 停用 |
| 送出成績 | `fetch` POST 至 `php/register.php`，欄位為 `name` / `time` / `score` |
| 排行榜排序 | 伺服器回傳未排序資料，前端依「分數大到小、分數相同再比時間大到小」排序；分數與時間都相同者共用名次 |
| 重新開始 | 排行榜的 Start Game 會回到遊戲說明畫面 |
| 音效 | `background.mp3` 飛行中循環、`destroy.m4a` 摧毀、`shoot.mp3` 射擊；音效按鈕可整體開關 |
| 無障礙 | 字級放大／縮小按鈕會改變計時器與分數的字級（CSS 變數 `--hud-font-scale`） |

## 伺服器端

`php/register.php` 使用 PDO prepared statement 寫入 `s53g2_starbattle.ranking`，
第一次執行時會自動建立資料庫與資料表（等同匯入 `php/ranking.sql`），
回傳格式與試題範例相同：

```json
[
  {"id":"1","name":"Player 1","time":"20","score":"10"},
  {"id":"2","name":"Player 2","time":"14","score":"8"}
]
```

競賽環境若要求 `http://<IP>/YY_Client_Side/register.php`，
只需修改 `js/ranking.js` 最上方的 `REGISTER_URL` 常數即可。

## 已驗證項目

以 headless Chrome 實際操作驗證（截圖存於 `design/`）：

* 遊戲說明的逐條動畫、Start Game 進入遊戲
* 計時器由 0 遞增、燃料每秒 −1、收集燃料 +15（15 → 22）
* 擊毀敵方 +5、誤擊友方 −10（分數出現負值）
* 感應區移動與畫面邊界限制（往上飛會停在 y = 0）
* 鍵盤 `P` 與暫停按鈕：暫停時計時器停止（2 秒後仍為 2），再按可繼續
* 字級按鈕：20px → 26px → 17px
* 音效按鈕：圖示切換、`aria-pressed` 切換、背景音樂暫停
* 燃料歸零 → 遊戲結束 → Continue 停用／填名後啟用 → AJAX 送出 → 排行榜正確排序並標示本局名次
* 瀏覽器主控台無 JavaScript 錯誤

## 備註

* 未使用任何需要連外的 CDN 或函式庫，全部為原生 HTML / CSS / JavaScript。
* CSS 刻意避開較新的簡寫屬性（`inset`、`rotate`、`backdrop-filter` 等），改用相容性與 W3C 驗證器支援度較佳的寫法。
* 由於本機為離線環境，無法連線 W3C validator 實際驗證，HTML5 / CSS3 的合規性是以人工檢查為準。

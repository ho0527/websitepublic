# WorldSkills 2015 — Client Side B（Olympic Race）

WSC2015_TP17 Web Design 職類，Module F「Client Side B」的實作。

- 進入點：<http://127.0.0.1:83/worldskill/2015/modulef/>
- 對應題目：`WSC2015_TP17_ClientSideB_actual.pdf`（Version 3.1）
- 素材來源：`WSC2015_TP17_resources_clientside/Marcelo - Client Side/Client Side B - 2hrs - Olympic Race/Media`

---

## 一、檔案結構

```
modulef/
├─ index.html          進入點（沿用主辦提供的版面骨架與所有素材）
├─ css/main.css        上半段＝提供的 main.css，「GAMEPLAY」以下為本次追加樣式
├─ js/
│  ├─ config.js        GameConfig：所有幾何 / 時間常數（跑道、坡道、時長、地標）
│  ├─ runner.js        Runner：位置、換道、跳躍、爬坡、影格動畫
│  ├─ obstacle.js      Obstacle：單一障礙物 + 隨機產生器
│  ├─ landmark.js      Landmark：地標的隱藏與呈現
│  ├─ game.js          OlympicRace：主迴圈、鏡頭、鍵盤、碰撞、結束流程
│  └─ script.js        進入點
├─ imgs/               主辦提供的 SVG 素材（全部使用）
├─ runner/             主辦提供的跑步影格 PNG（用於奔跑 / 跳躍動畫）
└─ lib/                主辦提供的 jQuery 本機版（本專案未使用，保留備查）
```

## 二、座標系統

以 `#playground`（5632 × 635）為基準，垂直方向使用「距離底部的距離」。
所有數值集中在 `js/config.js`，是照 `imgs/ground.svg` 的實際外觀量測出來的：

| 項目 | 值 | 說明 |
| --- | --- | --- |
| 跑道 1（最上 / 最遠） | `bottom: 112`，`scale 0.62` | 跑者最小 |
| 跑道 2（中） | `bottom: 82`，`scale 0.78` | |
| 跑道 3（最下 / 最近） | `bottom: 52`，`scale 0.96` | 跑者最大 |
| 上坡 | x = 4630 → 5095 | 對應 ground.svg 的斜坡 |
| 山頂平台 | `bottom: 232`，`scale 0.80` | 聖火台所在的白色平台 |
| 終點 | x = 5140 | 聖火台左側的白色位置 |
| 全程時間 | 10.5 秒 | 題目要求 9 ~ 12 秒 |

## 三、需求對照

| 題目 | 實作方式 |
| --- | --- |
| 1. 歡迎視窗 + 說明 + START | `#start` 模態視窗（保留提供的文案） |
| 2. 按下 START 後往右跑、跑步動作有動畫 | 4 張 `runner/runner_*.png` 影格 8 fps 循環，另加 `bodyBob` 身體起伏 |
| 3(a)(b) 三條跑道、上下鍵換道 | `Runner.changeLane(±1)` |
| 3(c) 不可跑出跑道 | 換道有上下界檢查 |
| 3(d) 透視：跑道 3 最大、跑道 1 最小 | 每條跑道各自的 `scale`，換道時有 0.22s 轉場 |
| 4. 空白鍵跳躍、動作與跑步不同、空中不可再跳 | 拋物線 `4p(1-p)`，高度 105px / 620ms；跳躍時切換成「雙腿張開」的影格並播放 `jumpPose`（身體後仰再前傾），`onGround` 檢查阻止連跳 |
| 5. 至少 5 個障礙、隨機、每條跑道至少一個 | 每局隨機產生 6 個，位置間距至少 320px；先保證三條跑道各一，其餘隨機 |
| 6. 跳過或換道皆可閃避；撞到即 Game Over + Restart | 碰撞條件＝同跑道 + 水平重疊 + 跳躍高度不足（< 障礙高度的 62%） |
| 7. 全程 9 ~ 12 秒 | 實測約 10.5 秒 |
| 8 / 9. 經過招牌時以動畫顯示地標圖與名稱，五個地標動畫各不相同 | 見下表 |
| 10. 聖火台與其招牌全程可見 | `#pyre` 及其 `.panel` 不受隱藏規則影響 |
| 11. 爬坡 → 停在聖火台旁 → 點燃聖火 → 顯示訊息與 Restart | `smoothstep` 抬升 + `climbing` 前傾動畫；`pyre_fire.svg` 以 `igniteFlame` + `flicker` 點燃，畫面同時泛光 |
| 12. Console 無錯誤 | 自動化測試已檢查 |

### 五個地標的呈現動畫（各自不同）

| 順序 | 地標 | 圖片動畫 | 招牌動畫 |
| --- | --- | --- | --- |
| 1 | Amazon Rainforest – Manaus – AM | 由下往上升起淡入 | 由上翻轉落下 |
| 2 | Lacerda Elevator – Salvador – BA | 由小放大（升降機意象） | 由左滑入 |
| 3 | Iguaçu Falls – Foz do Iguaçu – PR | 由上往下展開（瀑布） | 彈跳放大 |
| 4 | Cable-Stayed Bridge – São Paulo – SP | 由右側傾斜滑入 | Y 軸翻轉 |
| 5 | Christ the Redeemer – Rio de Janeiro – RJ | 以底部為軸旋轉展開 | 強光收斂 |

> 招牌名稱採用**題目 PDF** 的寫法（`Amazon Rainforest`、`Cable-Stayed Bridge`），
> 而非素材 HTML 內原本的 `Amazon Forest` / `Cable Stayed Bridge`。

## 四、實作取捨（2015 → 現代寫法）

1. **不使用 jQuery / jQuery UI。** 題目說「可以使用」。改用原生 `class` +
   `requestAnimationFrame` + CSS `transform` / `@keyframes`，效能與可讀性都較好。
   主辦提供的本機版 jQuery 仍保留在 `lib/`。
2. **鏡頭以 `transform: translate3d()` 平移 `#game`**，而非捲動視窗，
   可以走 GPU 合成、也不會受捲軸影響。跑者維持在畫面左側約 28% 的位置。
3. **跑者位置由「經過時間」推導**（等速），而非每幀累加速度，
   如此可以精準保證「9 ~ 12 秒跑完」，也不會因為掉幀而變快變慢。
4. **奔跑動畫用主辦提供的 4 張 PNG 影格**，容器固定 132×125、
   `background-position: bottom center`，讓四張不同尺寸的影格腳底對齊，
   自然產生身體起伏。跳躍改用雙腿張開的那張並套用不同的 keyframes，
   符合「跳躍動作必須與跑步不同」的要求。
5. **障礙物改由 JavaScript 動態產生**（原始 HTML 每條跑道各寫死一個 `<span>`），
   `.runway` 改為絕對定位的「基準線」容器，障礙物再以 `left` 定位。
6. **上坡以 `smoothstep` 內插高度並讓三條跑道收斂到山頂平台**，
   視覺上就是三條跑道在坡頂合而為一，與 `ground.svg` 的畫法一致。
7. 追加畫面上方的進度列（HUD），純輔助，不影響評分項目。

## 五、已知限制

- 遊戲畫面設計成 1440×900 左右的桌機視窗；視窗高度小於 ~700px 時
  賽道會被上下裁切（原始版面就是固定 635px 高、垂直置中）。
- 沒有做行動裝置的觸控操作（題目只要求鍵盤方向鍵與空白鍵）。
- 評分瀏覽器是 Chrome，本次即在 Chrome（含 headless）驗證。

# WorldSkills 2013 – Module G（Car Dealer 展場模擬器）

網址：<http://127.0.0.1:83/worldskill/2013/moduleG/>

## 檔案

| 檔案 | 說明 |
| --- | --- |
| `index.html` | 交件主頁（新增） |
| `dealer.css` | 版面樣式（改寫自題目提供的樣板） |
| `dealer.js` | 全部互動邏輯（改寫自題目提供的樣板） |
| `car_dealer.html` | 題目原始提供的樣板，保留備查，未使用 |
| `material/picture/images/` | 題目素材（車輛、客戶、收銀台、出口、SOLD 印章、背景） |
| `plugin/js/` | 題目提供的 jQuery 1.8.3 與 jQuery UI 1.9.2（本機檔案，完全不連外） |

## 已實作的規格

1. **展場區域**：客戶佇列、Porsche 4 台、Volkswagen 6 台、Audi 5 台、BMW 3 台、收銀台、出口、統計顯示，全部呈現在同一畫面。
2. **佇列**：最多同時 10 位客戶（面板右上顯示 `n/10`），每 1～4 秒隨機補一位新客戶；有人離開佇列後才會再補人。
3. **只有第一位可拖曳**：第一位客戶會被標亮並可拖曳，其餘客戶為半透明、不可拖曳。
4. **品牌配對**：客戶只能被放到他想看的品牌且未售出、未被佔用的車位；放到其他地方會自動飛回原處（jQuery UI `revert:'invalid'`）。
5. **售完可改看其他品牌**：某品牌全部掛上 SOLD 之後，想看該品牌的客戶可放到任何仍可用的車位。
6. **車位互斥**：已有客戶的車位不接受第二位客戶。
7. **收銀台**：只接受已在車位上的客戶，放下後跳出「Would you like to purchase the car?」對話框，按鈕為 YES / NO。
   - NO：客戶以動畫離場，只增加「Clients served」。
   - YES：客戶以動畫離場，增加「Clients served」「Cars sold」與「Amount collected」（Porsche € 72.500,00、Volkswagen € 23.930,00、Audi € 31.260,00、BMW € 43.990,00），該車蓋上 SOLD 印章且之後不可再被拜訪。
8. **出口**：佇列第一位或車位上的客戶都可以直接拖到出口，客戶立即移除且統計數字不變。
9. **使用者回饋**：拖曳時把可放置的區域標亮（綠框），右下角顯示操作結果訊息，統計數字更新時會閃動。
10. **JavaScript 註解**：`dealer.js` 內含 20 段以上的繁體中文區塊註解（題目要求至少 5 段）。

## 現代寫法的取捨

- 題目寫的是 2013 年的 Firefox / IE 相容性，本機沒有 IE 可測。實作改以**同一份標準 CSS3 + jQuery UI** 完成，Flexbox、CSS 動畫在現行 Chrome / Firefox / Edge 表現一致；未使用任何瀏覽器前綴 hack。
- 拖放沿用題目素材內的 **jQuery UI draggable / droppable**（2013 年的標準作法，也是唯一能在當年 IE 上一致運作的方式），而不是 HTML5 Drag & Drop API。所有函式庫檔案都在 `plugin/js/`，離線可用。
- 拖曳採用 clone helper 並掛到 `<body>`，避免車位的 `overflow:hidden` 裁切；放置成功後才把原節點搬進車位。
- 對話框改用自製 modal（原題只要求「顯示訊息並提供 YES / NO」），比 `confirm()` 更容易做到題目要求的外觀與動畫。

## 驗證

以 headless Chrome 模擬滑鼠事件跑過完整流程（1400×1000）：

- 放到錯誤品牌 → 客戶留在佇列 ✔
- 放到正確品牌 → 客戶進入車位 ✔
- 車位 → 收銀台 → 按 YES → served=1 / sold=1 / amount=€ 23.930,00、該車顯示 SOLD、庫存 6→5 ✔
- 佇列第一位 → 出口 → 客戶移除且統計不變 ✔
- Console 無任何 JavaScript 錯誤 ✔

## 尚未處理

- 未在真實 Internet Explorer 上比對（環境沒有 IE）。
- 客戶頭像沿用題目提供的 10 張圖，未另行修圖。

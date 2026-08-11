# WorldSkills 2015 — Skill 17 Web Design｜Design B（Olímpia Trucks 響應式改版）

舊網站 Olímpia Trucks 的現代化響應式重新設計。
線上預覽：<http://127.0.0.1:83/worldskill/2015/moduleb/>

## 交付檔案

| 路徑 | 尺寸 | 說明 |
| --- | --- | --- |
| `export/TW_computer.png` | 1440 × 3709 | 電腦版；前 1440×900 為裝置螢幕範圍，畫有 3px 紅框 |
| `export/TW_computer_2.png` | 1440 × 900 | 追加圖：登入面板展開狀態 |
| `export/TW_tablet.png` | 768 × 4522 | 平板版；前 768×1024 有 3px 紅框 |
| `export/TW_tablet_2.png` | 768 × 1024 | 追加圖：平板漢堡選單展開 |
| `export/TW_smartphone.png` | 320 × 5073 | 手機版；前 320×480 有 3px 紅框 |
| `export/TW_smartphone_2.png` | 320 × 480 | 追加圖：手機全螢幕選單展開 |
| `export/TW_mockups_preview.png` | 1500 × 1200 | 使用官方 `Mockups.psd` 模板，三個螢幕都嵌入對應設計 |
| `index.html` + `css/style.css` | — | 可編輯原始檔（真正可運作的 RWD 實作） |

紅框一律 3px（符合「小於等於 3px」），畫在圖片最上方的「裝置螢幕解析度」矩形上，
下方多出來的高度用來表現捲動內容，與題目附圖的作法相同。

## 關於「原始檔（source files）」

題目要求把 `.psd` / `.ai` 放進 `XX_source_files`。
**這台機器沒有 Photoshop / Illustrator，無法輸出 PSD / AI**，因此以
**HTML + CSS + 內嵌 SVG 圖示** 作為可編輯原始檔。所有斷點、色票、間距、
hover 狀態都在 `css/style.css`，修改後重跑截圖即可重新輸出全部 mock-up。

`TW_mockups_preview.png` 是把三張 PNG 以程式合成進官方 `Mockups.psd` 的
合成圖層（由 Pillow 讀出 PSD 合成影像後貼入三個螢幕區域），非手動在 Photoshop 內完成。

## 舊網站內容的保留對照

| 舊網站內容 | 新設計位置 |
| --- | --- |
| 公司 logo（保留原色與結構） | 標頭、頁尾（頁尾為同形狀的白色反白版） |
| 主選單 Start / Our Trucks / About us / Impressum | 標頭主選單（電腦版）／漢堡選單（平板、手機） |
| 子選單 Fast / Slow / Big Trucks | 電腦版下拉選單 + 「The range」區塊 + 頁尾連結 |
| Search 表單 | 標頭搜尋列；手機收成圖示，實際輸入在選單內 |
| 社群圖示 facebook / google / twitter | 頂部聯絡列與頁尾 |
| Login 表單（Username / Pass / Login） | 標頭 Login 按鈕 + 下拉登入面板（`TW_computer_2.png`） |
| 「Trucks」標題與前言 "Transport is a trust business…" | 主視覺標題與導言 |
| 3 則產品：Great efficiency / Trailer concepts / History（含原圖與 More information） | 「Our Trucks」三張卡片，沿用 `img/trucks/1.jpg`、`3.jpg`、`2.jpg` |
| Newsletter（Email / Send） | 電子報區塊 |
| Where you can find us + map.png | 「Where you can find us」地圖區塊 |
| 頁尾聯絡資訊全文 | 頁尾 `Impressum` 與底部法律列（原文完整保留） |

## 響應式作法

- **電腦（≥1024px）**：完整水平選單、下拉子選單、三欄卡片、左右並排的關於／地圖。
- **平板（640–1023px）**：選單收合為漢堡、搜尋列縮短、卡片兩欄（第三張改為橫式跨欄）、關於與地圖改為單欄。
- **手機（<640px）**：單欄、全螢幕選單、主視覺壓縮到 480px 首屏內、
  底部固定快捷列（Trucks / Call / Dealer / Login，屬於裝置專屬元件）、
  聯絡列只保留電話、搜尋收成圖示。
- 主視覺圖片使用 `<picture>` 依裝置切換不同裁切比例的檔案。

## 電腦版 hover 效果（題目要求須呈現）

1. 主選單「Our Trucks」下拉展開，且子項「Slow Trucks」為琥珀色 hover 態。
2. 主視覺「Explore our trucks」按鈕轉琥珀色並上浮加陰影。
3. 「Trailer concepts」卡片上浮、圖片放大、連結轉琥珀色，旁邊有滑鼠游標圖示。

## 色彩

主色完全取自公司原始 logo 的漸層（深靛藍 `#1A1554` → `#2B2483` → 鋼藍 `#6F8FD0`），
僅以琥珀 `#F2A51A` 作為行動呼籲的對比色，維持原識別的辨識度。

## 字型

- 標題：Oswald（題目 `GoogleFonts-master.zip` 內）
- 內文：Source Sans Pro（同上）
- Logo 專用字：`Palo_Alto_Oblique.ttf`（題目提供，已放入 `assets/fonts/`）

## 狀態切換參數（供輸出額外 mock-up）

- `?state=menu`：行動版選單展開
- `?state=login`：登入面板展開
- `?state=shot`：把固定底部快捷列釘在 480px 首屏底部（僅供整頁截圖用）

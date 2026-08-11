# WorldSkills 2015 — Skill 17 Web Design｜Design A（Community Plumbing Challenge）

一頁式（one page）網站設計稿。線上預覽：<http://127.0.0.1:83/worldskill/2015/modulea/>

## 交付檔案

| 路徑 | 說明 |
| --- | --- |
| `export/TW_onepage.png` | **正式交付的設計圖**，寬度 1366px（題目指定），高度 7129px |
| `index.html` + `css/style.css` | 可編輯原始檔（相當於 PSD/AI 的角色，見下方說明） |
| `assets/img/` | 由題目素材轉檔而來的圖片（emblem、夥伴 logo、海報主視覺、照片） |
| `assets/fonts/` | 題目提供的 Frutiger LT Std / Trade Gothic LT Std 字型 |

## 關於「原始檔（source files）」

題目要求把原始檔（`.psd` / `.ai`）放到 `XX_source_files`。
**這台機器沒有 Photoshop / Illustrator，無法產生 PSD 或 AI 檔**，因此改以
**HTML + CSS + 內嵌 SVG** 作為可編輯原始檔：所有版面、色票、字級、互動狀態
都寫在 `css/style.css` 裡，改一行就能重新輸出 PNG，可編輯性與圖層檔等價。
競賽現場請改用 Photoshop/Illustrator 依此版面重做圖層檔。

## 對應題目需求

| 題目要求 | 實作位置 |
| --- | --- |
| 一頁式、以捲動取代子頁 | 導覽列全部是 `#` 錨點，單一 `index.html` |
| 寬度 1366px | `:root { --w: 1366px }`，輸出圖實際寬度 1366 |
| Video gallery | `#videos`：主播放器 + 3 部影片清單、播放鍵、播放進度條 |
| Photo gallery | `#photos`：篩選 chip + 6×3 雜誌式拼貼 + Load more |
| Social media feed | `#social`：Twitter / Instagram / Facebook / YouTube 直播 4 張貼文卡 |
| Team information | `#team`：4 位成員（照片、職稱、簡介、社群） |
| Resource download option | `#resources`：4 個檔案（PDF/ZIP/MP4/PNG）+ 全部下載 |
| 夥伴 logo 需有標題且清楚標示 | `#partners`：標題「Our partners」，三個等高等寬容器，logo 尺寸一致 |
| 必須包含原始 emblem | 導覽列與頁尾皆使用 `Emblem.pdf` 轉出的 `cpc-emblem.png` |
| 採用海報的視覺識別 | 主視覺重現海報的青藍漸層 + 黃色放射光線 + 手握水龍頭圖形；色票取自海報／emblem |
| 使用題目提供字型 | 標題 Trade Gothic LT Std BdCn20、內文 Frutiger LT Std Roman |
| 互動狀態要看得見 | 導覽 hover、按鈕 hover、影片列 hover、照片 hover（放大鏡＋說明）、團隊卡 hover、下載列 hover，皆附滑鼠游標圖示 |

## 色票（取樣自海報與 emblem）

| 用途 | 色碼 |
| --- | --- |
| 深藍（標題） | `#262261` |
| 品牌藍 | `#0055B8` |
| 青 | `#00A3E3` / `#04BDF0` |
| 青綠（強調） | `#01C4B4` |

## 重新輸出 PNG

網站需先由 nginx 提供服務，再用 headless Chrome（CDP `Page.captureScreenshot`
+ `captureBeyondViewport`）整頁截圖為 1366px 寬的 PNG。

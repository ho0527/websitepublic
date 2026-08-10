/**
 * A09copy - Markdown Preview（WorldSkills 2022 Module A / C13）
 * 左側輸入 Markdown，右側即時預覽。
 * 支援：段落與換行、標題、粗體(含斜體/粗斜體)、水平線、清單、連結、圖片。
 *
 * 逐行處理，行層級語法(標題/水平線/清單)先判斷，再處理行內語法(粗體/連結/圖片)，
 * 避免先把換行換成 <br> 之後行首規則失效。
 */

const input = document.getElementById("input")
const show = document.getElementById("show")
const change = document.getElementById("change")

/** 將 HTML 特殊字元轉為實體，避免輸入的標籤被直接執行 */
function escapeHtml(text) {
    return text.replace(/[&<>]/g, function (character) {
        return { "&": "&amp;", "<": "&lt;", ">": "&gt;" }[character]
    })
}

/**
 * 處理行內語法：粗斜體 → 粗體 → 斜體 → 圖片 → 連結
 * 圖片必須排在連結前面，否則 ![alt](url) 會被當成連結
 * @param {string} text 已跳脫的單行文字
 * @returns {string} 轉換後的 HTML
 */
function renderInline(text) {
    return text
        .replace(/\*\*\*(.+?)\*\*\*/g, "<strong><i>$1</i></strong>")
        .replace(/\*\*(.+?)\*\*/g, "<strong>$1</strong>")
        .replace(/\*(.+?)\*/g, "<i>$1</i>")
        .replace(/!\[(.*?)\]\(\s*(.*?)\s*\)/g, '<img src="$2" alt="$1">')
        .replace(/\[(.*?)\]\(\s*(.*?)\s*\)/g, '<a href="$2" target="_blank">$1</a>')
}

/**
 * 將 Markdown 轉為 HTML
 * @param {string} source 原始輸入
 * @returns {string} 預覽用的 HTML
 */
function renderMarkdown(source) {
    // 換行模式：ON 代表真正的換行即為斷行，OFF 代表輸入的 "\n" 兩個字元才算斷行
    const normalized = change.value === "ON"
        ? source
        : source.replace(/\\n/g, "\n")

    const lines = escapeHtml(normalized).split(/\r?\n/)
    const html = []
    let inList = false

    /** 清單結束時補上結尾標籤 */
    function closeList() {
        if (inList) {
            html.push("</ul>")
            inList = false
        }
    }

    for (let index = 0; index < lines.length; index = index + 1) {
        const line = lines[index]

        // 水平線：三個以上的 - 或 *
        if (/^\s*(-{3,}|\*{3,})\s*$/.test(line)) {
            closeList()
            html.push("<hr>")
            continue
        }

        // 標題：# ~ ######
        const heading = line.match(/^(#{1,6})\s+(.*)$/)
        if (heading) {
            closeList()
            const level = heading[1].length
            html.push("<h" + level + ">" + renderInline(heading[2]) + "</h" + level + ">")
            continue
        }

        // 清單：連續的 "- item" 會包在同一個 <ul> 內
        const listItem = line.match(/^\s*-\s+(.*)$/)
        if (listItem) {
            if (!inList) {
                html.push("<ul>")
                inList = true
            }
            html.push("<li>" + renderInline(listItem[1]) + "</li>")
            continue
        }

        closeList()

        // 空行代表段落分隔
        if (line.trim() === "") {
            html.push("<br>")
            continue
        }

        html.push(renderInline(line) + "<br>")
    }

    closeList()
    return html.join("\n")
}

/** 重新產生預覽 */
function updatePreview() {
    show.innerHTML = renderMarkdown(input.value)
}

// 支援 Tab 縮排(輸入四個空白)
input.addEventListener("keydown", function (event) {
    if (event.key === "Tab") {
        event.preventDefault()
        const start = this.selectionStart
        this.value = this.value.substring(0, start) + "    " + this.value.substring(this.selectionEnd)
        this.selectionStart = start + 4
        this.selectionEnd = start + 4
        updatePreview()
    }
})

change.onclick = function () {
    change.value = change.value === "ON" ? "OFF" : "ON"
    updatePreview()
}

// 「live」預覽：任何輸入立即反映到右側
input.addEventListener("input", updatePreview)

updatePreview()

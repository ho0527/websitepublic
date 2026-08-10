/**
 * C1: Keyword Highlighter (Normal)
 * 按下 Search 後，將段落中所有符合搜尋字串的文字highlight起來，
 * highlight 顏色為隨機，搜尋為區分大小寫(case-sensitive)。
 */

const textElement = document.getElementById("text")
const searchInput = document.getElementById("search")
const submitButton = document.getElementById("submit")

// 保留原始文字，每次搜尋都從原文重新標記，避免重複搜尋時破壞既有標記
const originalText = textElement.textContent

/**
 * 將字串中的正規表示式特殊字元轉為純文字，避免使用者輸入 . * ( ) 等符號時出錯
 * @param {string} text 使用者輸入的搜尋字串
 * @returns {string} 已跳脫的字串
 */
function escapeRegExp(text) {
    return text.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")
}

/**
 * 產生一個隨機的高亮顏色，並依亮度決定文字要用黑色或白色
 * @returns {{background:string,color:string}} 背景色與文字色
 */
function createRandomColor() {
    const red = Math.floor(Math.random() * 256)
    const green = Math.floor(Math.random() * 256)
    const blue = Math.floor(Math.random() * 256)
    // 以感知亮度判斷底色深淺，確保文字在任何隨機色上都看得清楚
    const brightness = (red * 299 + green * 587 + blue * 114) / 1000
    return {
        background: "rgb(" + red + "," + green + "," + blue + ")",
        color: brightness > 140 ? "#000000" : "#ffffff"
    }
}

/** 依搜尋字串重新繪製段落，符合的部分包上隨機顏色的 <mark> */
function highlightKeyword() {
    const keyword = searchInput.value
    textElement.innerHTML = ""

    if (keyword === "") {
        textElement.textContent = originalText
        return
    }

    // 用跳脫後的關鍵字切開原文，split 保留分隔符號(括號群組)以便逐段重建
    const pattern = new RegExp("(" + escapeRegExp(keyword) + ")", "g")
    const pieces = originalText.split(pattern)

    pieces.forEach(function (piece) {
        if (piece === "") {
            return
        }
        if (piece === keyword) {
            const color = createRandomColor()
            const mark = document.createElement("mark")
            mark.className = "highlight"
            mark.style.background = color.background
            mark.style.color = color.color
            mark.textContent = piece
            textElement.appendChild(mark)
        } else {
            textElement.appendChild(document.createTextNode(piece))
        }
    })
}

submitButton.onclick = highlightKeyword

// 按 Enter 也可以搜尋
searchInput.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
        highlightKeyword()
    }
})

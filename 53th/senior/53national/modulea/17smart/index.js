/**
 * A17smart - Tic-Tac-Toe 前端
 * 畫面資料一律向後端 api.php 非同步索取，前端只負責呈現與送出操作。
 */

const API_URL = "api.php"
const cells = document.querySelectorAll(".td")
const maskElement = document.getElementById("maskdiv")

/**
 * 呼叫後端取得最新盤面
 * @param {object|null} body 要送出的動作，null 代表只讀取狀態
 * @returns {Promise<object>} 遊戲狀態
 */
async function callApi(body) {
    const options = body === null
        ? { method: "GET" }
        : {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
        }
    const response = await fetch(API_URL, options)
    return response.json()
}

/**
 * 依後端回傳的狀態重畫畫面
 * @param {object} game 遊戲狀態
 */
function render(game) {
    cells.forEach(function (cell, index) {
        const value = game.board[index]
        cell.textContent = value
        cell.classList.toggle("cover", value !== "")
    })

    if (game.over) {
        showResult(game.winner)
    } else {
        hideResult()
    }
}

/** 隱藏勝負提示（初始狀態與重新開始時） */
function hideResult() {
    maskElement.innerHTML = ""
}

/**
 * 顯示勝負提示
 * @param {string} winner "X"、"O" 或 "draw"
 */
function showResult(winner) {
    const label = winner === "draw" ? "平手" : winner + " 獲勝"
    maskElement.innerHTML = `
        <div class="div">
            <div class="mask"></div>
            <div class="body">
                <h2 class="title">遊戲結束</h2>
                <hr>
                <h1>結果:${label}</h1>
                <div class="buttonlist">
                    <button id="restart" class="submit button">重新開始</button>
                </div>
            </div>
        </div>
    `
    document.getElementById("restart").onclick = resetGame
}

/** 玩家點擊格子 */
async function playCell(index) {
    render(await callApi({ cell: index }))
}

/** 重新開始一局 */
async function resetGame() {
    render(await callApi({ reset: true }))
}

cells.forEach(function (cell, index) {
    cell.addEventListener("click", function () {
        if (cell.textContent === "") {
            playCell(index)
        }
    })
})

document.getElementById("reset").onclick = resetGame

// 進入頁面先向後端拿狀態，重新整理後可以接續上一局
callApi(null).then(render)

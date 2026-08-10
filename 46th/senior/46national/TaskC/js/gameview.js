/**
 * gameview.js
 * 負責把遊戲狀態畫到畫面上(盤面、下一個方塊、等級、行數、時間)，
 * 以及把目前的遊戲資訊輸出成一張 JPEG 圖片。
 */

class GameView {

    constructor() {
        this.boardElement = document.getElementById("board")
        this.nextElement = document.getElementById("nextPreview")
        this.levelElement = document.getElementById("levelText")
        this.lineElement = document.getElementById("lineText")
        this.timeElement = document.getElementById("timeText")
        this.pauseMaskElement = document.getElementById("pauseMask")
        this.boardCells = []
        this.nextCells = []
        this.createBoardCells()
        this.createNextCells()
    }

    /** 建立盤面的每一個格子(只建立一次，之後只更新顏色) */
    createBoardCells() {
        this.boardElement.style.setProperty("--column-count", GAME_CONFIG.columnCount)
        this.boardElement.style.setProperty("--row-count", GAME_CONFIG.rowCount)
        for (let row = 0; row < GAME_CONFIG.rowCount; row = row + 1) {
            const rowCells = []
            for (let column = 0; column < GAME_CONFIG.columnCount; column = column + 1) {
                const cell = document.createElement("div")
                cell.className = "cell"
                this.boardElement.appendChild(cell)
                rowCells.push(cell)
            }
            this.boardCells.push(rowCells)
        }
    }

    /** 建立「下一個方塊」預覽區的格子(固定 4x4) */
    createNextCells() {
        for (let row = 0; row < 4; row = row + 1) {
            const rowCells = []
            for (let column = 0; column < 4; column = column + 1) {
                const cell = document.createElement("div")
                cell.className = "cell"
                this.nextElement.appendChild(cell)
                rowCells.push(cell)
            }
            this.nextCells.push(rowCells)
        }
    }

    /**
     * 依遊戲狀態更新整個畫面
     * @param {GameEngine} engine 遊戲核心
     */
    render(engine) {
        this.renderBoard(engine.getRenderBoard())
        this.renderNext(engine.nextType)
        this.levelElement.textContent = GameStorage.getDifficultyText(engine.difficulty)
        this.lineElement.textContent = String(engine.lines)
        this.timeElement.textContent = formatTime(engine.elapsedSeconds)
    }

    /** 繪製盤面 */
    renderBoard(view) {
        for (let row = 0; row < GAME_CONFIG.rowCount; row = row + 1) {
            for (let column = 0; column < GAME_CONFIG.columnCount; column = column + 1) {
                const type = view[row][column]
                const cell = this.boardCells[row][column]
                cell.style.background = type === null ? "" : BLOCK_COLORS[type]
                cell.classList.toggle("filled", type !== null)
            }
        }
    }

    /** 繪製「下一個方塊」 */
    renderNext(type) {
        const matrix = createTetrominoMatrix(type)
        for (let row = 0; row < 4; row = row + 1) {
            for (let column = 0; column < 4; column = column + 1) {
                const isFilled = matrix[row] !== undefined && matrix[row][column] === 1
                const cell = this.nextCells[row][column]
                cell.style.background = isFilled ? BLOCK_COLORS[type] : ""
                cell.classList.toggle("filled", isFilled)
            }
        }
    }

    /**
     * 切換暫停畫面：遊戲畫面隱藏並顯示「暫停」
     * @param {boolean} isPaused 是否為暫停狀態
     */
    setPaused(isPaused) {
        this.pauseMaskElement.classList.toggle("show", isPaused)
        this.boardElement.classList.toggle("hidden", isPaused)
        document.getElementById("pauseButton").value = isPaused ? "繼續遊戲" : "暫停遊戲"
    }

    /**
     * 顯示遊戲結束的區塊，並帶入結束前一刻的行數與時間
     * @param {GameEngine} engine 遊戲核心
     */
    showGameOver(engine) {
        document.getElementById("overLines").textContent = String(engine.lines)
        document.getElementById("overTime").textContent = formatTime(engine.elapsedSeconds)
        document.getElementById("overLevel").textContent = GameStorage.getDifficultyText(engine.difficulty)
        document.getElementById("gameOverMask").classList.add("show")
        document.getElementById("nickname").focus()
    }

    /**
     * 將目前的遊戲畫面、下個方塊、等級、行數及時間輸出成 JPEG 並下載
     * @param {GameEngine} engine 遊戲核心
     */
    downloadSnapshot(engine) {
        const cellSize = 30
        const padding = 20
        const panelWidth = 220
        const canvas = document.createElement("canvas")
        canvas.width = padding * 3 + GAME_CONFIG.columnCount * cellSize + panelWidth
        canvas.height = padding * 2 + GAME_CONFIG.rowCount * cellSize
        const context = canvas.getContext("2d")

        // 背景(JPEG 沒有透明度，必須先填滿底色)
        context.fillStyle = "#232323"
        context.fillRect(0, 0, canvas.width, canvas.height)

        // 遊戲畫面
        const view = engine.getRenderBoard()
        for (let row = 0; row < GAME_CONFIG.rowCount; row = row + 1) {
            for (let column = 0; column < GAME_CONFIG.columnCount; column = column + 1) {
                const x = padding + column * cellSize
                const y = padding + row * cellSize
                const type = view[row][column]
                context.fillStyle = type === null ? "#2f2f2f" : BLOCK_COLORS[type]
                context.fillRect(x, y, cellSize - 1, cellSize - 1)
            }
        }

        // 右側資訊：下個方塊、等級、行數、時間
        const panelX = padding * 2 + GAME_CONFIG.columnCount * cellSize
        context.fillStyle = "#ffffff"
        context.font = "bold 22px Arial"
        context.fillText("下一個", panelX, padding + 22)

        const nextMatrix = createTetrominoMatrix(engine.nextType)
        for (let row = 0; row < nextMatrix.length; row = row + 1) {
            for (let column = 0; column < nextMatrix[row].length; column = column + 1) {
                if (nextMatrix[row][column] === 1) {
                    context.fillStyle = BLOCK_COLORS[engine.nextType]
                    context.fillRect(
                        panelX + column * cellSize,
                        padding + 40 + row * cellSize,
                        cellSize - 1,
                        cellSize - 1
                    )
                }
            }
        }

        context.fillStyle = "#ffffff"
        context.font = "bold 22px Arial"
        const infoTop = padding + 60 + nextMatrix.length * cellSize
        context.fillText("等級：" + GameStorage.getDifficultyText(engine.difficulty), panelX, infoTop)
        context.fillText("行數：" + engine.lines, panelX, infoTop + 40)
        context.fillText("時間：" + formatTime(engine.elapsedSeconds), panelX, infoTop + 80)

        // 轉成 JPEG 並跳出另存新檔視窗
        const link = document.createElement("a")
        link.href = canvas.toDataURL("image/jpeg", 0.92)
        link.download = "tetris_" + formatTime(engine.elapsedSeconds).replace(":", "") + ".jpg"
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
    }
}

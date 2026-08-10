/**
 * gameengine.js
 * 遊戲核心邏輯：盤面、方塊移動與旋轉、消行、障礙物、遊戲結束判斷。
 * 此類別完全不接觸 DOM，畫面由 GameView 負責，遊戲狀態也只存在於實體內部，
 * 以減少全域變數並降低遊戲狀態被外部任意修改的可能。
 */

class GameEngine {

    /**
     * @param {string} difficulty 難度代號(normal / hard)
     */
    constructor(difficulty) {
        this.difficulty = difficulty === "hard" ? "hard" : "normal"
        this.board = this.createEmptyBoard()
        this.currentBlock = null              // 目前操作中的方塊
        this.nextType = pickRandomTetrominoType()
        this.lines = 0                        // 已消除的行數(障礙物不計)
        this.elapsedSeconds = 0               // 經過的遊戲秒數
        this.garbageTimerSecond = 0           // 距離上次長出障礙物的秒數
        this.startTime = Date.now()           // 開始遊戲的時間(排行榜同分時比較用)
        this.isOver = false
        this.isPaused = false
        this.spawnBlock()
    }

    /** 建立一個全空的盤面 */
    createEmptyBoard() {
        const board = []
        for (let row = 0; row < GAME_CONFIG.rowCount; row = row + 1) {
            board.push(new Array(GAME_CONFIG.columnCount).fill(null))
        }
        return board
    }

    /** 目前難度對應的方塊下降間隔(毫秒) */
    getDropInterval() {
        return this.difficulty === "hard"
            ? GAME_CONFIG.dropIntervalHard
            : GAME_CONFIG.dropIntervalNormal
    }

    /** 產生下一個方塊，並預先決定再下一個要出現的方塊 */
    spawnBlock() {
        const type = this.nextType
        const matrix = createTetrominoMatrix(type)
        this.currentBlock = {
            type: type,
            matrix: matrix,
            x: Math.floor((GAME_CONFIG.columnCount - matrix.length) / 2),
            y: -1                                   // 由上邊界外開始進場
        }
        this.nextType = pickRandomTetrominoType()

        // 一產生就無處可放，代表畫面已經堆到頂端
        if (!this.isPlaceable(this.currentBlock.matrix, this.currentBlock.x, this.currentBlock.y)) {
            this.isOver = true
        }
    }

    /**
     * 判斷方塊放在指定位置是否合法(不超出邊界、不與其他方塊重疊)
     * @param {number[][]} matrix 形狀矩陣
     * @param {number} offsetX 盤面上的 X 位置
     * @param {number} offsetY 盤面上的 Y 位置
     */
    isPlaceable(matrix, offsetX, offsetY) {
        const cells = getFilledCells(matrix)
        for (let index = 0; index < cells.length; index = index + 1) {
            const x = offsetX + cells[index].x
            const y = offsetY + cells[index].y
            if (x < 0 || x >= GAME_CONFIG.columnCount || y >= GAME_CONFIG.rowCount) {
                return false                        // 超出左右或下邊界
            }
            if (y >= 0 && this.board[y][x] !== null) {
                return false                        // 與已停止的方塊重疊
            }
        }
        return true
    }

    /** 是否可以接受玩家操作 */
    canOperate() {
        return !this.isOver && !this.isPaused
    }

    /**
     * 左右(或向下)平移目前的方塊
     * @param {number} stepX 水平位移格數
     * @param {number} stepY 垂直位移格數
     * @returns {boolean} 是否成功移動
     */
    moveBlock(stepX, stepY) {
        if (!this.canOperate()) {
            return false
        }
        const block = this.currentBlock
        if (!this.isPlaceable(block.matrix, block.x + stepX, block.y + stepY)) {
            return false
        }
        block.x = block.x + stepX
        block.y = block.y + stepY
        return true
    }

    /**
     * 將目前的方塊向左旋轉 90 度，若旋轉後位置不合法則嘗試左右微調
     * @returns {boolean} 是否成功旋轉
     */
    rotateBlock() {
        if (!this.canOperate()) {
            return false
        }
        const block = this.currentBlock
        const rotated = rotateMatrixLeft(block.matrix)
        const kickOffsets = [0, -1, 1, -2, 2]        // 靠牆旋轉時的微調量
        for (let index = 0; index < kickOffsets.length; index = index + 1) {
            const offsetX = block.x + kickOffsets[index]
            if (this.isPlaceable(rotated, offsetX, block.y)) {
                block.matrix = rotated
                block.x = offsetX
                return true
            }
        }
        return false
    }

    /**
     * 方塊自動(或玩家操作)向下移動一格，若無法下移則固定方塊
     * @returns {boolean} 方塊是否成功下移
     */
    stepDown() {
        if (!this.canOperate()) {
            return false
        }
        if (this.moveBlock(0, 1)) {
            return true
        }
        this.lockBlock()
        return false
    }

    /** 將目前的方塊直接落到底部並固定 */
    dropToBottom() {
        if (!this.canOperate()) {
            return
        }
        while (this.moveBlock(0, 1)) {
            // 持續下移到不能移動為止
        }
        this.lockBlock()
    }

    /** 把目前的方塊固定在盤面上，並處理消行、障礙物與遊戲結束 */
    lockBlock() {
        const block = this.currentBlock
        const cells = getFilledCells(block.matrix)
        const touchedRows = []

        for (let index = 0; index < cells.length; index = index + 1) {
            const x = block.x + cells[index].x
            const y = block.y + cells[index].y
            if (y < 0) {
                // 方塊最上層的小方塊已經超出上邊界 → 遊戲結束
                this.isOver = true
                return
            }
            this.board[y][x] = block.type            // 以方塊代號記錄顏色，之後不會再變化
            if (touchedRows.indexOf(y) === -1) {
                touchedRows.push(y)
            }
        }

        this.clearTouchedGarbageRows(touchedRows)
        this.clearFullLines()
        this.spawnBlock()
    }

    /**
     * 方塊進入障礙物層且無法再移動時，清除該層障礙物(不增加行數)
     * @param {number[]} touchedRows 方塊剛剛佔用的列
     */
    clearTouchedGarbageRows(touchedRows) {
        const targetRows = touchedRows.filter(function (rowIndex) {
            return this.isGarbageRow(rowIndex)
        }, this)

        targetRows.sort(function (left, right) {
            return left - right
        })
        for (let index = targetRows.length - 1; index >= 0; index = index - 1) {
            this.removeRow(targetRows[index])
        }
    }

    /** 判斷某一列是否為障礙物層 */
    isGarbageRow(rowIndex) {
        return this.board[rowIndex].some(function (cell) {
            return cell === GARBAGE_CELL
        })
    }

    /** 消除填滿的整行，並累加行數(含障礙物的列不計) */
    clearFullLines() {
        for (let rowIndex = GAME_CONFIG.rowCount - 1; rowIndex >= 0; rowIndex = rowIndex - 1) {
            if (this.isGarbageRow(rowIndex)) {
                continue                             // 障礙物不計行數
            }
            const isFull = this.board[rowIndex].every(function (cell) {
                return cell !== null
            })
            if (isFull) {
                this.removeRow(rowIndex)
                this.lines = this.lines + 1
                rowIndex = rowIndex + 1              // 上方各行已下移，重新檢查同一列
            }
        }
    }

    /** 移除指定的一列，上方所有行向下移動 */
    removeRow(rowIndex) {
        this.board.splice(rowIndex, 1)
        this.board.unshift(new Array(GAME_CONFIG.columnCount).fill(null))
    }

    /**
     * 在遊戲畫面最下方新增一層障礙物，上方的方塊皆向上移動
     * 障礙物由灰色小方塊組成，並隨機留下一個空格
     */
    addGarbageRow() {
        const gapColumn = Math.floor(Math.random() * GAME_CONFIG.columnCount)
        const garbageRow = new Array(GAME_CONFIG.columnCount).fill(GARBAGE_CELL)
        garbageRow[gapColumn] = null

        this.board.shift()                           // 最上方的一列被推出畫面
        this.board.push(garbageRow)

        // 操作中的方塊也跟著向上移動，避免被障礙物穿透
        const block = this.currentBlock
        if (this.isPlaceable(block.matrix, block.x, block.y - 1)) {
            block.y = block.y - 1
        }
    }

    /**
     * 遊戲時間前進一秒，「困難」難度每 10 秒長出一層障礙物
     */
    tickSecond() {
        if (!this.canOperate()) {
            return
        }
        this.elapsedSeconds = this.elapsedSeconds + 1
        if (this.difficulty !== "hard") {
            return
        }
        this.garbageTimerSecond = this.garbageTimerSecond + 1
        if (this.garbageTimerSecond >= GAME_CONFIG.garbageIntervalSecond) {
            this.garbageTimerSecond = 0
            this.addGarbageRow()
        }
    }

    /**
     * 取得包含操作中方塊的完整畫面，供畫面繪製使用
     * @returns {(string|null)[][]} 每一格的方塊代號
     */
    getRenderBoard() {
        const view = this.board.map(function (row) {
            return row.slice()
        })
        const block = this.currentBlock
        if (block === null) {
            return view
        }
        const cells = getFilledCells(block.matrix)
        for (let index = 0; index < cells.length; index = index + 1) {
            const x = block.x + cells[index].x
            const y = block.y + cells[index].y
            if (y >= 0 && y < GAME_CONFIG.rowCount && x >= 0 && x < GAME_CONFIG.columnCount) {
                view[y][x] = block.type
            }
        }
        return view
    }

    /** 將遊戲狀態轉為可儲存的純資料 */
    toPlainObject() {
        return {
            difficulty: this.difficulty,
            board: this.board,
            currentBlock: this.currentBlock,
            nextType: this.nextType,
            lines: this.lines,
            elapsedSeconds: this.elapsedSeconds,
            garbageTimerSecond: this.garbageTimerSecond,
            startTime: this.startTime,
            isOver: this.isOver,
            isPaused: this.isPaused
        }
    }

    /**
     * 由儲存的資料還原遊戲狀態
     * @param {object} data toPlainObject() 產生的資料
     * @returns {GameEngine|null} 還原後的遊戲，資料不完整時回傳 null
     */
    static fromPlainObject(data) {
        if (!data || !Array.isArray(data.board) || !data.currentBlock) {
            return null
        }
        const engine = new GameEngine(data.difficulty)
        engine.board = data.board
        engine.currentBlock = data.currentBlock
        engine.nextType = data.nextType
        engine.lines = data.lines
        engine.elapsedSeconds = data.elapsedSeconds
        engine.garbageTimerSecond = data.garbageTimerSecond
        engine.startTime = data.startTime
        engine.isOver = data.isOver === true
        engine.isPaused = data.isPaused === true
        return engine
    }
}

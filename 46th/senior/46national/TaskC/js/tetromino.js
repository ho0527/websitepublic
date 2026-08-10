/**
 * tetromino.js
 * 方塊(Tetromino)的定義與旋轉運算，純資料與純函式，不涉及畫面。
 */

/** 遊戲面板尺寸與速度等設定 */
const GAME_CONFIG = {
    columnCount: 10,                 // 遊戲畫面寬度(格)
    rowCount: 17,                    // 遊戲畫面高度(格)
    dropIntervalNormal: 1000,        // 「一般」難度：每 1 秒下降一格
    dropIntervalHard: 250,           // 「困難」難度：每 0.25 秒下降一格
    garbageIntervalSecond: 10        // 「困難」難度：每 10 秒長出一層障礙物
}

/** 障礙物(灰色小方塊)在盤面中的代號 */
const GARBAGE_CELL = "X"

/** 每種方塊的顏色，同時給 CSS 與畫面分享的 canvas 使用 */
const BLOCK_COLORS = {
    I: "#22d3ee",
    J: "#3b82f6",
    L: "#f97316",
    O: "#facc15",
    S: "#22c55e",
    T: "#a855f7",
    Z: "#ef4444",
    X: "#9ca3af"   // 障礙物：灰色
}

/** 七種方塊的初始形狀(以正方形矩陣表示，方便旋轉) */
const TETROMINO_SHAPES = {
    I: [
        [0, 0, 0, 0],
        [1, 1, 1, 1],
        [0, 0, 0, 0],
        [0, 0, 0, 0]
    ],
    J: [
        [1, 0, 0],
        [1, 1, 1],
        [0, 0, 0]
    ],
    L: [
        [0, 0, 1],
        [1, 1, 1],
        [0, 0, 0]
    ],
    O: [
        [1, 1],
        [1, 1]
    ],
    S: [
        [0, 1, 1],
        [1, 1, 0],
        [0, 0, 0]
    ],
    T: [
        [0, 1, 0],
        [1, 1, 1],
        [0, 0, 0]
    ],
    Z: [
        [1, 1, 0],
        [0, 1, 1],
        [0, 0, 0]
    ]
}

const TETROMINO_TYPES = Object.keys(TETROMINO_SHAPES)

/**
 * 隨機取得一種方塊的代號
 * @returns {string} 方塊代號(I/J/L/O/S/T/Z)
 */
function pickRandomTetrominoType() {
    const index = Math.floor(Math.random() * TETROMINO_TYPES.length)
    return TETROMINO_TYPES[index]
}

/**
 * 取得某種方塊的形狀矩陣(回傳複本，避免原始定義被修改)
 * @param {string} type 方塊代號
 * @returns {number[][]} 形狀矩陣
 */
function createTetrominoMatrix(type) {
    return TETROMINO_SHAPES[type].map(function (row) {
        return row.slice()
    })
}

/**
 * 將矩陣向左旋轉 90 度(逆時針)
 * @param {number[][]} matrix 來源矩陣
 * @returns {number[][]} 旋轉後的新矩陣
 */
function rotateMatrixLeft(matrix) {
    const size = matrix.length
    const result = []
    for (let row = 0; row < size; row = row + 1) {
        result.push(new Array(size).fill(0))
    }
    for (let row = 0; row < size; row = row + 1) {
        for (let column = 0; column < size; column = column + 1) {
            result[size - 1 - column][row] = matrix[row][column]
        }
    }
    return result
}

/**
 * 取出矩陣中所有實心格子的相對座標
 * @param {number[][]} matrix 形狀矩陣
 * @returns {{x:number,y:number}[]} 相對座標陣列
 */
function getFilledCells(matrix) {
    const cells = []
    for (let row = 0; row < matrix.length; row = row + 1) {
        for (let column = 0; column < matrix[row].length; column = column + 1) {
            if (matrix[row][column] !== 0) {
                cells.push({ x: column, y: row })
            }
        }
    }
    return cells
}

/**
 * index.js
 * 起始頁面：選擇難易度、顯示該難度的最高行數、開始遊戲。
 */

const StartPage = {

    /** 初始化頁面 */
    init: function () {
        this.difficultySelect = document.getElementById("difficulty")
        this.bestLinesElement = document.getElementById("bestLines")
        this.continueHint = document.getElementById("continueHint")

        this.difficultySelect.value = GameStorage.getDifficulty()
        this.updateBestLines()
        this.showContinueHintIfNeeded()
        this.bindEvents()
    },

    /** 綁定事件 */
    bindEvents: function () {
        this.difficultySelect.onchange = this.handleDifficultyChange.bind(this)
        document.getElementById("startButton").onclick = this.startGame.bind(this)
        document.getElementById("continueLink").onclick = function () {
            location.href = "main.html"
        }
    },

    /** 切換難度時同步更新最高行數 */
    handleDifficultyChange: function () {
        GameStorage.setDifficulty(this.difficultySelect.value)
        this.updateBestLines()
    },

    /** 顯示目前難度的最高行數 */
    updateBestLines: function () {
        const bestLines = GameStorage.getBestLines(this.difficultySelect.value)
        this.bestLinesElement.textContent = String(bestLines)
    },

    /** 若上一局尚未結束，提供直接接續的連結 */
    showContinueHintIfNeeded: function () {
        const savedGame = GameStorage.getSavedGame()
        if (savedGame !== null && savedGame.isOver !== true) {
            this.continueHint.classList.add("show")
        }
    },

    /** 以目前選擇的難度開始新的一局 */
    startGame: function () {
        GameStorage.setDifficulty(this.difficultySelect.value)
        GameStorage.clearSavedGame()      // 清掉舊的進度，確保是全新的一局
        location.href = "main.html"
    }
}

StartPage.init()

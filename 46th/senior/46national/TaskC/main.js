/**
 * main.js
 * 遊戲頁面的控制器：串接 GameEngine(邏輯) 與 GameView(畫面)，
 * 處理計時器、鍵盤操作、按鈕事件與遊戲進度的保存/還原。
 */

class GameController {

    constructor() {
        this.view = new GameView()
        this.engine = this.restoreOrCreateGame()
        this.dropTimer = null      // 方塊自動下降的計時器
        this.secondTimer = null    // 遊戲時間的計時器
        this.isLeaving = false     // 是否正在離開遊戲(離開後不再保存進度)
        this.bindEvents()
        this.view.render(this.engine)

        if (this.engine.isOver) {
            this.view.showGameOver(this.engine)
        } else if (this.engine.isPaused) {
            this.view.setPaused(true)
        } else {
            this.startTimers()
        }
    }

    /** 若上次離開時仍有未結束的遊戲則還原，否則以目前難度開新局 */
    restoreOrCreateGame() {
        const savedGame = GameStorage.getSavedGame()
        const restored = GameEngine.fromPlainObject(savedGame)
        if (restored !== null) {
            return restored
        }
        return new GameEngine(GameStorage.getDifficulty())
    }

    /** 保存目前的遊戲進度，讓玩家離開網頁後再進入可以接續 */
    saveGame() {
        if (this.isLeaving) {
            return
        }
        GameStorage.setSavedGame(this.engine.toPlainObject())
    }

    /** 啟動方塊下降與計時的計時器 */
    startTimers() {
        this.stopTimers()
        this.dropTimer = setInterval(this.handleAutoDrop.bind(this), this.engine.getDropInterval())
        this.secondTimer = setInterval(this.handleSecondTick.bind(this), 1000)
    }

    /** 停止所有計時器 */
    stopTimers() {
        clearInterval(this.dropTimer)
        clearInterval(this.secondTimer)
        this.dropTimer = null
        this.secondTimer = null
    }

    /** 方塊自動下降一格 */
    handleAutoDrop() {
        this.engine.stepDown()
        this.refresh()
    }

    /** 遊戲時間每秒前進 */
    handleSecondTick() {
        this.engine.tickSecond()
        this.refresh()
    }

    /** 更新畫面、保存進度，並在遊戲結束時切換到結束畫面 */
    refresh() {
        this.view.render(this.engine)
        this.saveGame()
        if (this.engine.isOver) {
            this.stopTimers()
            this.view.showGameOver(this.engine)
        }
    }

    /** 綁定鍵盤與按鈕事件 */
    bindEvents() {
        document.addEventListener("keydown", this.handleKeyDown.bind(this))
        document.getElementById("shareButton").onclick = this.handleShare.bind(this)
        document.getElementById("pauseButton").onclick = this.togglePause.bind(this)
        document.getElementById("quitButton").onclick = this.handleQuit.bind(this)
        document.getElementById("submitButton").onclick = this.handleSubmitRecord.bind(this)
        window.addEventListener("beforeunload", this.saveGame.bind(this))
    }

    /**
     * 鍵盤操作：上=向左旋轉90度、下=加速下降、左右=水平移動
     * 暫停或遊戲結束時拒絕所有按鍵操作
     */
    handleKeyDown(event) {
        if (!this.engine.canOperate()) {
            return
        }
        const keyActions = {
            ArrowUp: function () { this.engine.rotateBlock() },
            ArrowDown: function () { this.engine.stepDown() },
            ArrowLeft: function () { this.engine.moveBlock(-1, 0) },
            ArrowRight: function () { this.engine.moveBlock(1, 0) },
            " ": function () { this.engine.dropToBottom() }
        }
        const action = keyActions[event.key]
        if (action === undefined) {
            return
        }
        event.preventDefault()
        action.call(this)
        this.refresh()
    }

    /** 畫面分享：暫停遊戲 → 輸出 JPEG → 恢復原本狀態 */
    handleShare() {
        const wasPaused = this.engine.isPaused
        if (!wasPaused) {
            this.pauseGame()
        }
        this.view.downloadSnapshot(this.engine)
        if (!wasPaused) {
            this.resumeGame()
        }
    }

    /** 切換暫停 / 繼續遊戲 */
    togglePause() {
        if (this.engine.isOver) {
            return
        }
        if (this.engine.isPaused) {
            this.resumeGame()
        } else {
            this.pauseGame()
        }
    }

    /** 暫停遊戲：停止計時、隱藏遊戲畫面並顯示「暫停」 */
    pauseGame() {
        this.engine.isPaused = true
        this.stopTimers()
        this.view.setPaused(true)
        this.saveGame()
    }

    /** 繼續遊戲：畫面恢復、時間繼續計算、方塊繼續移動 */
    resumeGame() {
        this.engine.isPaused = false
        this.view.setPaused(false)
        this.startTimers()
        this.saveGame()
    }

    /** 放棄遊戲：確認後清除所有紀錄並回到起始頁面 */
    handleQuit() {
        const wasPaused = this.engine.isPaused
        if (!wasPaused) {
            this.pauseGame()
        }
        if (confirm("是否要放棄遊戲？")) {
            this.isLeaving = true
            this.stopTimers()
            GameStorage.clearSavedGame()
            location.href = "index.html"
            return
        }
        if (!wasPaused) {
            this.resumeGame()
        }
    }

    /** 送出暱稱：儲存這局的紀錄，並立即顯示該等級的排行榜 */
    handleSubmitRecord() {
        const nicknameInput = document.getElementById("nickname")
        const nickname = nicknameInput.value.trim()
        if (nickname === "") {
            alert("請輸入暱稱")
            nicknameInput.focus()
            return
        }
        GameStorage.addRankRecord({
            name: nickname,
            difficulty: this.engine.difficulty,
            lines: this.engine.lines,
            seconds: this.engine.elapsedSeconds,
            startTime: this.engine.startTime
        })
        this.isLeaving = true
        GameStorage.clearSavedGame()
        location.href = "rank.html?difficulty=" + this.engine.difficulty
    }
}

// 啟動遊戲
const gameController = new GameController()

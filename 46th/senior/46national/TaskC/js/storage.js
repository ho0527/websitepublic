/**
 * storage.js
 * 統一管理 localStorage 的存取，包含遊戲設定、暫存的遊戲進度與排行榜紀錄。
 * 以單一物件封裝，避免到處散落全域變數與字串 key。
 */

const GameStorage = {
    KEYS: {
        difficulty: "tetris.difficulty",   // 目前選擇的難度
        savedGame: "tetris.savedGame",     // 未完成的遊戲進度(離開網頁後可還原)
        rankList: "tetris.rankList",       // 排行榜紀錄
        lastRecord: "tetris.lastRecord"    // 玩家最近一次送出的紀錄編號
    },

    /** 難度代號與顯示文字的對應 */
    DIFFICULTY_TEXTS: {
        normal: "一般",
        hard: "困難"
    },

    /**
     * 讀取並解析 JSON 格式的資料
     * @param {string} key 儲存的 key
     * @param {*} defaultValue 找不到或格式錯誤時回傳的預設值
     */
    readJson: function (key, defaultValue) {
        const text = localStorage.getItem(key)
        if (text === null) {
            return defaultValue
        }
        try {
            return JSON.parse(text)
        } catch (error) {
            return defaultValue
        }
    },

    /** 以 JSON 格式寫入資料 */
    writeJson: function (key, value) {
        localStorage.setItem(key, JSON.stringify(value))
    },

    /** 取得目前選擇的難度(預設為一般) */
    getDifficulty: function () {
        const difficulty = localStorage.getItem(this.KEYS.difficulty)
        return difficulty === "hard" ? "hard" : "normal"
    },

    /** 設定目前選擇的難度 */
    setDifficulty: function (difficulty) {
        localStorage.setItem(this.KEYS.difficulty, difficulty)
    },

    /** 取得難度的顯示文字 */
    getDifficultyText: function (difficulty) {
        return this.DIFFICULTY_TEXTS[difficulty] || this.DIFFICULTY_TEXTS.normal
    },

    /** 取得尚未結束的遊戲進度，沒有則回傳 null */
    getSavedGame: function () {
        return this.readJson(this.KEYS.savedGame, null)
    },

    /** 保存遊戲進度 */
    setSavedGame: function (state) {
        this.writeJson(this.KEYS.savedGame, state)
    },

    /** 清除遊戲進度 */
    clearSavedGame: function () {
        localStorage.removeItem(this.KEYS.savedGame)
    },

    /** 取得全部排行榜紀錄 */
    getRankList: function () {
        const list = this.readJson(this.KEYS.rankList, [])
        return Array.isArray(list) ? list : []
    },

    /**
     * 新增一筆排行榜紀錄
     * @param {{name:string,difficulty:string,lines:number,seconds:number,startTime:number}} record
     * @returns {number} 這筆紀錄的編號
     */
    addRankRecord: function (record) {
        const list = this.getRankList()
        const id = Date.now() + Math.floor(Math.random() * 1000)
        list.push(Object.assign({ id: id }, record))
        this.writeJson(this.KEYS.rankList, list)
        localStorage.setItem(this.KEYS.lastRecord, String(id))
        return id
    },

    /** 取得玩家最近一次送出的紀錄編號 */
    getLastRecordId: function () {
        const id = localStorage.getItem(this.KEYS.lastRecord)
        return id === null ? null : Number(id)
    },

    /**
     * 取得某難度的最高行數
     * @param {string} difficulty 難度代號
     * @returns {number} 最高行數，沒有紀錄時為 0
     */
    getBestLines: function (difficulty) {
        const records = this.getRankList().filter(function (record) {
            return record.difficulty === difficulty
        })
        if (records.length === 0) {
            return 0
        }
        return records.reduce(function (best, record) {
            return record.lines > best ? record.lines : best
        }, 0)
    }
}

/**
 * 將秒數格式化為 MM:SS
 * @param {number} totalSeconds 總秒數
 * @returns {string} MM:SS 格式的字串
 */
function formatTime(totalSeconds) {
    const minutes = Math.floor(totalSeconds / 60)
    const seconds = totalSeconds % 60
    return String(minutes).padStart(2, "0") + ":" + String(seconds).padStart(2, "0")
}

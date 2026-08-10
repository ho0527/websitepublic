/**
 * rank.js
 * 排行榜頁面：切換難度頁籤、顯示前五名與玩家自己的名次。
 */

const RankPage = {

    TOP_COUNT: 5,   // 排行榜顯示的名單數量

    /** 初始化頁面，預設顯示網址參數或最近一次遊玩的難度 */
    init: function () {
        this.rankBody = document.getElementById("rankBody")
        this.playerRecordId = GameStorage.getLastRecordId()
        this.bindTabs()
        this.showDifficulty(this.getDefaultDifficulty())
    },

    /** 取得預設要顯示的難度 */
    getDefaultDifficulty: function () {
        const difficulty = new URLSearchParams(location.search).get("difficulty")
        if (difficulty === "normal" || difficulty === "hard") {
            return difficulty
        }
        return GameStorage.getDifficulty()
    },

    /** 綁定頁籤的切換事件 */
    bindTabs: function () {
        const tabs = document.querySelectorAll(".tab")
        tabs.forEach(function (tab) {
            tab.onclick = function () {
                RankPage.showDifficulty(tab.dataset.difficulty)
            }
        })
    },

    /**
     * 顯示指定難度的排行榜
     * @param {string} difficulty 難度代號
     */
    showDifficulty: function (difficulty) {
        document.querySelectorAll(".tab").forEach(function (tab) {
            tab.classList.toggle("active", tab.dataset.difficulty === difficulty)
        })
        this.renderRecords(difficulty)
    },

    /** 依難度繪製名單：前五名 + 玩家自己的名次 */
    renderRecords: function (difficulty) {
        const records = RankingService.getSortedRecords(difficulty)
        const playerRank = RankingService.getRankOfRecord(difficulty, this.playerRecordId)
        this.rankBody.innerHTML = ""

        if (records.length === 0) {
            const emptyRow = document.createElement("tr")
            emptyRow.innerHTML = "<td colspan=\"4\" class=\"empty\">尚無紀錄</td>"
            this.rankBody.appendChild(emptyRow)
            return
        }

        // 前五名
        const topRecords = records.slice(0, this.TOP_COUNT)
        for (let index = 0; index < topRecords.length; index = index + 1) {
            this.rankBody.appendChild(this.createRow(topRecords[index], index + 1, playerRank))
        }

        // 玩家不在前五名時，另外標示並顯示玩家的名次
        if (playerRank > this.TOP_COUNT) {
            const gapRow = document.createElement("tr")
            gapRow.innerHTML = "<td colspan=\"4\" class=\"gap\">⋮</td>"
            this.rankBody.appendChild(gapRow)
            this.rankBody.appendChild(this.createRow(records[playerRank - 1], playerRank, playerRank))
        }
    },

    /**
     * 建立一列排行榜資料
     * @param {object} record 紀錄
     * @param {number} rank 名次
     * @param {number} playerRank 玩家的名次(用來標示玩家自己)
     */
    createRow: function (record, rank, playerRank) {
        const row = document.createElement("tr")
        if (rank === playerRank) {
            row.classList.add("player")          // 標示目前玩家
        }
        row.innerHTML = `
            <td>${rank}</td>
            <td>${record.name}</td>
            <td>${record.lines.toLocaleString()}</td>
            <td>${formatTime(record.seconds)}</td>
        `
        return row
    }
}

RankPage.init()

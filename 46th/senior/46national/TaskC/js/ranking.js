/**
 * ranking.js
 * 排行榜的排序與名次計算。
 * 排序規則：行數由大到小 → 遊戲時間由少到多 → 開始遊戲的時間由先到後。
 */

const RankingService = {
    /**
     * 取得某難度排序後的完整名單
     * @param {string} difficulty 難度代號
     * @returns {object[]} 已排序的紀錄陣列
     */
    getSortedRecords: function (difficulty) {
        const records = GameStorage.getRankList().filter(function (record) {
            return record.difficulty === difficulty
        })
        records.sort(function (left, right) {
            if (left.lines !== right.lines) {
                return right.lines - left.lines          // 行數由大到小
            }
            if (left.seconds !== right.seconds) {
                return left.seconds - right.seconds      // 時間由少到多
            }
            return left.startTime - right.startTime      // 開始遊戲時間由先到後
        })
        return records
    },

    /**
     * 找出指定紀錄在該難度中的名次
     * @param {string} difficulty 難度代號
     * @param {number} recordId 紀錄編號
     * @returns {number} 名次(從 1 開始)，找不到時回傳 -1
     */
    getRankOfRecord: function (difficulty, recordId) {
        const records = this.getSortedRecords(difficulty)
        for (let index = 0; index < records.length; index = index + 1) {
            if (records[index].id === recordId) {
                return index + 1
            }
        }
        return -1
    }
}

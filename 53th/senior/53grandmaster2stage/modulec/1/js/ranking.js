/**
 * Star Battle - 排行榜
 *
 * 需求：
 *   遊戲結束後以 AJAX（POST）把 name / time / score 送到 register.php
 *   伺服器回傳未排序的 JSON，前端負責排序後呈現
 *   排序規則：先比分數（大到小），分數相同再比飛行時間（大到小）
 *   分數與時間都相同的人共用同一個名次
 *   欄位順序：position、name、score、time
 */
(function (global) {
    'use strict';

    /** 伺服器端註冊程式的位址 */
    var REGISTER_URL = 'php/register.php';

    function RankingBoard() {
        this.tableBody = document.getElementById('rankingBody');
        this.message = document.getElementById('rankingMessage');
    }

    /**
     * 送出本局成績並取回排行榜資料
     *
     * @param {{name: string, time: number, score: number}} record
     * @returns {Promise<Array>} 伺服器回傳的排行榜資料
     */
    RankingBoard.prototype.submit = function (record) {
        var body = new FormData();
        body.append('name', record.name);
        body.append('time', String(record.time));
        body.append('score', String(record.score));

        return fetch(REGISTER_URL, { method: 'POST', body: body })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            });
    };

    /**
     * 依規則排序：分數大到小，分數相同時飛行時間大到小
     *
     * @param {Array} rows 伺服器回傳（未排序）的資料
     * @returns {Array} 已排序且帶有 position 的資料
     */
    RankingBoard.prototype.sort = function (rows) {
        var sorted = rows.slice().sort(function (a, b) {
            var scoreDifference = Number(b.score) - Number(a.score);
            if (scoreDifference !== 0) {
                return scoreDifference;
            }
            return Number(b.time) - Number(a.time);
        });

        var position = 0;
        var previous = null;

        sorted.forEach(function (row, index) {
            // 分數與時間都相同的人共用名次
            var isTied = previous !== null
                && Number(previous.score) === Number(row.score)
                && Number(previous.time) === Number(row.time);

            if (!isTied) {
                position = index + 1;
            }

            row.position = position;
            previous = row;
        });

        return sorted;
    };

    /**
     * 繪製排行榜表格
     *
     * @param {Array} rows        伺服器資料
     * @param {Object} [highlight] 要標示的本局紀錄 {name, time, score}
     */
    RankingBoard.prototype.render = function (rows, highlight) {
        var sorted = this.sort(rows || []);
        var tableBody = this.tableBody;
        var highlighted = false;

        tableBody.textContent = '';

        if (sorted.length === 0) {
            tableBody.appendChild(this.createEmptyRow());
            return;
        }

        sorted.forEach(function (row) {
            var tableRow = document.createElement('tr');

            // 只標示第一筆完全符合的紀錄（本局玩家）
            if (!highlighted && highlight
                && String(row.name) === String(highlight.name)
                && Number(row.time) === Number(highlight.time)
                && Number(row.score) === Number(highlight.score)) {
                tableRow.className = 'is-current';
                highlighted = true;
            }

            [row.position, row.name, row.score, row.time + 's'].forEach(function (value) {
                var cell = document.createElement('td');
                // 使用 textContent 輸出，避免玩家名稱造成 XSS
                cell.textContent = String(value);
                tableRow.appendChild(cell);
            });

            tableBody.appendChild(tableRow);
        });
    };

    /** 產生「沒有資料」的列 */
    RankingBoard.prototype.createEmptyRow = function () {
        var row = document.createElement('tr');
        var cell = document.createElement('td');
        cell.colSpan = 4;
        cell.textContent = 'No data.';
        row.appendChild(cell);
        return row;
    };

    /** 顯示提示訊息 */
    RankingBoard.prototype.setMessage = function (text) {
        this.message.textContent = text || '';
    };

    global.StarBattle = global.StarBattle || {};
    global.StarBattle.RankingBoard = RankingBoard;
}(window));

/**
 * Star Battle - 畫面流程與使用者互動
 *
 * 負責：三個畫面（遊戲說明 / 遊戲畫面 / 排行榜）的切換、
 *       按鈕與鍵盤事件綁定、感應區控制、字級調整、音效開關、
 *       遊戲結束後送出成績並顯示排行榜。
 */
(function (global) {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var StarBattle = global.StarBattle;

        /* ---------- 常用節點 ---------- */
        var dom = {
            game: document.getElementById('game'),
            field: document.getElementById('field'),
            player: document.getElementById('player'),
            dpad: document.getElementById('dpad'),
            planetLayer: document.getElementById('planetLayer'),
            timerValue: document.getElementById('timerValue'),
            scoreValue: document.getElementById('scoreValue'),
            fuelValue: document.getElementById('fuelValue'),
            fuelBar: document.getElementById('fuelBar'),
            scoreCounter: document.querySelector('.counter--score'),
            fuelCounter: document.querySelector('.counter--fuel'),
            pauseOverlay: document.getElementById('pauseOverlay'),
            gameOverOverlay: document.getElementById('gameOverOverlay'),
            finalScore: document.getElementById('finalScore'),
            finalTime: document.getElementById('finalTime'),
            screens: {
                instructions: document.getElementById('screenInstructions'),
                board: document.getElementById('screenBoard'),
                ranking: document.getElementById('screenRanking')
            }
        };

        var sound = new StarBattle.SoundManager();
        var ranking = new StarBattle.RankingBoard();
        var game = new StarBattle.Game(dom, sound);

        /** 本局成績，於遊戲結束時填入 */
        var currentRecord = { name: '', time: 0, score: 0 };

        /* ================= 畫面切換 ================= */

        /**
         * 顯示指定畫面
         * @param {string} name instructions | board | ranking
         */
        function showScreen(name) {
            Object.keys(dom.screens).forEach(function (key) {
                dom.screens[key].classList.toggle('is-active', key === name);
            });

            if (name === 'instructions') {
                animateInstructions();
            }
        }

        /** 遊戲說明逐條淡入的動畫 */
        function animateInstructions() {
            var items = document.querySelectorAll('#instructionList li');

            Array.prototype.forEach.call(items, function (item, index) {
                // 重新播放動畫
                item.style.animation = 'none';
                void item.offsetWidth;
                item.style.animation = '';
                item.style.animationDelay = (0.35 + index * 0.12) + 's';
            });
        }

        /* ================= 遊戲開始 / 結束 ================= */

        /** 開始遊戲 */
        function startGame() {
            showScreen('board');
            game.start();
        }

        // 「Start Game」按鈕（遊戲說明與排行榜畫面各一顆）
        Array.prototype.forEach.call(
            document.querySelectorAll('[data-action="start-game"]'),
            function (button) {
                button.addEventListener('click', function () {
                    // 從排行榜按下時，先回到遊戲說明畫面（試題要求）
                    if (dom.screens.ranking.classList.contains('is-active')) {
                        showScreen('instructions');
                        return;
                    }
                    startGame();
                });
            }
        );

        // 遊戲結束：顯示名稱輸入表單
        game.onGameOver = function (result) {
            currentRecord.score = result.score;
            currentRecord.time = result.time;
            currentRecord.name = '';

            nameInput.value = '';
            continueButton.disabled = true;
            nameInput.focus();
        };

        /* ================= 遊戲結束表單 ================= */

        var gameOverForm = document.getElementById('gameOverForm');
        var nameInput = document.getElementById('playerName');
        var continueButton = document.getElementById('continueButton');

        // 名稱填寫前，Continue 按鈕維持停用
        nameInput.addEventListener('input', function () {
            continueButton.disabled = nameInput.value.trim() === '';
        });

        gameOverForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var name = nameInput.value.trim();
            if (name === '') {
                return;
            }

            currentRecord.name = name;
            continueButton.disabled = true;

            game.stop();
            showScreen('ranking');
            ranking.setMessage('Sending your score…');

            // 以 AJAX 將 name / time / score 送到伺服器，並取回排行榜資料
            ranking.submit(currentRecord)
                .then(function (rows) {
                    ranking.render(rows, currentRecord);
                    ranking.setMessage('');
                })
                .catch(function (error) {
                    // 伺服器無法連線時，至少顯示本局成績，避免畫面空白
                    ranking.render([currentRecord], currentRecord);
                    ranking.setMessage('Could not reach the server (' + error.message + '), showing your local result only.');
                });
        });

        /* ================= 感應區（十字方向控制） ================= */

        Array.prototype.forEach.call(dom.dpad.querySelectorAll('[data-direction]'), function (area) {
            var direction = area.getAttribute('data-direction');

            // 如同遊戲手把：滑鼠移入啟動、移出停止
            area.addEventListener('mouseenter', function () {
                area.classList.add('is-active');
                game.setDirection(direction, true);
            });

            area.addEventListener('mouseleave', function () {
                area.classList.remove('is-active');
                game.setDirection(direction, false);
            });

            // 觸控裝置也能操作
            area.addEventListener('touchstart', function (event) {
                event.preventDefault();
                area.classList.add('is-active');
                game.setDirection(direction, true);
            });

            area.addEventListener('touchend', function () {
                area.classList.remove('is-active');
                game.setDirection(direction, false);
            });
        });

        /* ================= 鍵盤 ================= */

        document.addEventListener('keydown', function (event) {
            // 在輸入名稱時不攔截按鍵
            if (event.target === nameInput) {
                return;
            }

            if (event.code === 'Space' || event.key === ' ') {
                event.preventDefault();
                game.playerShoot();
                return;
            }

            if (event.key === 'p' || event.key === 'P') {
                togglePause();
            }
        });

        // 放開空白鍵後才能再射擊一次（無法按住連射）
        document.addEventListener('keyup', function (event) {
            if (event.code === 'Space' || event.key === ' ') {
                game.releaseShoot();
            }
        });

        /* ================= HUD 按鈕 ================= */

        var pauseButton = document.getElementById('pauseButton');
        var pauseIcon = document.getElementById('pauseIcon');
        var soundButton = document.getElementById('soundButton');
        var soundIcon = document.getElementById('soundIcon');

        /** 切換暫停狀態並更新按鈕圖示 */
        function togglePause() {
            var isPaused = game.togglePause();
            pauseIcon.setAttribute('href', isPaused ? '#icon-play' : '#icon-pause');
            pauseButton.setAttribute('title', isPaused ? 'Continue' : 'Pause');
        }

        pauseButton.addEventListener('click', togglePause);

        // 音效開關
        soundButton.addEventListener('click', function () {
            var isEnabled = sound.toggle();

            soundIcon.setAttribute('href', isEnabled ? '#icon-sound-on' : '#icon-sound-off');
            soundButton.classList.toggle('is-off', !isEnabled);
            soundButton.setAttribute('aria-pressed', String(isEnabled));
        });

        /* ================= 字級調整（無障礙） ================= */

        var FONT_SCALE_MIN = 0.7;
        var FONT_SCALE_MAX = 1.8;
        var FONT_SCALE_STEP = 0.15;
        var fontScale = 1;

        /** 套用字級倍率到計時器與分數（HUD） */
        function applyFontScale(nextScale) {
            fontScale = Math.min(FONT_SCALE_MAX, Math.max(FONT_SCALE_MIN, nextScale));
            dom.game.style.setProperty('--hud-font-scale', String(fontScale.toFixed(2)));
        }

        document.getElementById('fontUpButton').addEventListener('click', function () {
            applyFontScale(fontScale + FONT_SCALE_STEP);
        });

        document.getElementById('fontDownButton').addEventListener('click', function () {
            applyFontScale(fontScale - FONT_SCALE_STEP);
        });

        /* ================= 初始畫面 ================= */

        showScreen('instructions');
    });
}(window));

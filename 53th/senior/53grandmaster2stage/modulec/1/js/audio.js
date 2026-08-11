/**
 * Star Battle - 音效管理
 *
 * 需求：
 *   background.mp3 於飛行中循環播放
 *   destroy（destroyed）於飛船或小行星被摧毀時播放
 *   shoot.mp3 於主飛船射擊時播放
 *   可由按鈕整體開啟／關閉所有音效；關閉時任何音效都不會發出聲音
 */
(function (global) {
    'use strict';

    /** 音效管理器 */
    function SoundManager() {
        this.enabled = true;
        /** 目前是否應該播放背景音樂（遊戲進行中為 true） */
        this.backgroundActive = false;
        this.background = document.getElementById('soundBackground');
        this.destroy = document.getElementById('soundDestroy');
        this.shoot = document.getElementById('soundShoot');
        this.background.volume = 0.35;
        this.destroy.volume = 0.6;
        this.shoot.volume = 0.4;
    }

    /** 播放背景音樂（循環） */
    SoundManager.prototype.startBackground = function () {
        this.backgroundActive = true;
        if (!this.enabled) {
            return;
        }
        this.background.currentTime = 0;
        this.play(this.background);
    };

    /** 停止背景音樂 */
    SoundManager.prototype.stopBackground = function () {
        this.backgroundActive = false;
        this.background.pause();
    };

    /** 暫停背景音樂（保留播放位置，供暫停功能使用） */
    SoundManager.prototype.pauseBackground = function () {
        this.background.pause();
    };

    /** 從暫停的位置繼續播放背景音樂 */
    SoundManager.prototype.resumeBackground = function () {
        if (this.enabled && this.backgroundActive) {
            this.play(this.background);
        }
    };

    /** 播放射擊音效 */
    SoundManager.prototype.playShoot = function () {
        this.playEffect(this.shoot);
    };

    /** 播放摧毀音效 */
    SoundManager.prototype.playDestroy = function () {
        this.playEffect(this.destroy);
    };

    /**
     * 播放短音效：複製節點才能在音效重疊時同時發聲
     */
    SoundManager.prototype.playEffect = function (audioElement) {
        if (!this.enabled) {
            return;
        }
        var clone = audioElement.cloneNode();
        clone.volume = audioElement.volume;
        this.play(clone);
    };

    /**
     * 統一的播放方法：瀏覽器可能因自動播放政策拒絕播放，
     * 這裡吞掉該 Promise 的錯誤，避免主控台出現未處理的例外
     */
    SoundManager.prototype.play = function (audioElement) {
        var playPromise = audioElement.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(function () {
                /* 使用者尚未與頁面互動時瀏覽器會拒絕播放，忽略即可 */
            });
        }
    };

    /**
     * 切換音效開關
     * @returns {boolean} 切換後是否為開啟狀態
     */
    SoundManager.prototype.toggle = function () {
        this.enabled = !this.enabled;

        if (this.enabled) {
            this.resumeBackground();
        } else {
            // 關閉時只是停止發聲，backgroundActive 維持不變，重新開啟後可繼續播放
            this.background.pause();
        }

        return this.enabled;
    };

    global.StarBattle = global.StarBattle || {};
    global.StarBattle.SoundManager = SoundManager;
}(window));

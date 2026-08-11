/**
 * avatar.js — 大頭貼處理
 * 驗證檔案格式，並將圖片壓縮成 120px * 120px 的 JPEG（base64 dataURL）。
 */
window.App = window.App || {};

App.Avatar = (function () {
    'use strict';

    var SIZE = 120;                                        // 儲存尺寸 120px * 120px
    var ACCEPT = ['image/jpeg', 'image/png'];              // 試題指定可選取的格式
    var DEFAULT_SRC = 'img/default-avatar.png';            // 預設大頭貼（路徑，不可為 base64）

    /** 檢查檔案是否為允許的圖片格式 */
    function isValid(file) {
        if (!file) {
            return false;
        }
        if (ACCEPT.indexOf(file.type) !== -1) {
            return true;
        }
        // 部分環境不會帶入 MIME type，退而以副檔名判斷
        return /\.(jpe?g|png)$/i.test(file.name || '');
    }

    /**
     * 將使用者選取的檔案壓縮為 120x120 的 JPEG dataURL。
     * 採用置中裁切（cover），避免圖片變形。
     */
    function compress(file) {
        return new Promise(function (resolve, reject) {
            if (!isValid(file)) {
                reject(new Error('invalid-avatar'));
                return;
            }
            var reader = new FileReader();
            reader.onload = function () {
                var image = new Image();
                image.onload = function () {
                    var canvas = document.createElement('canvas');
                    canvas.width = SIZE;
                    canvas.height = SIZE;

                    var context = canvas.getContext('2d');
                    context.fillStyle = '#ffffff';
                    context.fillRect(0, 0, SIZE, SIZE);

                    // 以較短邊為基準置中裁切
                    var side = Math.min(image.width, image.height);
                    var sourceX = (image.width - side) / 2;
                    var sourceY = (image.height - side) / 2;
                    context.drawImage(image, sourceX, sourceY, side, side, 0, 0, SIZE, SIZE);

                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                };
                image.onerror = function () { reject(new Error('invalid-avatar')); };
                image.src = reader.result;
            };
            reader.onerror = function () { reject(new Error('invalid-avatar')); };
            reader.readAsDataURL(file);
        });
    }

    return {
        SIZE: SIZE,
        ACCEPT: ACCEPT,
        DEFAULT_SRC: DEFAULT_SRC,
        isValid: isValid,
        compress: compress
    };
})();

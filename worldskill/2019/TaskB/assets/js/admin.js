/**
 * 後台輔助腳本
 *
 *   1. 圖片下拉選單即時預覽
 *   2. 由標題自動填入 URL slug（僅在 slug 尚未填寫時）
 *   3. slug 預覽文字同步
 */
(function () {
    'use strict';

    /* 1. 圖片預覽 */
    document.querySelectorAll('[data-image-picker]').forEach(function (select) {
        var preview = document.getElementById(select.dataset.preview);
        if (!preview) {
            return;
        }

        // 由已載入的預覽圖推算網站根路徑
        var basePath = (preview.getAttribute('src') || '').replace(/uploads\/.*$/, '');

        select.addEventListener('change', function () {
            preview.src = select.value ? basePath + select.value : '';
        });
    });

    /* 2 & 3. 標題 → slug */
    var titleField = document.getElementById('title');
    var slugField = document.getElementById('slug');
    var slugPreview = document.querySelector('[data-slug-preview]');

    function slugify(value) {
        return value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9À-ɏ]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function updatePreview() {
        if (slugPreview && slugField) {
            slugPreview.textContent = slugField.value || 'museum-name';
        }
    }

    if (titleField && slugField) {
        var slugTouched = slugField.value !== '';

        slugField.addEventListener('input', function () {
            slugTouched = true;
            updatePreview();
        });

        titleField.addEventListener('input', function () {
            if (!slugTouched) {
                slugField.value = slugify(titleField.value);
                updatePreview();
            }
        });
    }

    updatePreview();
})();

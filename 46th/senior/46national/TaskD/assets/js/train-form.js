/**
 * 後台列車表單：管理行經車站的列。
 *
 * 站數限制與「相同車站不可重複」在送出前先於瀏覽器提示，
 * 伺服器端仍會再驗證一次，避免直接繞過前端檢查。
 */
(function () {
    'use strict';

    var config   = window.trainFormConfig || { minStops: 2, maxStops: 15 };
    var rowsHost = document.getElementById('stop-rows');
    var template = document.getElementById('stop-row-template');
    var addButton = document.getElementById('add-stop');
    var form     = rowsHost ? rowsHost.closest('form') : null;

    if (!rowsHost || !template || !addButton || !form) {
        return;
    }

    /**
     * 重新編號站序，並依「發車站沒有行駛時間、發車站與終點站沒有停留時間」停用不適用的欄位。
     */
    function refreshRows() {
        var rows = rowsHost.querySelectorAll('.stop-row');

        for (var index = 0; index < rows.length; index++) {
            var row        = rows[index];
            var isFirst    = index === 0;
            var isLast     = index === rows.length - 1;
            var travel     = row.querySelector('input[name="travel_minutes[]"]');
            var stopTime   = row.querySelector('input[name="stop_minutes[]"]');
            var fare       = row.querySelector('input[name="fare_from_origin[]"]');

            row.querySelector('.stop-index').textContent = String(index + 1);

            // 發車站的行駛時間與累計票價固定為 0
            travel.readOnly = isFirst;
            fare.readOnly   = isFirst;

            if (isFirst) {
                travel.value = '0';
                fare.value   = '0';
            }

            // 發車站與終點站沒有停留時間
            stopTime.readOnly = isFirst || isLast;

            if (isFirst || isLast) {
                stopTime.value = '0';
            }
        }

        addButton.disabled = rows.length >= config.maxStops;
    }

    /**
     * 新增一列停靠站。
     */
    addButton.addEventListener('click', function () {
        if (rowsHost.querySelectorAll('.stop-row').length >= config.maxStops) {
            window.alert('行經車站最多只能有 ' + config.maxStops + ' 站');

            return;
        }

        rowsHost.appendChild(template.content.cloneNode(true));
        refreshRows();
    });

    /**
     * 移除一列停靠站。
     */
    rowsHost.addEventListener('click', function (event) {
        if (!event.target.classList.contains('stop-remove')) {
            return;
        }

        if (rowsHost.querySelectorAll('.stop-row').length <= config.minStops) {
            window.alert('行經車站至少要有 ' + config.minStops + ' 站（發車站與終點站）');

            return;
        }

        event.target.closest('.stop-row').remove();
        refreshRows();
    });

    /**
     * 送出前檢查站數與重複車站。
     */
    form.addEventListener('submit', function (event) {
        var selects = rowsHost.querySelectorAll('select[name="station_id[]"]');
        var chosen  = [];

        for (var index = 0; index < selects.length; index++) {
            var value = selects[index].value;

            if (value === '') {
                continue;
            }

            if (chosen.indexOf(value) !== -1) {
                event.preventDefault();
                window.alert('行經車站不可重複選取相同的車站，請修改後再送出');

                return;
            }

            chosen.push(value);
        }

        if (chosen.length < config.minStops) {
            event.preventDefault();
            window.alert('行經車站至少要有 ' + config.minStops + ' 站（發車站與終點站）');

            return;
        }

        if (chosen.length > config.maxStops) {
            event.preventDefault();
            window.alert('行經車站最多只能有 ' + config.maxStops + ' 站');
        }
    });

    refreshRows();
})();

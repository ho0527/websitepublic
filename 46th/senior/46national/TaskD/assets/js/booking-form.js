/**
 * 訂票表單：選擇車次後，把起訖站的下拉選單限縮為該車次實際行經的車站。
 */
(function () {
    'use strict';

    var config = window.railBookingConfig || {};

    var trainSelect = document.getElementById('train_code');
    var fromSelect  = document.getElementById('from_station');
    var toSelect    = document.getElementById('to_station');

    if (!trainSelect || !fromSelect || !toSelect) {
        return;
    }

    /** 車次未選擇時要還原的完整車站清單 */
    var allStationOptions = {
        from: fromSelect.innerHTML,
        to:   toSelect.innerHTML
    };

    /**
     * 以行經車站重建下拉選單，並盡量保留原本選到的車站。
     */
    function fillStations(select, stops, placeholder) {
        var previous = select.value || select.getAttribute('data-selected') || '';
        var html     = '<option value="">' + placeholder + '</option>';

        stops.forEach(function (stop) {
            var selected = stop.code === previous ? ' selected' : '';
            html += '<option value="' + stop.code + '"' + selected + '>'
                + stop.sequence + '. ' + stop.name + '</option>';
        });

        select.innerHTML = html;
    }

    /**
     * 載入指定車次的行經車站。
     */
    function loadStops(trainCode) {
        if (!trainCode) {
            fromSelect.innerHTML = allStationOptions.from;
            toSelect.innerHTML   = allStationOptions.to;

            return;
        }

        fetch(config.trainStopsUrl + '/' + encodeURIComponent(trainCode), { credentials: 'same-origin' })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.success) {
                    return;
                }

                fillStations(fromSelect, data.stops, '請選擇起程站');
                fillStations(toSelect, data.stops, '請選擇到達站');
            });
    }

    trainSelect.addEventListener('change', function () {
        loadStops(trainSelect.value);
    });

    // 由車次查詢帶入車次時，進入頁面就先載入該車次的行經車站
    if (trainSelect.value) {
        loadStops(trainSelect.value);
    }
})();

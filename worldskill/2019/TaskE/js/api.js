/**
 * REST API 用戶端
 * 對應 Task C 實作的參加者 API（規格第一階段 B1 ~ B4）
 */
(function (global) {
    'use strict';

    /**
     * API 進入點。
     * 因為沒有修改 nginx.conf，實際使用 PATH_INFO 形式的網址；
     * 若日後在 nginx 加上 rewrite，只要把這裡改成 '../TaskC/api/v1' 即可。
     */
    var API_BASE = '../TaskC/api/v1/index.php';

    var TOKEN_KEY = 'wsc2019_attendee';

    /**
     * 取出已登入的使用者資料（含 token）；未登入回傳 null
     */
    function getUser() {
        try {
            return JSON.parse(localStorage.getItem(TOKEN_KEY)) || null;
        } catch (error) {
            return null;
        }
    }

    /**
     * 寫入或清除登入資料
     */
    function setUser(user) {
        if (user) {
            localStorage.setItem(TOKEN_KEY, JSON.stringify(user));
        } else {
            localStorage.removeItem(TOKEN_KEY);
        }
    }

    /**
     * 送出請求並回傳 { ok, status, data }
     */
    function request(path, options) {
        options = options || {};
        var init = { method: options.method || 'GET', headers: {} };

        if (options.body !== undefined) {
            // 依規格：送出 JSON 時必須設定對應的 Content-Type
            init.headers['Content-Type'] = 'application/json';
            init.body = JSON.stringify(options.body);
        }

        return fetch(API_BASE + path, init).then(function (response) {
            return response.json().catch(function () {
                return {};
            }).then(function (data) {
                return { ok: response.ok, status: response.status, data: data };
            });
        }).catch(function () {
            return { ok: false, status: 0, data: { message: 'Network error' } };
        });
    }

    /**
     * 在網址後面附加 token 查詢字串
     */
    function withToken(path) {
        var user = getUser();
        var token = user ? user.token : '';
        return path + (path.indexOf('?') === -1 ? '?' : '&') + 'token=' + encodeURIComponent(token);
    }

    global.Api = {
        getUser: getUser,
        setUser: setUser,

        /** B1a - 取得所有即將舉行的活動 */
        listEvents: function () {
            return request('/events');
        },

        /** B2a - 取得單一活動的完整資料 */
        getEvent: function (organizerSlug, eventSlug) {
            return request('/organizers/' + encodeURIComponent(organizerSlug) +
                '/events/' + encodeURIComponent(eventSlug));
        },

        /** B3a - 參加者登入 */
        login: function (lastname, registrationCode) {
            return request('/login', {
                method: 'POST',
                body: { lastname: lastname, registration_code: registrationCode }
            });
        },

        /** B3b - 參加者登出 */
        logout: function () {
            return request(withToken('/logout'), { method: 'POST' });
        },

        /** B4b - 取得自己的報名紀錄 */
        listRegistrations: function () {
            return request(withToken('/registrations'));
        },

        /** B4a - 報名活動並購票 */
        register: function (organizerSlug, eventSlug, ticketId, sessionIds) {
            var path = '/organizers/' + encodeURIComponent(organizerSlug) +
                '/events/' + encodeURIComponent(eventSlug) + '/registration';
            return request(withToken(path), {
                method: 'POST',
                body: { ticket_id: ticketId, session_ids: sessionIds }
            });
        }
    };
}(window));

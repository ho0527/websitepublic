/**
 * 參加者前端（規格第二階段 C1 ~ C5）
 *
 * 採用 hash 路由，所有畫面狀態都保存在網址中：
 *   #/                                          活動列表
 *   #/events/{organizer}/{event}                活動議程
 *   #/events/{organizer}/{event}/sessions/{id}  議程詳細
 *   #/events/{organizer}/{event}/register       報名購票
 *   #/login?next=...                            登入
 * 其餘網址一律顯示錯誤頁。
 */
(function () {
    'use strict';

    var app = document.getElementById('app');
    var nav = document.getElementById('nav');
    var flash = document.getElementById('flash');

    // 報名成功後導回議程頁時，讓提示訊息只保留一次
    var keepFlashOnce = false;

    // 議程時間軸的範圍（09:00 ~ 17:00）與必須顯示的刻度
    var TIMELINE_START = 9 * 60;
    var TIMELINE_END = 17 * 60;
    var TIMELINE_TICKS = [9, 11, 13, 15];

    // -----------------------------------------------------------------------
    // 共用工具
    // -----------------------------------------------------------------------

    /**
     * HTML 跳脫，避免 API 回傳的內容造成 XSS
     */
    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    /**
     * 解析 "2019-09-23 09:00:00" 這種格式（避免各瀏覽器解析差異）
     */
    function parseDateTime(value) {
        var match = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/.exec(String(value || ''));
        if (!match) {
            return null;
        }
        return new Date(+match[1], +match[2] - 1, +match[3], +match[4], +match[5]);
    }

    /**
     * 取得該時間在一天中的分鐘數
     */
    function minutesOfDay(value) {
        var date = parseDateTime(value);
        return date ? date.getHours() * 60 + date.getMinutes() : 0;
    }

    /**
     * 顯示成 "9:00"
     */
    function formatTime(value) {
        var date = parseDateTime(value);
        if (!date) {
            return '';
        }
        return date.getHours() + ':' + ('0' + date.getMinutes()).slice(-2);
    }

    /**
     * 顯示成 "September 23, 2019"
     */
    function formatDate(value) {
        var months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        var parts = /^(\d{4})-(\d{2})-(\d{2})/.exec(String(value || ''));
        if (!parts) {
            return '';
        }
        return months[+parts[2] - 1] + ' ' + (+parts[3]) + ', ' + parts[1];
    }

    /**
     * 金額顯示成 "210.-"
     */
    function formatCost(value) {
        var number = Number(value || 0);
        var text = number % 1 === 0 ? String(number) : number.toFixed(2);
        return text + '.-';
    }

    /**
     * 顯示一次性提示訊息
     */
    function showFlash(message, type) {
        flash.textContent = message;
        flash.className = 'flash flash-' + (type || 'success');
        flash.hidden = false;
    }

    function clearFlash() {
        flash.hidden = true;
        flash.textContent = '';
    }

    /**
     * 切換路由（replace 用於登入／登出，讓上一頁無法回到受保護的畫面）
     */
    function go(hash, replace) {
        if (replace) {
            location.replace('#' + hash);
        } else {
            location.hash = hash;
        }
    }

    // -----------------------------------------------------------------------
    // 頁首
    // -----------------------------------------------------------------------

    function renderNav() {
        var user = Api.getUser();
        if (user) {
            nav.innerHTML =
                '<span class="username" id="username">' + escapeHtml(user.username) + '</span>' +
                '<button type="button" class="btn btn-ghost" id="logout">Logout</button>';
            document.getElementById('logout').addEventListener('click', function () {
                Api.logout().then(function () {
                    Api.setUser(null);
                    renderNav();
                    go('/login', true);
                });
            });
        } else {
            nav.innerHTML = '<a class="btn btn-ghost" id="login-link" href="#/login">Login</a>';
        }
    }

    // -----------------------------------------------------------------------
    // C1 - 活動列表
    // -----------------------------------------------------------------------

    function viewEvents() {
        app.innerHTML = '<p class="loading">Loading events…</p>';
        Api.listEvents().then(function (result) {
            if (!result.ok) {
                return renderError('Could not load the events.');
            }
            var events = result.data.events || [];
            var html = '<h1 class="page-title">Upcoming events</h1><div class="event-list">';
            events.forEach(function (item) {
                html += '<a class="event" href="#/events/' +
                    encodeURIComponent(item.organizer.slug) + '/' + encodeURIComponent(item.slug) + '">' +
                    '<h2 class="event-name">' + escapeHtml(item.name) + '</h2>' +
                    '<p class="event-meta">' + escapeHtml(item.organizer.name) + ', ' +
                    escapeHtml(formatDate(item.date)) + '</p>' +
                    '</a>';
            });
            if (!events.length) {
                html += '<p class="empty">There are no upcoming events.</p>';
            }
            app.innerHTML = html + '</div>';
        });
    }

    // -----------------------------------------------------------------------
    // C2 - 活動議程（頻道／房間泳道）
    // -----------------------------------------------------------------------

    function viewAgenda(organizerSlug, eventSlug) {
        app.innerHTML = '<p class="loading">Loading agenda…</p>';

        Promise.all([
            Api.getEvent(organizerSlug, eventSlug),
            loadMyRegistration(organizerSlug, eventSlug)
        ]).then(function (results) {
            var result = results[0];
            var registration = results[1];

            if (!result.ok) {
                return renderError(result.data.message || 'Event not found');
            }
            var event = result.data;
            var registeredSessionIds = registration ? registration.session_ids : null;

            var html = '<div class="page-head">' +
                '<div><h1 class="page-title">' + escapeHtml(event.name) + '</h1>' +
                '<p class="page-subtitle">' + escapeHtml(formatDate(event.date)) + '</p></div>';

            if (registration) {
                html += '<span class="badge-registered">You are registered</span>';
            } else {
                html += '<button type="button" class="btn btn-primary" id="register">Register for this event</button>';
            }
            html += '</div>';

            html += renderTimelineHead();

            (event.channels || []).forEach(function (channel) {
                html += '<div class="channel-group">' +
                    '<div class="channel">' + escapeHtml(channel.name) + '</div>' +
                    '<div class="channel-rows">';
                (channel.rooms || []).forEach(function (room) {
                    html += '<div class="row">' +
                        '<div class="room">' + escapeHtml(room.name) + '</div>' +
                        '<div class="track">' + renderTicksBackground();
                    (room.sessions || []).forEach(function (session) {
                        html += renderSessionBlock(session, organizerSlug, eventSlug, registeredSessionIds);
                    });
                    html += '</div></div>';
                });
                html += '</div></div>';
            });

            app.innerHTML = html;

            var registerButton = document.getElementById('register');
            if (registerButton) {
                registerButton.addEventListener('click', function () {
                    var target = '/events/' + encodeURIComponent(organizerSlug) + '/' +
                        encodeURIComponent(eventSlug) + '/register';
                    if (!Api.getUser()) {
                        // 未登入時先導到登入頁，登入後再回到報名頁
                        go('/login?next=' + encodeURIComponent(target));
                    } else {
                        go(target);
                    }
                });
            }
        });
    }

    /**
     * 時間軸標題列（9:00 / 11:00 / 13:00 / 15:00）
     */
    function renderTimelineHead() {
        var html = '<div class="agenda-head"><div class="agenda-corner"></div><div class="timeline">';
        TIMELINE_TICKS.forEach(function (hour) {
            html += '<span class="tick" style="left:' + positionPercent(hour * 60) + '%">' + hour + ':00</span>';
        });
        html += '</div></div>';
        return html;
    }

    /**
     * 泳道底色的時間格線
     */
    function renderTicksBackground() {
        var html = '';
        TIMELINE_TICKS.forEach(function (hour) {
            html += '<span class="grid-line" style="left:' + positionPercent(hour * 60) + '%"></span>';
        });
        return html;
    }

    /**
     * 分鐘數換算成時間軸上的百分比位置
     */
    function positionPercent(minutes) {
        var ratio = (minutes - TIMELINE_START) / (TIMELINE_END - TIMELINE_START);
        return Math.max(0, Math.min(1, ratio)) * 100;
    }

    /**
     * 單一議程方塊（已報名者的議程會加上 registered 類別，顯示綠色外框）
     */
    function renderSessionBlock(session, organizerSlug, eventSlug, registeredSessionIds) {
        var start = positionPercent(minutesOfDay(session.start));
        var end = positionPercent(minutesOfDay(session.end));
        var width = Math.max(end - start, 3);

        var isRegistered = false;
        if (registeredSessionIds) {
            // 已報名活動 => 所有 talk 自動包含；workshop 需另外勾選
            isRegistered = session.type === 'talk' || registeredSessionIds.indexOf(session.id) !== -1;
        }

        return '<a class="session session-' + escapeHtml(session.type) + (isRegistered ? ' registered' : '') + '"' +
            ' style="left:' + start + '%;width:' + width + '%"' +
            ' href="#/events/' + encodeURIComponent(organizerSlug) + '/' + encodeURIComponent(eventSlug) +
            '/sessions/' + encodeURIComponent(session.id) + '"' +
            ' title="' + escapeHtml(session.title) + '">' +
            '<span class="session-title">' + escapeHtml(session.title) + '</span>' +
            '</a>';
    }

    /**
     * 取得目前使用者對這個活動的報名紀錄；未登入或未報名回傳 null
     */
    function loadMyRegistration(organizerSlug, eventSlug) {
        if (!Api.getUser()) {
            return Promise.resolve(null);
        }
        return Api.listRegistrations().then(function (result) {
            if (!result.ok) {
                return null;
            }
            var found = null;
            (result.data.registrations || []).forEach(function (registration) {
                if (registration.event.slug === eventSlug &&
                    registration.event.organizer.slug === organizerSlug) {
                    found = registration;
                }
            });
            return found;
        });
    }

    // -----------------------------------------------------------------------
    // C3 - 議程詳細
    // -----------------------------------------------------------------------

    function viewSession(organizerSlug, eventSlug, sessionId) {
        app.innerHTML = '<p class="loading">Loading session…</p>';
        Api.getEvent(organizerSlug, eventSlug).then(function (result) {
            if (!result.ok) {
                return renderError(result.data.message || 'Event not found');
            }
            var session = findSession(result.data, Number(sessionId));
            if (!session) {
                return renderError('Session not found');
            }

            var typeLabel = session.type.charAt(0).toUpperCase() + session.type.slice(1);
            var html = '<a class="back-link" href="#/events/' + encodeURIComponent(organizerSlug) + '/' +
                encodeURIComponent(eventSlug) + '">&larr; Back to agenda</a>' +
                '<article class="session-detail">' +
                '<h1 class="page-title">' + escapeHtml(session.title) + ' - ' + escapeHtml(typeLabel) + '</h1>' +
                '<p class="session-description">' + escapeHtml(session.description) + '</p>' +
                '<dl class="session-facts">' +
                '<dt>Speaker:</dt><dd>' + escapeHtml(session.speaker) + '</dd>' +
                '<dt>Start:</dt><dd>' + escapeHtml(formatTime(session.start)) + '</dd>' +
                '<dt>End:</dt><dd>' + escapeHtml(formatTime(session.end)) + '</dd>';
            if (session.cost !== null && session.cost !== undefined) {
                html += '<dt>Cost:</dt><dd>' + escapeHtml(formatCost(session.cost)) + '</dd>';
            }
            html += '</dl></article>';
            app.innerHTML = html;
        });
    }

    /**
     * 在活動資料中找出指定的議程
     */
    function findSession(event, sessionId) {
        var found = null;
        (event.channels || []).forEach(function (channel) {
            (channel.rooms || []).forEach(function (room) {
                (room.sessions || []).forEach(function (session) {
                    if (session.id === sessionId) {
                        found = session;
                    }
                });
            });
        });
        return found;
    }

    // -----------------------------------------------------------------------
    // C4 - 報名購票
    // -----------------------------------------------------------------------

    function viewRegistration(organizerSlug, eventSlug) {
        var target = '/events/' + encodeURIComponent(organizerSlug) + '/' +
            encodeURIComponent(eventSlug) + '/register';
        if (!Api.getUser()) {
            return go('/login?next=' + encodeURIComponent(target), true);
        }

        app.innerHTML = '<p class="loading">Loading tickets…</p>';
        Api.getEvent(organizerSlug, eventSlug).then(function (result) {
            if (!result.ok) {
                return renderError(result.data.message || 'Event not found');
            }
            var event = result.data;
            var workshops = collectWorkshops(event);

            var html = '<h1 class="page-title">' + escapeHtml(event.name) + '</h1>' +
                '<div class="ticket-list">';
            (event.tickets || []).forEach(function (ticket) {
                html += '<label class="ticket' + (ticket.available ? '' : ' unavailable') + '">' +
                    '<input type="radio" name="ticket" value="' + ticket.id + '"' +
                    (ticket.available ? '' : ' disabled') + '>' +
                    '<span class="ticket-body">' +
                    '<span class="ticket-head">' +
                    '<span class="ticket-name">' + escapeHtml(ticket.name) + '</span>' +
                    '<span class="ticket-cost">' + escapeHtml(formatCost(ticket.cost)) + '</span>' +
                    '</span>' +
                    '<span class="ticket-note">' +
                    escapeHtml(ticket.available ? (ticket.description || '') : 'unavailable') +
                    '</span></span></label>';
            });
            html += '</div>';

            html += '<h2 class="section-title">Select additional workshops you want to book:</h2>' +
                '<div class="workshop-list">';
            workshops.forEach(function (session) {
                html += '<label class="workshop">' +
                    '<input type="checkbox" name="session" value="' + session.id + '"' +
                    ' data-cost="' + Number(session.cost || 0) + '">' +
                    '<span class="workshop-title">' + escapeHtml(session.title) + '</span>' +
                    '<span class="workshop-cost">' +
                    (session.cost ? escapeHtml(formatCost(session.cost)) : '') + '</span>' +
                    '</label>';
            });
            if (!workshops.length) {
                html += '<p class="empty">This event has no workshops.</p>';
            }
            html += '</div>';

            html += '<div class="cost-view">' +
                '<div class="cost-row"><span>Event ticket:</span><span id="event-cost">0.-</span></div>' +
                '<div class="cost-row"><span>Additional workshops:</span><span id="additional-cost">0.-</span></div>' +
                '<div class="cost-row cost-total"><span>Total:</span><span id="total-cost">0.-</span></div>' +
                '</div>' +
                '<div class="actions">' +
                '<button type="button" class="btn btn-primary" id="purchase" disabled>Purchase</button>' +
                '</div>';

            app.innerHTML = html;
            bindRegistrationForm(organizerSlug, eventSlug, event);
        });
    }

    /**
     * 取出活動中所有 workshop 類型的議程
     */
    function collectWorkshops(event) {
        var workshops = [];
        (event.channels || []).forEach(function (channel) {
            (channel.rooms || []).forEach(function (room) {
                (room.sessions || []).forEach(function (session) {
                    if (session.type === 'workshop') {
                        workshops.push(session);
                    }
                });
            });
        });
        return workshops;
    }

    /**
     * 綁定票種／工作坊的選取行為，並即時更新費用摘要
     */
    function bindRegistrationForm(organizerSlug, eventSlug, event) {
        var ticketInputs = Array.prototype.slice.call(app.querySelectorAll('input[name="ticket"]'));
        var sessionInputs = Array.prototype.slice.call(app.querySelectorAll('input[name="session"]'));
        var purchase = document.getElementById('purchase');

        function selectedTicket() {
            var checked = ticketInputs.filter(function (input) {
                return input.checked;
            })[0];
            if (!checked) {
                return null;
            }
            return (event.tickets || []).filter(function (ticket) {
                return String(ticket.id) === checked.value;
            })[0] || null;
        }

        function updateCosts() {
            var ticket = selectedTicket();
            var ticketCost = ticket ? Number(ticket.cost) : 0;
            var extraCost = 0;

            sessionInputs.forEach(function (input) {
                input.parentNode.classList.toggle('checked', input.checked);
                if (input.checked) {
                    extraCost += Number(input.getAttribute('data-cost')) || 0;
                }
            });
            ticketInputs.forEach(function (input) {
                input.parentNode.classList.toggle('selected', input.checked);
            });

            document.getElementById('event-cost').textContent = formatCost(ticketCost);
            document.getElementById('additional-cost').textContent = formatCost(extraCost);
            document.getElementById('total-cost').textContent = formatCost(ticketCost + extraCost);
            // 沒有選擇票種時不可購買
            purchase.disabled = !ticket;
        }

        ticketInputs.concat(sessionInputs).forEach(function (input) {
            input.addEventListener('change', updateCosts);
        });
        updateCosts();

        purchase.addEventListener('click', function () {
            var ticket = selectedTicket();
            if (!ticket) {
                return;
            }
            var sessionIds = sessionInputs.filter(function (input) {
                return input.checked;
            }).map(function (input) {
                return Number(input.value);
            });

            purchase.disabled = true;
            Api.register(organizerSlug, eventSlug, ticket.id, sessionIds).then(function (result) {
                if (result.ok) {
                    showFlash('Registration successful', 'success');
                    keepFlashOnce = true;
                    go('/events/' + encodeURIComponent(organizerSlug) + '/' + encodeURIComponent(eventSlug), true);
                } else {
                    purchase.disabled = false;
                    showFlash(result.data.message || 'Registration failed', 'error');
                    if (result.status === 401 && result.data.message === 'User not logged in') {
                        Api.setUser(null);
                        renderNav();
                    }
                }
            });
        });
    }

    // -----------------------------------------------------------------------
    // C5 - 登入
    // -----------------------------------------------------------------------

    function viewLogin(nextPath) {
        app.innerHTML =
            '<div class="login-box">' +
            '<h1 class="page-title">Login</h1>' +
            '<form id="login-form" novalidate>' +
            '<div class="field"><label for="lastname">Lastname</label>' +
            '<input type="text" id="lastname" name="lastname" placeholder="Lastname" autocomplete="off"></div>' +
            '<div class="field"><label for="registration_code">Registration Code</label>' +
            '<input type="text" id="registration_code" name="registration_code" placeholder="Code" autocomplete="off"></div>' +
            '<p class="form-error" id="login-error" hidden></p>' +
            '<button type="submit" class="btn btn-primary" id="login">Login</button>' +
            '</form></div>';

        document.getElementById('login-form').addEventListener('submit', function (submitEvent) {
            submitEvent.preventDefault();
            var lastname = document.getElementById('lastname').value.trim();
            var code = document.getElementById('registration_code').value.trim();
            var errorBox = document.getElementById('login-error');

            Api.login(lastname, code).then(function (result) {
                if (result.ok) {
                    Api.setUser(result.data);
                    renderNav();
                    // 登入後回到剛才想去的頁面
                    go(nextPath || '/', true);
                } else {
                    errorBox.textContent = 'Lastname or registration code not correct';
                    errorBox.hidden = false;
                }
            });
        });
    }

    // -----------------------------------------------------------------------
    // 錯誤頁
    // -----------------------------------------------------------------------

    function renderError(message) {
        app.innerHTML = '<div class="error-page">' +
            '<h1 class="page-title">Oops…</h1>' +
            '<p>' + escapeHtml(message) + '</p>' +
            '<a class="btn btn-primary" href="#/">Back to all events</a>' +
            '</div>';
    }

    // -----------------------------------------------------------------------
    // 路由
    // -----------------------------------------------------------------------

    function route() {
        renderNav();

        var raw = location.hash.replace(/^#/, '') || '/';
        var queryIndex = raw.indexOf('?');
        var query = {};
        if (queryIndex !== -1) {
            raw.slice(queryIndex + 1).split('&').forEach(function (pair) {
                var parts = pair.split('=');
                query[decodeURIComponent(parts[0])] = decodeURIComponent(parts[1] || '');
            });
            raw = raw.slice(0, queryIndex);
        }

        var segments = raw.split('/').filter(function (segment) {
            return segment !== '';
        }).map(decodeURIComponent);

        window.scrollTo(0, 0);

        if (segments.length === 0) {
            clearFlash();
            return viewEvents();
        }
        if (segments.length === 1 && segments[0] === 'login') {
            clearFlash();
            return viewLogin(query.next);
        }
        if (segments[0] === 'events' && segments.length === 3) {
            if (keepFlashOnce) {
                keepFlashOnce = false;
            } else {
                clearFlash();
            }
            return viewAgenda(segments[1], segments[2]);
        }
        if (segments[0] === 'events' && segments.length === 5 && segments[3] === 'sessions') {
            clearFlash();
            return viewSession(segments[1], segments[2], segments[4]);
        }
        if (segments[0] === 'events' && segments.length === 4 && segments[3] === 'register') {
            clearFlash();
            return viewRegistration(segments[1], segments[2]);
        }

        clearFlash();
        renderError('The page you requested does not exist.');
    }

    window.addEventListener('hashchange', route);
    window.addEventListener('load', function () {
        if (!location.hash) {
            location.replace('#/');
        }
        route();
    });
}());

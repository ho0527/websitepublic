/**
 * list.js — 聯絡人清單（RecyclerView 式虛擬捲動）
 * 畫面上實際存在的 .contact 節點數量固定不超過 MAX_NODES 個，
 * 捲動時重複使用（回收）既有節點並改寫其內容，而非隱藏或不斷新增節點。
 */
window.App = window.App || {};

App.List = (function () {
    'use strict';

    var ROW_HEIGHT = 76;   // 單一清單項目基本高度（含留白，需 >= 64px）
    var MAX_NODES = 12;    // 清單項目節點數量上限（試題規定不可超過 12 個）
    var BUFFER = 2;        // 上下各預留的緩衝節點

    var rowHeight = ROW_HEIGHT;   // 實際採用的列高（畫面過高時等比放大以維持版面平衡）

    var container = null;  // .contacts
    var scroller = null;   // .contacts_scroller（撐出總高度）
    var pool = null;       // .contacts_pool（放置回收節點）
    var messageNode = null;

    var data = [];         // 目前要呈現的聯絡人陣列
    var nodes = [];        // 已建立、可重複使用的 .contact 節點
    var lastStart = -1;
    var callbacks = {};

    /** 初始化，綁定容器與事件 */
    function init(options) {
        container = document.querySelector('.contacts');
        scroller = container.querySelector('.contacts_scroller');
        pool = container.querySelector('.contacts_pool');
        callbacks = options || {};

        container.addEventListener('scroll', function () { render(); });
        window.addEventListener('resize', function () { render(true); });

        // 事件委派：編輯 / 刪除按鈕
        pool.addEventListener('click', function (event) {
            var button = event.target.closest('button');
            if (!button) {
                return;
            }
            var item = button.closest('.contact');
            if (!item) {
                return;
            }
            var id = Number(item.dataset.id);
            if (button.classList.contains('edit') && callbacks.onEdit) {
                callbacks.onEdit(id);
            } else if (button.classList.contains('delete') && callbacks.onDelete) {
                callbacks.onDelete(id);
            }
        });
    }

    /**
     * 設定要呈現的資料。
     * @param {Array} list 聯絡人陣列
     * @param {string} emptyMessage 無資料時要呈現的訊息文字
     */
    function setData(list, emptyMessage) {
        data = list || [];
        updateMetrics();
        scroller.style.height = (data.length * rowHeight) + 'px';
        if (container.scrollTop > Math.max(0, data.length * rowHeight - container.clientHeight)) {
            container.scrollTop = 0;
        }
        showMessage(data.length === 0 ? emptyMessage : '');
        lastStart = -1;
        render(true);
    }

    /** 依需要建立或移除 .message 元素（沒有訊息時不留下該元素） */
    function showMessage(text) {
        if (!text) {
            if (messageNode && messageNode.parentNode) {
                messageNode.parentNode.removeChild(messageNode);
            }
            messageNode = null;
            return;
        }
        if (!messageNode) {
            messageNode = document.createElement('p');
            messageNode.className = 'message';
            container.appendChild(messageNode);
        }
        messageNode.textContent = text;
    }

    /** 建立一個可重複使用的清單項目節點 */
    function createNode() {
        var item = document.createElement('div');
        item.className = 'contact';
        item.innerHTML =
            '<img class="avatar" alt="" src="' + App.Avatar.DEFAULT_SRC + '">' +
            '<span class="col col_name fullname">' +
                '<span class="last_name"></span><span class="first_name"></span>' +
            '</span>' +
            '<span class="col col_email email"></span>' +
            '<span class="col col_phone phone"></span>' +
            '<span class="col col_tags tags"></span>' +
            '<span class="actions">' +
                '<button type="button" class="edit icon_btn" title="編輯" aria-label="編輯"></button>' +
                '<button type="button" class="delete icon_btn" title="刪除" aria-label="刪除"></button>' +
            '</span>';
        pool.appendChild(item);
        return item;
    }

    /**
     * 計算實際列高。
     * 節點數量上限為 MAX_NODES，因此畫面很高時改以加大列高填滿版面，
     * 避免清單下方出現空白而破壞元件之間的平衡。
     */
    function updateMetrics() {
        var height = container.clientHeight;
        var fit = Math.ceil(height / (MAX_NODES - BUFFER));
        rowHeight = Math.max(ROW_HEIGHT, fit);
        container.style.setProperty('--row-height', rowHeight + 'px');
    }

    /**
     * 計算目前需要的節點數量。
     * 永遠不超過 MAX_NODES，也不超過實際資料筆數；
     * 沒有資料時回傳 0，池中不會殘留任何空白的 .contact 節點。
     */
    function neededCount() {
        var visible = Math.ceil(container.clientHeight / rowHeight) + BUFFER;
        return Math.max(0, Math.min(MAX_NODES, visible, data.length));
    }

    /** 依捲動位置重新指派節點內容 */
    function render(force) {
        updateMetrics();
        scroller.style.height = (data.length * rowHeight) + 'px';
        var count = neededCount();

        while (nodes.length < count) {
            nodes.push(createNode());
        }
        while (nodes.length > count) {
            var extra = nodes.pop();
            if (extra.parentNode) {
                extra.parentNode.removeChild(extra);
            }
        }

        var maxStart = Math.max(0, data.length - count);
        var start = Math.min(maxStart, Math.floor(container.scrollTop / rowHeight));
        start = Math.max(0, start);

        if (!force && start === lastStart) {
            return;
        }
        lastStart = start;

        nodes.forEach(function (node, index) {
            var contact = data[start + index];
            if (!contact) {
                node.hidden = true;
                return;
            }
            node.hidden = false;
            node.style.transform = 'translateY(' + ((start + index) * rowHeight) + 'px)';
            fillNode(node, contact);
        });
    }

    /** 將聯絡人資料寫入既有節點（回收重用） */
    function fillNode(node, contact) {
        node.dataset.id = contact.id;

        var avatar = node.querySelector('.avatar');
        var src = contact.avatar || App.Avatar.DEFAULT_SRC;
        if (avatar.getAttribute('src') !== src) {
            avatar.setAttribute('src', src);
        }

        node.querySelector('.last_name').textContent = contact.last_name || '';
        node.querySelector('.first_name').textContent = contact.first_name || '';
        // 有多組電子郵件或電話時，只呈現第一組
        node.querySelector('.email').textContent = (contact.emails || [])[0] || '';
        node.querySelector('.phone').textContent = (contact.phones || [])[0] || '';

        var tagsBox = node.querySelector('.tags');
        tagsBox.textContent = '';
        (contact.tags || []).forEach(function (tagId) {
            var tag = App.Store.getTag(tagId);
            if (!tag) {
                return;
            }
            var span = document.createElement('span');
            span.className = 'tag';
            span.textContent = tag.name;
            tagsBox.appendChild(span);
        });
    }

    /** 目前畫面上的 .contact 節點數量（供驗證用） */
    function nodeCount() {
        return nodes.length;
    }

    return {
        ROW_HEIGHT: ROW_HEIGHT,
        MAX_NODES: MAX_NODES,
        init: init,
        setData: setData,
        render: render,
        nodeCount: nodeCount
    };
})();

/**
 * app.js — 應用程式進入點
 * 負責串接資料層 (App.Store)、清單畫面 (App.List) 與對話框 (App.Dialog)，
 * 以及側邊欄、搜尋等頁面互動。
 */
(function () {
    'use strict';

    var MESSAGE_EMPTY = '目前還沒有任何聯絡人';
    var MESSAGE_NO_RESULT = '在你的聯絡人中找不到相符的搜尋結果';

    // 目前的檢視狀態（單一資料來源，避免散落的全域變數）
    var view = {
        tagId: null,   // null 代表「所有聯絡人」
        keyword: ''
    };

    var elements = {};

    function cacheElements() {
        elements.aside = document.getElementById('aside');
        elements.layout = document.getElementById('layout');
        elements.menuToggle = document.getElementById('menuToggle');
        elements.addContact = document.getElementById('addContact');
        elements.contactsMenu = document.getElementById('contactsMenu');
        elements.tagsList = document.getElementById('tagsList');
        elements.addTag = document.getElementById('addTag');
        elements.searchForm = document.getElementById('searchForm');
        elements.searchInput = elements.searchForm.querySelector('input[name="search"]');
    }

    // ---------- 側邊欄 ----------

    /** 重繪側邊欄的標籤清單與各項數量 */
    function renderAside() {
        // 所有聯絡人數量
        elements.contactsMenu.querySelector('.num').textContent = App.Store.countContacts();

        // 標籤項目：全部重建，並保留 li#addTag 於最後
        Array.prototype.slice.call(elements.tagsList.querySelectorAll('.item')).forEach(function (item) {
            if (item !== elements.addTag) {
                elements.tagsList.removeChild(item);
            }
        });

        App.Store.getTags().forEach(function (tag) {
            var item = document.createElement('li');
            item.className = 'item';
            item.dataset.tagId = tag.id;

            var icon = document.createElement('span');
            icon.className = 'item_icon icon_tag';

            var text = document.createElement('span');
            text.className = 'item_text';
            text.textContent = tag.name;

            var num = document.createElement('span');
            num.className = 'num';
            num.textContent = App.Store.countByTag(tag.id);

            item.appendChild(icon);
            item.appendChild(text);
            item.appendChild(num);
            elements.tagsList.insertBefore(item, elements.addTag);
        });

        renderCurrent();
    }

    /** 依目前檢視標示高亮項目 (.current) */
    function renderCurrent() {
        var items = document.querySelectorAll('#aside .list .item');
        Array.prototype.slice.call(items).forEach(function (item) {
            item.classList.remove('current');
        });

        if (view.tagId === null) {
            elements.contactsMenu.querySelector('.item').classList.add('current');
        } else {
            var target = elements.tagsList.querySelector('.item[data-tag-id="' + view.tagId + '"]');
            if (target) {
                target.classList.add('current');
            }
        }
    }

    // ---------- 主內容 ----------

    /** 依目前檢視條件重繪聯絡人清單 */
    function renderList() {
        var result = App.Store.query({ tagId: view.tagId, keyword: view.keyword });
        var message = view.keyword ? MESSAGE_NO_RESULT : MESSAGE_EMPTY;
        App.List.setData(result, message);
    }

    /** 資料或檢視變動後統一重繪 */
    function refresh() {
        renderAside();
        renderList();
    }

    // ---------- 事件 ----------

    function bindEvents() {
        // 側邊欄收合
        elements.menuToggle.addEventListener('click', function () {
            elements.layout.classList.toggle('aside_hidden');
            App.List.render(true);
        });

        // 建立聯絡人
        elements.addContact.addEventListener('click', function () {
            App.Dialog.openContact(null, function (payload) {
                return App.Store.addContact(payload);
            });
        });

        // 側邊欄清單點擊（含「聯絡人」、各標籤、建立標籤）
        elements.aside.addEventListener('click', function (event) {
            var item = event.target.closest('.item');
            if (!item) {
                return;
            }

            if (item === elements.addTag) {
                App.Dialog.openTag(function (name) {
                    return App.Store.addTag(name);
                });
                return;
            }

            view.keyword = '';
            elements.searchInput.value = '';
            view.tagId = item.dataset.tagId ? Number(item.dataset.tagId) : null;
            renderCurrent();
            renderList();
        });

        // 搜尋：輸入文字後按下 Enter
        elements.searchForm.addEventListener('submit', function (event) {
            event.preventDefault();
            view.keyword = elements.searchInput.value.trim();
            view.tagId = null;          // 搜尋範圍為全部聯絡人
            renderCurrent();
            renderList();
        });

        // 清空搜尋框（按下 × 或全部刪除）時回復完整清單
        elements.searchInput.addEventListener('search', function () {
            if (elements.searchInput.value === '' && view.keyword !== '') {
                view.keyword = '';
                renderList();
            }
        });
    }

    // ---------- 啟動 ----------

    function start() {
        cacheElements();

        App.List.init({
            onEdit: function (id) {
                var contact = App.Store.getContact(id);
                if (!contact) {
                    return;
                }
                App.Dialog.openContact(contact, function (payload) {
                    return App.Store.updateContact(id, payload);
                });
            },
            onDelete: function (id) {
                App.Dialog.openDelete(function () {
                    return App.Store.removeContact(id);
                });
            }
        });

        bindEvents();
        App.Store.subscribe(refresh);

        // 先以目前（空的）資料同步繪製一次，讓「目前還沒有任何聯絡人」等空狀態
        // 在頁面載入當下就正確呈現，不必等待 IndexedDB 非同步開啟完成。
        refresh();

        // IndexedDB 讀取完成後再以實際資料重繪
        App.Store.load().then(refresh).catch(function (error) {
            console.warn('資料載入失敗', error);
            refresh();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();

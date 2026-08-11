/**
 * store.js — 資料模型與查詢邏輯
 * 將 IndexedDB 的資料載入記憶體以供快速查詢／過濾，
 * 所有異動皆同步寫回 IndexedDB。此層完全不碰 DOM。
 */
window.App = window.App || {};

App.Store = (function () {
    'use strict';

    var contacts = [];   // { id, first_name, last_name, emails[], phones[], tags[], note, avatar, seq }
    var tags = [];       // { id, name, seq }
    var listeners = [];  // 資料變動後要通知的回呼

    /** 訂閱資料變動 */
    function subscribe(callback) {
        listeners.push(callback);
    }

    /** 廣播資料已變動 */
    function emit() {
        listeners.forEach(function (callback) { callback(); });
    }

    /** 由 IndexedDB 載入全部資料 */
    function load() {
        return Promise.all([
            App.DB.getAll(App.DB.STORE_CONTACTS),
            App.DB.getAll(App.DB.STORE_TAGS)
        ]).then(function (result) {
            contacts = (result[0] || []).slice();
            tags = (result[1] || []).slice();
            sortBySeq(contacts);
            sortBySeq(tags);
        });
    }

    /** 依新增順序排序（seq 小的在前，也就是最新新增的排於下方） */
    function sortBySeq(list) {
        list.sort(function (a, b) {
            return (a.seq || a.id || 0) - (b.seq || b.id || 0);
        });
    }

    /** 產生一個遞增的排序序號 */
    function nextSeq(list) {
        return list.reduce(function (max, item) {
            return Math.max(max, item.seq || 0);
        }, 0) + 1;
    }

    // ---------- 標籤 ----------

    function getTags() {
        return tags.slice();
    }

    function getTag(id) {
        return tags.filter(function (tag) { return tag.id === id; })[0] || null;
    }

    /** 新增標籤，name 需為非空字串 */
    function addTag(name) {
        var record = { name: String(name).trim(), seq: nextSeq(tags) };
        return App.DB.put(App.DB.STORE_TAGS, record).then(function (id) {
            record.id = id;
            tags.push(record);
            emit();
            return record;
        });
    }

    /** 計算指定標籤底下的聯絡人數量 */
    function countByTag(tagId) {
        return contacts.filter(function (contact) {
            return (contact.tags || []).indexOf(tagId) !== -1;
        }).length;
    }

    // ---------- 聯絡人 ----------

    function getContacts() {
        return contacts.slice();
    }

    function getContact(id) {
        return contacts.filter(function (contact) { return contact.id === id; })[0] || null;
    }

    function countContacts() {
        return contacts.length;
    }

    /** 新增聯絡人；data 為已整理好的欄位物件 */
    function addContact(data) {
        var record = {
            first_name: data.first_name || '',
            last_name: data.last_name || '',
            emails: data.emails || [],
            phones: data.phones || [],
            tags: data.tags || [],
            note: data.note || '',
            avatar: data.avatar || null,   // null 代表使用預設大頭貼圖片路徑
            seq: nextSeq(contacts)
        };
        return App.DB.put(App.DB.STORE_CONTACTS, record).then(function (id) {
            record.id = id;
            contacts.push(record);
            emit();
            return record;
        });
    }

    /** 更新聯絡人；未提供的 avatar 代表沿用原本圖片 */
    function updateContact(id, data) {
        var record = getContact(id);
        if (!record) {
            return Promise.resolve(null);
        }
        record.first_name = data.first_name || '';
        record.last_name = data.last_name || '';
        record.emails = data.emails || [];
        record.phones = data.phones || [];
        record.tags = data.tags || [];
        record.note = data.note || '';
        if (typeof data.avatar !== 'undefined' && data.avatar !== null) {
            record.avatar = data.avatar;
        }
        return App.DB.put(App.DB.STORE_CONTACTS, record).then(function () {
            emit();
            return record;
        });
    }

    /** 刪除聯絡人 */
    function removeContact(id) {
        return App.DB.remove(App.DB.STORE_CONTACTS, id).then(function () {
            contacts = contacts.filter(function (contact) { return contact.id !== id; });
            emit();
        });
    }

    // ---------- 查詢 ----------

    /** 取得聯絡人全名（姓氏 + 名字） */
    function fullName(contact) {
        return (contact.last_name || '') + (contact.first_name || '');
    }

    /**
     * 依條件過濾聯絡人。
     * @param {Object} options { tagId: number|null, keyword: string }
     */
    function query(options) {
        var tagId = options && options.tagId;
        var keyword = ((options && options.keyword) || '').trim().toLowerCase();
        var result = contacts;

        if (tagId) {
            result = result.filter(function (contact) {
                return (contact.tags || []).indexOf(tagId) !== -1;
            });
        }
        if (keyword) {
            result = result.filter(function (contact) {
                return matchKeyword(contact, keyword);
            });
        }
        return result;
    }

    /** 模糊比對：姓氏、名字、姓氏+名字、名字+姓氏、電話、電子郵件 */
    function matchKeyword(contact, keyword) {
        var last = (contact.last_name || '').toLowerCase();
        var first = (contact.first_name || '').toLowerCase();
        var fields = [last, first, last + first, first + last];

        (contact.emails || []).forEach(function (email) { fields.push(String(email).toLowerCase()); });
        (contact.phones || []).forEach(function (phone) { fields.push(String(phone).toLowerCase()); });

        return fields.some(function (field) {
            return field && field.indexOf(keyword) !== -1;
        });
    }

    return {
        subscribe: subscribe,
        load: load,
        getTags: getTags,
        getTag: getTag,
        addTag: addTag,
        countByTag: countByTag,
        getContacts: getContacts,
        getContact: getContact,
        countContacts: countContacts,
        addContact: addContact,
        updateContact: updateContact,
        removeContact: removeContact,
        fullName: fullName,
        query: query
    };
})();

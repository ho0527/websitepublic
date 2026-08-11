/**
 * db.js — IndexedDB 資料存取層
 * 只負責與瀏覽器資料庫溝通，不處理任何畫面邏輯。
 * 依試題規定，系統資料一律存放於 IndexedDB（不使用 localStorage / cookie）。
 */
window.App = window.App || {};

App.DB = (function () {
    'use strict';

    var DB_NAME = 'contactsDB';
    var DB_VERSION = 1;
    var STORE_CONTACTS = 'contacts';
    var STORE_TAGS = 'tags';

    var dbPromise = null;

    /** 開啟（或初始化）資料庫，回傳 Promise<IDBDatabase> */
    function open() {
        if (dbPromise) {
            return dbPromise;
        }
        dbPromise = new Promise(function (resolve, reject) {
            var request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onupgradeneeded = function (event) {
                var db = event.target.result;
                if (!db.objectStoreNames.contains(STORE_CONTACTS)) {
                    db.createObjectStore(STORE_CONTACTS, { keyPath: 'id', autoIncrement: true });
                }
                if (!db.objectStoreNames.contains(STORE_TAGS)) {
                    db.createObjectStore(STORE_TAGS, { keyPath: 'id', autoIncrement: true });
                }
            };
            request.onsuccess = function (event) { resolve(event.target.result); };
            request.onerror = function (event) { reject(event.target.error); };
        });
        return dbPromise;
    }

    /** 以指定模式取得 object store */
    function withStore(storeName, mode, callback) {
        return open().then(function (db) {
            return new Promise(function (resolve, reject) {
                var transaction = db.transaction(storeName, mode);
                var store = transaction.objectStore(storeName);
                var result;
                try {
                    result = callback(store);
                } catch (error) {
                    reject(error);
                    return;
                }
                transaction.oncomplete = function () {
                    resolve(result && typeof result.result !== 'undefined' ? result.result : result);
                };
                transaction.onerror = function (event) { reject(event.target.error); };
                transaction.onabort = function (event) { reject(event.target.error); };
            });
        });
    }

    /** 取出某個 store 的全部資料 */
    function getAll(storeName) {
        return withStore(storeName, 'readonly', function (store) {
            return store.getAll();
        });
    }

    /** 新增或更新一筆資料，回傳寫入後的主鍵 */
    function put(storeName, record) {
        return withStore(storeName, 'readwrite', function (store) {
            return store.put(record);
        });
    }

    /** 依主鍵刪除一筆資料 */
    function remove(storeName, id) {
        return withStore(storeName, 'readwrite', function (store) {
            store.delete(id);
            return id;
        });
    }

    return {
        STORE_CONTACTS: STORE_CONTACTS,
        STORE_TAGS: STORE_TAGS,
        open: open,
        getAll: getAll,
        put: put,
        remove: remove
    };
})();

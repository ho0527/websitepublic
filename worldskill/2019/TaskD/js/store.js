/**
 * store.js － 資料模型與自動儲存
 * 以模組化的 IIFE 封裝，對外只暴露 KE.Store。
 * 資料結構：
 *   elements: [{ id, x, y, title, content(HTML), captions:{1,2,3,4} }]
 *   links:    [{ id, from, section(1-4), to }]
 *   rootId:   根元素 id
 */
window.KE = window.KE || {};

KE.Store = (function () {
  'use strict';

  var STORAGE_KEY = 'ke.route.v1';
  var state = null;
  var listeners = [];
  var saveTimer = null;

  /** 產生唯一識別碼 */
  function uid(prefix) {
    return (prefix || 'id') + '-' + Math.random().toString(36).slice(2, 9);
  }

  /** 建立一個空白元素 */
  function makeElement(x, y, title, content) {
    return {
      id: uid('el'),
      x: x,
      y: y,
      title: title || '新元素',
      content: content || '<div>請點擊「編輯」按鈕填入這張投影片的內容。</div>',
      captions: { 1: '', 2: '', 3: '', 4: '' }
    };
  }

  /** 建立只有一個根元素的初始專案（元素置於畫布中央） */
  function makeInitialState(centerX, centerY) {
    var root = makeElement(centerX, centerY, '起點：喀山',
      '<h1>歡迎來到喀山</h1><div>韃靼斯坦共和國千年古都。請選擇你想認識的主題。</div>');
    return { rootId: root.id, elements: [root], links: [] };
  }

  /** 通知所有訂閱者資料已變更 */
  function emit() {
    listeners.forEach(function (fn) { fn(state); });
  }

  /** 延遲寫入 localStorage，避免拖曳時頻繁寫入 */
  function scheduleSave() {
    clearTimeout(saveTimer);
    saveTimer = setTimeout(function () {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        KE.Store.onSaved && KE.Store.onSaved();
      } catch (err) {
        console.warn('儲存失敗：', err);
      }
    }, 200);
  }

  return {
    /** 由 localStorage 還原，若無資料則建立初始專案 */
    load: function (centerX, centerY) {
      var raw = null;
      try { raw = localStorage.getItem(STORAGE_KEY); } catch (err) { raw = null; }
      if (raw) {
        try {
          var parsed = JSON.parse(raw);
          if (parsed && parsed.elements && parsed.elements.length) {
            state = parsed;
            return state;
          }
        } catch (err) { /* 資料毀損則忽略，改用初始專案 */ }
      }
      state = makeInitialState(centerX, centerY);
      scheduleSave();
      return state;
    },

    getState: function () { return state; },

    /** 重置為只有一個根元素的狀態 */
    reset: function (centerX, centerY) {
      state = makeInitialState(centerX, centerY);
      this.commit();
      return state;
    },

    /** 直接以指定資料取代目前狀態（載入範例用） */
    replace: function (next) {
      state = next;
      this.commit();
      return state;
    },

    getElement: function (id) {
      return state.elements.filter(function (el) { return el.id === id; })[0] || null;
    },

    /** 取得某元素某區域的連接 */
    getLinkFrom: function (id, section) {
      return state.links.filter(function (lk) {
        return lk.from === id && lk.section === section;
      })[0] || null;
    },

    /** 取得由某元素出發的所有連接 */
    getOutgoing: function (id) {
      return state.links.filter(function (lk) { return lk.from === id; });
    },

    addElement: function (x, y, title) {
      var el = makeElement(x, y, title);
      state.elements.push(el);
      this.commit();
      return el;
    },

    /** 刪除元素，同時刪除其所有連接（進出皆是） */
    removeElement: function (id) {
      if (id === state.rootId) { return false; }
      state.elements = state.elements.filter(function (el) { return el.id !== id; });
      state.links = state.links.filter(function (lk) {
        return lk.from !== id && lk.to !== id;
      });
      this.commit();
      return true;
    },

    /** 建立連接；同一元素同一區域只能有一條 */
    addLink: function (from, section, to) {
      if (from === to) { return null; }
      if (this.getLinkFrom(from, section)) { return null; }
      var link = { id: uid('lk'), from: from, section: section, to: to };
      state.links.push(link);
      this.commit();
      return link;
    },

    removeLink: function (id) {
      state.links = state.links.filter(function (lk) { return lk.id !== id; });
      this.commit();
    },

    /** 更新元素屬性（座標、內容、標題、區域標題） */
    updateElement: function (id, patch) {
      var el = this.getElement(id);
      if (!el) { return; }
      Object.keys(patch).forEach(function (key) { el[key] = patch[key]; });
      this.commit();
    },

    /** 移動元素但不立即重繪整體（拖曳中使用） */
    moveElement: function (id, x, y) {
      var el = this.getElement(id);
      if (!el) { return; }
      el.x = x;
      el.y = y;
      scheduleSave();
    },

    /** 儲存 + 通知畫面更新 */
    commit: function () {
      scheduleSave();
      emit();
    },

    subscribe: function (fn) { listeners.push(fn); },

    makeElement: makeElement,
    uid: uid
  };
}());

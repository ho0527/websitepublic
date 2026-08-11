/**
 * viewer.js － 查看模式
 * 負責：由根元素開始播放、方向性過場動畫、導覽控制（滑鼠／觸控／數字鍵 1-4）、
 *       目前位置指示器、路線圖跳轉、全螢幕。
 */
window.KE = window.KE || {};

KE.Viewer = (function () {
  'use strict';

  // 各區域對應的移動方向，供過場動畫使用
  var DIRECTION = { 1: 'up', 2: 'right', 3: 'down', 4: 'left' };
  var ARROW = { 1: '↑', 2: '→', 3: '↓', 4: '←' };

  var root, stage, nav, trailBox, whereBox, mapBox, mapList;
  var store;
  var currentId = null;
  var trail = [];              // 已走過的路徑（元素 id）
  var busy = false;

  /** 建立一張投影片的 DOM */
  function buildSlide(el) {
    var slide = document.createElement('article');
    slide.className = 'slide';
    slide.innerHTML =
      '<div class="slide__inner">' +
      '<h1 class="slide__title"></h1>' +
      '<div class="slide__content trix-content"></div>' +
      '</div>';
    slide.querySelector('.slide__title').textContent = el.title;
    // 內容來自所見即所得編輯器，於此直接呈現
    slide.querySelector('.slide__content').innerHTML = el.content;
    return slide;
  }

  /** 依方向計算進場／退場的位移類別 */
  function classesFor(direction) {
    return {
      up: { enter: 'from-top', leave: 'to-bottom' },
      down: { enter: 'from-bottom', leave: 'to-top' },
      left: { enter: 'from-left', leave: 'to-right' },
      right: { enter: 'from-right', leave: 'to-left' }
    }[direction] || { enter: 'from-fade', leave: 'to-fade' };
  }

  /** 切換至指定元素，direction 為 null 時不做方向動畫 */
  function goTo(id, direction) {
    var el = store.getElement(id);
    if (!el || busy) { return; }
    var previous = stage.querySelector('.slide');
    var next = buildSlide(el);
    var move = classesFor(direction);

    if (previous && direction) {
      busy = true;
      next.classList.add('slide--enter', move.enter);
      stage.appendChild(next);
      // 觸發重繪後移除位移類別，產生過場動畫
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          previous.classList.add('slide--leave', move.leave);
          next.classList.remove(move.enter);
        });
      });
      setTimeout(function () {
        previous.remove();
        next.classList.remove('slide--enter');
        busy = false;
      }, 520);
    } else {
      if (previous) { previous.remove(); }
      stage.appendChild(next);
    }

    currentId = id;
    var pos = trail.indexOf(id);
    if (pos >= 0) {
      trail = trail.slice(0, pos + 1);   // 循環路線：回到走過的節點就截斷路徑
    } else {
      trail.push(id);
    }
    renderNav();
    renderTrail();
    // 讓螢幕閱讀器與鍵盤使用者知道已切換投影片
    next.setAttribute('tabindex', '-1');
    next.focus({ preventScroll: true });
  }

  /** 產生導覽控制按鈕（只顯示有連接的區域） */
  function renderNav() {
    nav.innerHTML = '';
    var links = store.getOutgoing(currentId);
    var current = store.getElement(currentId);
    if (!links.length) {
      var end = document.createElement('p');
      end.className = 'viewer__end';
      end.textContent = '這是路線的終點，可用「路線圖」跳到其他元素。';
      nav.appendChild(end);
      return;
    }
    links.sort(function (a, b) { return a.section - b.section; });
    links.forEach(function (link) {
      var target = store.getElement(link.to);
      var caption = current.captions[link.section] || (target ? target.title : '下一步');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'navbtn navbtn--' + DIRECTION[link.section];
      btn.innerHTML =
        '<span class="navbtn__key" aria-hidden="true">' + link.section + '</span>' +
        '<span class="navbtn__caption">' + caption + '</span>' +
        '<span class="navbtn__arrow" aria-hidden="true">' + ARROW[link.section] + '</span>';
      btn.setAttribute('aria-label', '前往：' + caption + '（鍵盤數字鍵 ' + link.section + '）');
      btn.addEventListener('click', function () {
        goTo(link.to, DIRECTION[link.section]);
      });
      nav.appendChild(btn);
    });
  }

  /** 目前位置指示器：麵包屑 + 文字說明 */
  function renderTrail() {
    trailBox.innerHTML = '';
    trail.forEach(function (id, index) {
      var el = store.getElement(id);
      if (!el) { return; }
      var li = document.createElement('li');
      li.className = 'viewer__trail-item' + (id === currentId ? ' is-current' : '');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = el.title;
      if (id === currentId) { btn.setAttribute('aria-current', 'step'); }
      btn.addEventListener('click', function () { goTo(id, null); });
      li.appendChild(btn);
      trailBox.appendChild(li);
    });
    var current = store.getElement(currentId);
    whereBox.textContent = '目前位置：' + (current ? current.title : '') +
      '（第 ' + trail.length + ' 步，共 ' + store.getState().elements.length + ' 個元素）';
  }

  /** 路線圖：列出所有元素，可直接跳轉 */
  function renderMap() {
    mapList.innerHTML = '';
    store.getState().elements.forEach(function (el) {
      var li = document.createElement('li');
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'viewer__map-btn' + (el.id === currentId ? ' is-current' : '');
      btn.textContent = el.title;
      btn.addEventListener('click', function () {
        goTo(el.id, null);
        toggleMap(false);
      });
      li.appendChild(btn);
      mapList.appendChild(li);
    });
  }

  function toggleMap(force) {
    var open = typeof force === 'boolean' ? force : mapBox.hidden;
    mapBox.hidden = !open;
    document.getElementById('btn-map').setAttribute('aria-expanded', String(open));
    if (open) { renderMap(); }
  }

  /** 數字鍵 1-4 導覽、Esc 離開 */
  function onKeyDown(evt) {
    if (root.hidden) { return; }
    if (evt.key === 'Escape') { KE.Viewer.close(); return; }
    var section = Number(evt.key);
    if (section >= 1 && section <= 4) {
      var link = store.getLinkFrom(currentId, section);
      if (link) {
        evt.preventDefault();
        goTo(link.to, DIRECTION[section]);
      }
    }
  }

  return {
    init: function (options) {
      store = KE.Store;
      root = options.root;
      stage = options.stage;
      nav = options.nav;
      trailBox = options.trail;
      whereBox = options.where;
      mapBox = options.map;
      mapList = options.mapList;
      document.addEventListener('keydown', onKeyDown);
      document.getElementById('btn-map').addEventListener('click', function () {
        toggleMap();
      });
    },

    /** 開啟查看模式；skipFullscreen 為 true 時不要求全螢幕（例如由網址直接開啟） */
    open: function (skipFullscreen) {
      root.hidden = false;
      document.body.classList.add('is-viewing');
      trail = [];
      goTo(store.getState().rootId, null);
      if (!skipFullscreen) { this.requestFullscreen(); }
    },

    close: function () {
      root.hidden = true;
      document.body.classList.remove('is-viewing');
      toggleMap(false);
      if (document.fullscreenElement) { document.exitFullscreen(); }
    },

    requestFullscreen: function () {
      if (!document.fullscreenElement && root.requestFullscreen) {
        root.requestFullscreen().catch(function () { /* 使用者拒絕時忽略 */ });
      }
    }
  };
}());

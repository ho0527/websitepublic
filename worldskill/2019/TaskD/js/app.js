/**
 * app.js － 應用程式進入點
 * 負責：初始化 Store / Editor / Viewer、工具列事件、元素編輯對話框（Trix 所見即所得編輯器）、
 *       以及示範用的範例路線。
 */
(function () {
  'use strict';

  var store = KE.Store;
  var canvas = document.getElementById('editor-canvas');
  var svg = document.getElementById('links-layer');
  var modal = document.getElementById('modal');
  var trixInput = document.getElementById('trix-input');
  var trixEditor = document.querySelector('trix-editor');
  var fieldTitle = document.getElementById('field-title');
  var saveStatus = document.getElementById('save-status');
  var editingId = null;
  var lastFocused = null;

  /* ---------- 初始資料 ---------- */

  /** 依畫布尺寸取得中心點，讓根元素出現在畫面中央 */
  function canvasCenter() {
    return {
      x: Math.round(canvas.clientWidth / 2),
      y: Math.round(canvas.clientHeight / 2)
    };
  }

  var center = canvasCenter();
  store.load(center.x, center.y);

  /* ---------- 編輯對話框 ---------- */

  function openModal(id) {
    editingId = id;
    lastFocused = document.activeElement;
    var el = store.getElement(id);
    fieldTitle.value = el.title;
    [1, 2, 3, 4].forEach(function (section) {
      document.getElementById('cap-' + section).value = el.captions[section] || '';
    });
    modal.hidden = false;
    trixInput.value = el.content;
    if (trixEditor.editor) {
      trixEditor.editor.loadHTML(el.content);
    }
    fieldTitle.focus();
  }

  function closeModal() {
    modal.hidden = true;
    editingId = null;
    if (lastFocused) { lastFocused.focus(); }
  }

  function saveModal() {
    if (!editingId) { return; }
    var captions = {};
    [1, 2, 3, 4].forEach(function (section) {
      captions[section] = document.getElementById('cap-' + section).value.trim();
    });
    store.updateElement(editingId, {
      title: fieldTitle.value.trim() || '未命名元素',
      content: trixInput.value,
      captions: captions
    });
    closeModal();
  }

  modal.addEventListener('click', function (evt) {
    if (evt.target.dataset && evt.target.dataset.close) { closeModal(); }
  });
  document.getElementById('btn-save').addEventListener('click', saveModal);
  document.addEventListener('keydown', function (evt) {
    if (evt.key === 'Escape' && !modal.hidden) { closeModal(); }
  });

  /* ---------- 初始化模組 ---------- */

  KE.Editor.init({
    canvas: canvas,
    svg: svg,
    onEditRequest: openModal
  });

  KE.Viewer.init({
    root: document.getElementById('viewer'),
    stage: document.getElementById('viewer-stage'),
    nav: document.getElementById('viewer-nav'),
    trail: document.getElementById('viewer-trail'),
    where: document.getElementById('viewer-where'),
    map: document.getElementById('viewer-map'),
    mapList: document.getElementById('viewer-map-list')
  });

  /* ---------- 工具列 ---------- */

  document.getElementById('btn-add').addEventListener('click', function () {
    var pos = canvasCenter();
    store.addElement(pos.x + Math.round(Math.random() * 160 - 80),
      pos.y + Math.round(Math.random() * 160 - 80), '新元素');
  });

  document.getElementById('btn-clear').addEventListener('click', function () {
    if (window.confirm('確定要清空編輯器？將只保留一個位於中央的根元素。')) {
      var pos = canvasCenter();
      store.reset(pos.x, pos.y);
    }
  });

  document.getElementById('btn-sample').addEventListener('click', function () {
    if (window.confirm('載入範例路線會覆蓋目前內容，要繼續嗎？')) {
      store.replace(buildSample(canvasCenter()));
    }
  });

  document.getElementById('btn-view').addEventListener('click', function () {
    KE.Viewer.open();
  });
  document.getElementById('btn-exit').addEventListener('click', function () {
    KE.Viewer.close();
  });
  document.getElementById('btn-fullscreen').addEventListener('click', function () {
    KE.Viewer.requestFullscreen();
  });

  /* ---------- 自動儲存提示 ---------- */

  store.onSaved = function () {
    saveStatus.textContent = '已自動儲存 · ' + new Date().toLocaleTimeString('zh-TW');
  };

  /* ---------- 範例路線 ---------- */

  /** 建立一條示範用的喀山導覽路線（含循環路線） */
  function buildSample(pos) {
    function make(x, y, title, content, captions) {
      var el = store.makeElement(x, y, title, content);
      el.captions = captions || { 1: '', 2: '', 3: '', 4: '' };
      return el;
    }
    var photo = function (file, alt) {
      return '<figure><img src="assets/photo/' + file + '" alt="' + alt + '"></figure>';
    };

    var rootEl = make(pos.x, pos.y, '喀山：千年古都',
      '<h2>Choose your way to learn Kazan</h2>' +
      '<div>韃靼斯坦共和國的首都，東西方文化在此交會。請選擇你想認識的主題。</div>' +
      photo('Destination-Kazan.jpg', '喀山市區景色'),
      { 1: '歷史與克里姆林宮', 2: '清真寺與信仰', 3: '博物館巡禮', 4: '在地美食' });

    var history = make(pos.x, pos.y - 240, '喀山克里姆林宮',
      '<div>世界文化遺產，白色城牆環繞著千年的城市記憶。</div>' +
      photo('kzn-2.jpg', '喀山克里姆林宮'),
      { 3: '回到起點', 2: '前往清真寺' });

    var mosque = make(pos.x + 300, pos.y, '庫爾沙里夫清真寺',
      '<div>克里姆林宮內最醒目的藍色圓頂，象徵韃靼文化的復興。</div>' +
      photo('museum-of-islamic-culture-1.jpg', '伊斯蘭文化博物館'),
      { 4: '回到起點', 3: '看看博物館' });

    var museum = make(pos.x, pos.y + 240, '國家博物館',
      '<div>從古代文物到當代藝術，一次讀懂韃靼斯坦。</div>' +
      photo('national-museum-of-the-republic-of-tatarstan-1.jpg', '韃靼斯坦國家博物館'),
      { 1: '回到起點', 4: '嚐嚐在地美食' });

    var food = make(pos.x - 300, pos.y, '恰克恰蜜糖點心',
      '<div>Chak-chak 是韃靼人的待客之道，甜而不膩。</div>' +
      photo('chak-chak-museum-2.png', '恰克恰博物館'),
      { 2: '回到起點', 1: '再看一次克里姆林宮' });

    var elements = [rootEl, history, mosque, museum, food];
    var links = [
      { id: store.uid('lk'), from: rootEl.id, section: 1, to: history.id },
      { id: store.uid('lk'), from: rootEl.id, section: 2, to: mosque.id },
      { id: store.uid('lk'), from: rootEl.id, section: 3, to: museum.id },
      { id: store.uid('lk'), from: rootEl.id, section: 4, to: food.id },
      { id: store.uid('lk'), from: history.id, section: 3, to: rootEl.id },
      { id: store.uid('lk'), from: history.id, section: 2, to: mosque.id },
      { id: store.uid('lk'), from: mosque.id, section: 4, to: rootEl.id },
      { id: store.uid('lk'), from: mosque.id, section: 3, to: museum.id },
      { id: store.uid('lk'), from: museum.id, section: 1, to: rootEl.id },
      { id: store.uid('lk'), from: museum.id, section: 4, to: food.id },
      { id: store.uid('lk'), from: food.id, section: 2, to: rootEl.id },
      { id: store.uid('lk'), from: food.id, section: 1, to: history.id }
    ];
    return { rootId: rootEl.id, elements: elements, links: links };
  }

  // 首次使用（畫布只有一個預設根元素、且尚未有連接）時自動載入範例，方便展示
  if (store.getState().elements.length === 1 && !store.getState().links.length &&
      store.getState().elements[0].title === '起點：喀山') {
    store.replace(buildSample(canvasCenter()));
  }

  // 允許以網址參數 ?view=1 直接開啟查看模式（供行銷頁面的「立即體驗」連結使用）
  if (window.location.search.indexOf('view=1') >= 0) {
    KE.Viewer.open(true);
  }
}());

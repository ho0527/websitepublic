/**
 * editor.js － 路線編輯器
 * 負責：節點繪製、4 個互動區域、新增／刪除元素、拖曳移動、
 *       Shift 拖曳連接兩個既有元素、連接線繪製與刪除。
 */
window.KE = window.KE || {};

KE.Editor = (function () {
  'use strict';

  var NODE_SIZE = 132;                 // 節點直徑（需與 CSS 的 --node-size 一致）
  var SECTIONS = [1, 2, 3, 4];
  // 各區域對應的方向位移：1 上、2 右、3 下、4 左
  var OFFSET = {
    1: { x: 0, y: -240 },
    2: { x: 300, y: 0 },
    3: { x: 0, y: 240 },
    4: { x: -300, y: 0 }
  };
  var SECTION_NAME = { 1: '上', 2: '右', 3: '下', 4: '左' };

  var canvas, svg, store;
  var nodeMap = {};        // id -> DOM 節點
  var selectedLinkId = null;
  var drag = null;         // 目前拖曳狀態
  var onEditRequest = null;

  /** 建立 SVG 元素的小工具 */
  function svgEl(name, attrs) {
    var node = document.createElementNS('http://www.w3.org/2000/svg', name);
    Object.keys(attrs || {}).forEach(function (key) {
      node.setAttribute(key, attrs[key]);
    });
    return node;
  }

  /** 取得節點中心座標（畫布座標系） */
  function centerOf(el) {
    return { x: el.x, y: el.y };
  }

  /** 依角度把線段端點推到圓周上，避免線壓在節點上 */
  function edgePoint(from, to) {
    var dx = to.x - from.x;
    var dy = to.y - from.y;
    var len = Math.sqrt(dx * dx + dy * dy) || 1;
    var r = NODE_SIZE / 2 + 6;
    return { x: from.x + (dx / len) * r, y: from.y + (dy / len) * r };
  }

  /** 重繪所有連接線 */
  function renderLinks() {
    // 僅清除線條，保留 <defs>
    Array.prototype.slice.call(svg.querySelectorAll('.link-group')).forEach(function (g) {
      g.remove();
    });

    store.getState().links.forEach(function (link) {
      var a = store.getElement(link.from);
      var b = store.getElement(link.to);
      if (!a || !b) { return; }
      var p1 = edgePoint(centerOf(a), centerOf(b));
      var p2 = edgePoint(centerOf(b), centerOf(a));

      // 若兩元素間存在反向連接，讓線條往側邊彎曲，避免兩條線重疊
      var hasReverse = store.getState().links.some(function (other) {
        return other.from === link.to && other.to === link.from;
      });
      var bend = hasReverse ? 26 : 0;
      var mx = (p1.x + p2.x) / 2;
      var my = (p1.y + p2.y) / 2;
      var dx = p2.x - p1.x;
      var dy = p2.y - p1.y;
      var len = Math.sqrt(dx * dx + dy * dy) || 1;
      var cx = mx + (-dy / len) * bend;
      var cy = my + (dx / len) * bend;
      var d = 'M' + p1.x + ',' + p1.y + ' Q' + cx + ',' + cy + ' ' + p2.x + ',' + p2.y;

      var group = svgEl('g', { class: 'link-group' });
      // 加寬的透明線：擴大點擊範圍
      var hit = svgEl('path', { d: d, class: 'link-hit' });
      var line = svgEl('path', {
        d: d,
        class: 'link-line' + (selectedLinkId === link.id ? ' is-selected' : ''),
        'marker-end': 'url(#arrow-head)'
      });
      // 區域編號標籤置於曲線約 30% 處，讓使用者看得出這條線是從哪個區域出去
      var t = 0.3;
      var lx = (1 - t) * (1 - t) * p1.x + 2 * (1 - t) * t * cx + t * t * p2.x;
      var ly = (1 - t) * (1 - t) * p1.y + 2 * (1 - t) * t * cy + t * t * p2.y;
      var label = svgEl('text', { x: lx, y: ly - 6, class: 'link-label' });
      label.textContent = String(link.section);

      group.appendChild(hit);
      group.appendChild(line);
      group.appendChild(label);
      group.addEventListener('mousedown', function (evt) {
        evt.stopPropagation();
        selectLink(link.id);
      });
      svg.appendChild(group);
    });
  }

  /** 選取連接（之後可用 Delete / Backspace 刪除） */
  function selectLink(id) {
    selectedLinkId = id;
    renderLinks();
    canvas.focus();
  }

  /** 建立單一節點的 DOM */
  function buildNode(el, isRoot) {
    var node = document.createElement('div');
    node.className = 'node' + (isRoot ? ' node--root' : '');
    node.dataset.id = el.id;
    node.style.left = el.x + 'px';
    node.style.top = el.y + 'px';

    var sectors = document.createElement('div');
    sectors.className = 'node__sectors';
    SECTIONS.forEach(function (section) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'sector sector--' + section;
      btn.dataset.section = String(section);
      btn.innerHTML = '<span class="sector__num">' + section + '</span>';
      var caption = el.captions[section] ? '：' + el.captions[section] : '';
      btn.setAttribute('aria-label',
        '區域 ' + section + '（' + SECTION_NAME[section] + '）' + caption + '，點擊以新增相連元素');
      sectors.appendChild(btn);
    });

    var body = document.createElement('span');
    body.className = 'node__title';
    body.textContent = el.title;

    var tools = document.createElement('div');
    tools.className = 'node__tools';
    tools.innerHTML =
      '<button type="button" class="node__tool node__tool--edit" ' +
      'aria-label="編輯元素「' + el.title + '」的內容">編輯</button>' +
      (isRoot ? '' :
        '<button type="button" class="node__tool node__tool--delete" ' +
        'aria-label="刪除元素「' + el.title + '」">刪除</button>');

    node.appendChild(sectors);
    node.appendChild(body);
    node.appendChild(tools);
    return node;
  }

  /** 重繪整個畫布 */
  function render() {
    var state = store.getState();
    // 移除舊節點
    Array.prototype.slice.call(canvas.querySelectorAll('.node')).forEach(function (n) {
      n.remove();
    });
    nodeMap = {};
    state.elements.forEach(function (el) {
      var node = buildNode(el, el.id === state.rootId);
      nodeMap[el.id] = node;
      canvas.appendChild(node);
    });
    renderLinks();
  }

  /** 依區域方向找出不重疊的新座標 */
  function newPositionFrom(el, section) {
    var pos = { x: el.x + OFFSET[section].x, y: el.y + OFFSET[section].y };
    var tries = 0;
    while (tries < 24 && store.getState().elements.some(function (other) {
      return Math.abs(other.x - pos.x) < NODE_SIZE && Math.abs(other.y - pos.y) < NODE_SIZE;
    })) {
      pos.x += 40;
      pos.y += 40;
      tries += 1;
    }
    return pos;
  }

  /** 點擊區域：建立新元素與連接 */
  function createFromSection(elId, section) {
    var el = store.getElement(elId);
    if (!el) { return; }
    if (store.getLinkFrom(elId, section)) {
      // 該區域已有連接，改為選取該連接讓使用者可刪除
      selectLink(store.getLinkFrom(elId, section).id);
      return;
    }
    var pos = newPositionFrom(el, section);
    var created = store.addElement(pos.x, pos.y, '新元素');
    store.addLink(elId, section, created.id);
    if (!el.captions[section]) {
      store.updateElement(elId, { captions: Object.assign({}, el.captions,
        (function () { var patch = {}; patch[section] = created.title; return patch; }())) });
    }
  }

  /** 滑鼠按下：判斷是拖曳節點、Shift 連線，還是點空白處取消選取 */
  function onMouseDown(evt) {
    var sector = evt.target.closest ? evt.target.closest('.sector') : null;
    var node = evt.target.closest ? evt.target.closest('.node') : null;

    if (!node) {
      selectedLinkId = null;
      renderLinks();
      return;
    }
    var id = node.dataset.id;

    if (sector && evt.shiftKey) {
      // Shift + 由區域拖出 → 連接兩個既有元素
      evt.preventDefault();
      var el = store.getElement(id);
      drag = {
        type: 'link',
        fromId: id,
        section: Number(sector.dataset.section),
        temp: svgEl('line', { x1: el.x, y1: el.y, x2: el.x, y2: el.y, class: 'link-temp' })
      };
      svg.appendChild(drag.temp);
      canvas.classList.add('is-linking');
      return;
    }
    if (sector || evt.target.closest('.node__tool')) {
      return; // 交給 click 事件處理
    }

    // 一般拖曳移動節點
    evt.preventDefault();
    var target = store.getElement(id);
    var rect = canvas.getBoundingClientRect();
    drag = {
      type: 'move',
      id: id,
      dx: (evt.clientX - rect.left + canvas.scrollLeft) - target.x,
      dy: (evt.clientY - rect.top + canvas.scrollTop) - target.y,
      moved: false
    };
    node.classList.add('is-dragging');
  }

  function onMouseMove(evt) {
    if (!drag) { return; }
    var rect = canvas.getBoundingClientRect();
    var px = evt.clientX - rect.left + canvas.scrollLeft;
    var py = evt.clientY - rect.top + canvas.scrollTop;

    if (drag.type === 'move') {
      var x = Math.max(NODE_SIZE / 2, px - drag.dx);
      var y = Math.max(NODE_SIZE / 2, py - drag.dy);
      store.moveElement(drag.id, x, y);
      var node = nodeMap[drag.id];
      node.style.left = x + 'px';
      node.style.top = y + 'px';
      drag.moved = true;
      renderLinks();                   // 連接線跟隨元素
    } else if (drag.type === 'link') {
      drag.temp.setAttribute('x2', px);
      drag.temp.setAttribute('y2', py);
    }
  }

  function onMouseUp(evt) {
    if (!drag) { return; }
    if (drag.type === 'link') {
      var node = evt.target.closest ? evt.target.closest('.node') : null;
      if (node && node.dataset.id !== drag.fromId) {
        store.addLink(drag.fromId, drag.section, node.dataset.id);
      }
      drag.temp.remove();
      canvas.classList.remove('is-linking');
      renderLinks();
    } else if (drag.type === 'move') {
      nodeMap[drag.id].classList.remove('is-dragging');
      store.commit();
    }
    drag = null;
  }

  /** 點擊：區域、編輯、刪除 */
  function onClick(evt) {
    var node = evt.target.closest ? evt.target.closest('.node') : null;
    if (!node) { return; }
    var id = node.dataset.id;

    var sector = evt.target.closest('.sector');
    if (sector) {
      if (evt.shiftKey) { return; }
      createFromSection(id, Number(sector.dataset.section));
      return;
    }
    if (evt.target.closest('.node__tool--edit')) {
      onEditRequest && onEditRequest(id);
      return;
    }
    if (evt.target.closest('.node__tool--delete')) {
      var el = store.getElement(id);
      if (window.confirm('確定要刪除元素「' + el.title + '」？其所有連接也會一併刪除。')) {
        store.removeElement(id);
      }
    }
  }

  /** 鍵盤：Delete / Backspace 刪除已選取的連接 */
  function onKeyDown(evt) {
    if ((evt.key === 'Delete' || evt.key === 'Backspace') && selectedLinkId) {
      evt.preventDefault();
      store.removeLink(selectedLinkId);
      selectedLinkId = null;
    }
  }

  return {
    /** 初始化編輯器 */
    init: function (options) {
      canvas = options.canvas;
      svg = options.svg;
      store = KE.Store;
      onEditRequest = options.onEditRequest;

      canvas.addEventListener('mousedown', onMouseDown);
      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
      canvas.addEventListener('click', onClick);
      document.addEventListener('keydown', onKeyDown);

      store.subscribe(render);
      render();
    },
    render: render,
    NODE_SIZE: NODE_SIZE,
    OFFSET: OFFSET,
    SECTION_NAME: SECTION_NAME
  };
}());

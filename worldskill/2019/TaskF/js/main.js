/**
 * main.js － 行銷單頁的互動行為
 * 1. 頁首捲動陰影
 * 2. 「兩種模式」頁籤（含鍵盤左右鍵切換）
 * 3. 「如何運作」互動步驟
 * 4. 捲動進場動畫
 * 5. 1440 × 900 設計邊界切換
 */
(function () {
  'use strict';

  /* ---------- 1. 頁首捲動陰影 ---------- */
  var header = document.getElementById('site-header');
  function updateHeader() {
    header.classList.toggle('is-stuck', window.scrollY > 12);
  }
  window.addEventListener('scroll', updateHeader, { passive: true });
  updateHeader();

  /* ---------- 2. 兩種模式頁籤 ---------- */
  var tabs = Array.prototype.slice.call(document.querySelectorAll('.tabs__tab'));

  function activateTab(tab, setFocus) {
    tabs.forEach(function (item) {
      var selected = item === tab;
      item.classList.toggle('is-active', selected);
      item.setAttribute('aria-selected', String(selected));
      item.tabIndex = selected ? 0 : -1;
      document.getElementById(item.getAttribute('aria-controls')).hidden = !selected;
    });
    if (setFocus) { tab.focus(); }
  }

  tabs.forEach(function (tab, index) {
    tab.addEventListener('click', function () { activateTab(tab, false); });
    tab.addEventListener('keydown', function (evt) {
      if (evt.key !== 'ArrowRight' && evt.key !== 'ArrowLeft') { return; }
      evt.preventDefault();
      var step = evt.key === 'ArrowRight' ? 1 : -1;
      activateTab(tabs[(index + step + tabs.length) % tabs.length], true);
    });
  });

  /* ---------- 3. 如何運作：互動步驟 ---------- */
  // 每個步驟對應的截圖、標籤、說明與提示圈位置（以截圖百分比定位）
  var STEP_DATA = {
    1: {
      image: 'screenshots/editor.png',
      mode: 'route editor',
      alt: '步驟 1：在路線編輯器中建立投影片並填入內容',
      caption: '步驟 1：在路線編輯器中新增元素，並用所見即所得編輯器填入內容。',
      spot: { left: '39%', top: '45%' }
    },
    2: {
      image: 'screenshots/editor.png',
      mode: 'route editor',
      alt: '步驟 2：在路線編輯器中把投影片以連接線串起來',
      caption: '步驟 2：點擊元素的 4 個互動區域，或按住 Shift 拖曳，把投影片連在一起。',
      spot: { left: '22%', top: '28%' }
    },
    3: {
      image: 'screenshots/viewer.png',
      mode: 'view mode',
      alt: '步驟 3：在查看模式中檢視完成的互動簡報',
      caption: '步驟 3：切換到查看模式，訪客可用畫面下方的按鈕或鍵盤 1 至 4 自由探索。',
      spot: { left: '46%', top: '86%' }
    }
  };

  var steps = Array.prototype.slice.call(document.querySelectorAll('.step'));
  var howImage = document.getElementById('how-image');
  var howMode = document.getElementById('how-mode');
  var howCaption = document.getElementById('how-caption');
  var howSpot = document.getElementById('how-spot');

  function showStep(number) {
    var data = STEP_DATA[number];
    if (!data) { return; }
    howImage.src = data.image;
    howImage.alt = data.alt;
    howMode.textContent = data.mode;
    howCaption.textContent = data.caption;
    howSpot.style.left = data.spot.left;
    howSpot.style.top = data.spot.top;
    steps.forEach(function (step) {
      step.classList.toggle('is-active', step.dataset.step === String(number));
    });
  }

  steps.forEach(function (step) {
    step.addEventListener('click', function () { showStep(step.dataset.step); });
  });
  showStep(1);

  /* ---------- 4. 捲動進場動畫 ---------- */
  var revealTargets = document.querySelectorAll(
    '.hero__copy, .hero__visual, .tabs, .cards > li, .how__grid > *, .gallery > li, .cta__inner > *'
  );
  Array.prototype.forEach.call(revealTargets, function (node) {
    node.classList.add('reveal');
  });

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    Array.prototype.forEach.call(revealTargets, function (node) { observer.observe(node); });
  } else {
    Array.prototype.forEach.call(revealTargets, function (node) {
      node.classList.add('is-visible');
    });
  }

  /* ---------- 5. 1440 × 900 設計邊界 ---------- */
  var guide = document.getElementById('design-guide');
  var guideToggle = document.getElementById('guide-toggle');
  guideToggle.addEventListener('click', function () {
    var next = guide.hidden;
    guide.hidden = !next;
    guideToggle.setAttribute('aria-pressed', String(next));
    guideToggle.textContent = next ? '隱藏 1440 × 900 設計邊界' : '顯示 1440 × 900 設計邊界';
  });
}());

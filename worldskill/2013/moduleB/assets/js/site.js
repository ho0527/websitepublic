/* FASHION4YOU 前端互動：手機／平板主選單展開收合 */
(function () {
  "use strict";

  var toggle = document.getElementById("menu-toggle");
  var menu = document.getElementById("main-menu");
  if (!toggle || !menu) { return; }

  toggle.addEventListener("click", function () {
    var open = menu.classList.toggle("is-open");
    toggle.setAttribute("aria-expanded", open ? "true" : "false");
  });

  /* 匯出 mock-up 圖時（?menu=open）強制展開選單，讓截圖呈現展開狀態 */
  if (location.search.indexOf("menu=open") !== -1) {
    menu.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
  }
}());

/* 匯出整頁 mock-up 時（?flat=1）把固定底部操作列改成一般文件流，
   這樣截圖的頁面高度＝真實內容高度，底部不會留下大片空白。 */
(function () {
  "use strict";
  if (location.search.indexOf("flat") !== -1) {
    document.documentElement.classList.add("is-flat");
  }
}());

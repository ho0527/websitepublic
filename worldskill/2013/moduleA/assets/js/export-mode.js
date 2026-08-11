/* 匯出模式：在網址加上 ?export=1 時隱藏「設計稿導覽」浮動列，
   讓 headless Chrome 輸出的 PNG 只包含設計本身。 */
(function () {
  "use strict";
  if (location.search.indexOf("export") === -1) { return; }
  document.documentElement.classList.add("is-export");
}());

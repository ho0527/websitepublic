//Here is your CODE!

/**
 * 收集與指定 Range 相交、且需要被標記的所有文字節點
 * @param {Range} range 使用者選取的範圍
 * @returns {Text[]} 相交的文字節點陣列
 */
var collectTextNodes = (range) => {
    var root = range.commonAncestorContainer;
    // TreeWalker 的根必須是元素節點，若共同祖先本身是文字節點就直接回傳它
    if (root.nodeType === Node.TEXT_NODE) {
        return [root];
    }

    var nodes = [];
    var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
    var current = walker.nextNode();

    while (current) {
        // 跳過純空白節點（例如標籤之間的換行縮排），避免產生多餘的黃色區塊
        var isBlank = current.nodeValue.trim() === '';
        // 已經被標記過的文字不需要重複包裝
        var isMarked = current.parentNode &&
            current.parentNode.nodeType === Node.ELEMENT_NODE &&
            current.parentNode.classList.contains('highlight');

        if (!isBlank && !isMarked && range.intersectsNode(current)) {
            nodes.push(current);
        }
        current = walker.nextNode();
    }
    return nodes;
};

/**
 * 將單一文字節點中指定區間的文字包進 <span class="highlight">
 * @param {Text} textNode 目標文字節點
 * @param {number} start 起始位移
 * @param {number} end 結束位移
 */
var wrapTextRange = (textNode, start, end) => {
    if (start >= end) {
        return;
    }

    var partial = document.createRange();
    partial.setStart(textNode, start);
    partial.setEnd(textNode, end);

    var mark = document.createElement('span');
    mark.className = 'highlight';
    // 選取範圍落在單一文字節點內，surroundContents 一定安全
    partial.surroundContents(mark);
};

/**
 * 標記單一 Range（可跨多個文字節點、跨段落）
 * @param {Range} range 使用者選取的範圍
 */
var highlightRange = (range) => {
    if (range.collapsed) {
        return;
    }

    var startNode = range.startContainer;
    var startOffset = range.startOffset;
    var endNode = range.endContainer;
    var endOffset = range.endOffset;

    // 先一次收集完所有節點，之後的切割才不會影響走訪結果
    var textNodes = collectTextNodes(range);

    textNodes.forEach((node) => {
        var start = node === startNode ? startOffset : 0;
        var end = node === endNode ? endOffset : node.nodeValue.length;
        wrapTextRange(node, start, end);
    });
};

// 快取最後一次有效的選取範圍，避免點擊按鈕時瀏覽器已把選取清除
var cachedRanges = [];

/**
 * 讀取目前（或最後一次有效的）選取範圍副本
 * @returns {Range[]} Range 副本陣列
 */
var readRanges = () => {
    var selection = window.getSelection();
    if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
        return cachedRanges;
    }

    var ranges = [];
    for (var i = 0; i < selection.rangeCount; i += 1) {
        ranges.push(selection.getRangeAt(i).cloneRange());
    }
    return ranges;
};

/**
 * Render 按鈕的處理函式：標記目前選取的文字，並保留先前所有標記
 */
var render = (e) => {
    var ranges = readRanges();
    if (ranges.length === 0) {
        return;
    }

    ranges.forEach((range) => highlightRange(range));
    cachedRanges = [];

    var selection = window.getSelection();
    if (!selection) {
        return;
    }

    // 標記完成後清除選取狀態，讓黃底紅字的結果清楚可見
    selection.removeAllRanges();
};

// index.js 於 <head> 同步載入，此時 DOM 尚未建立，
// 且 HTML 的 onclick="render" 並未實際呼叫函式，因此自行綁定 click 事件
document.addEventListener('DOMContentLoaded', () => {
    // 隨時記錄使用者最新的有效選取範圍
    document.addEventListener('selectionchange', () => {
        var selection = window.getSelection();
        if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
            return;
        }
        cachedRanges = readRanges();
    });

    var button = document.querySelector('.render-btn');
    if (button) {
        // 阻擋 mousedown 預設行為，按下按鈕時選取範圍才不會被清掉
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', render);
    }
});

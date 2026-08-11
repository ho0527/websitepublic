/**
 * Word Rank
 * 取得一串句子中「分數最高」的單字。
 * 計分規則：a/A = 1, b/B = 2, ... z/Z = 26，其他字元不計分。
 * 回傳字串只保留英文字母（a-z / A-Z）且保留原本大小寫；同分時取最先出現者。
 */

/**
 * 計算單一單字的分數（只計算 a-z / A-Z）
 * @param {string} word 單字
 * @returns {number} 分數
 */
function wordScore(word) {
  let score = 0;

  for (const char of word) {
    const upper = char.toUpperCase();
    // 只有 A~Z 才計分，其餘字元（數字、標點、空白）一律略過
    if (upper >= "A" && upper <= "Z") {
      score += upper.charCodeAt(0) - 64; // 'A' 的 charCode 為 65，減 64 即得 1
    }
  }

  return score;
}

/**
 * 只保留字串中的英文字母（保留大小寫）
 * @param {string} word 單字
 * @returns {string} 只含字母的字串
 */
function lettersOnly(word) {
  return word.replace(/[^a-zA-Z]/g, "");
}

/**
 * 主要函式：回傳分數最高的單字（只含字母、保留大小寫）
 * @param {string} str 一串以空白分隔的單字
 * @returns {string} 最高分的單字
 */
function wordRank(str) {
  if (typeof str !== "string") {
    return "";
  }

  // 以任意空白字元切割成單字，並濾掉空字串
  const words = str.split(/\s+/).filter((word) => word.length > 0);

  let bestWord = "";
  let bestScore = -1;

  for (const word of words) {
    const score = wordScore(word);

    // 嚴格大於才更新 → 同分時保留先出現的單字
    if (score > bestScore) {
      bestScore = score;
      bestWord = lettersOnly(word);
    }
  }

  return bestWord;
}

/**
 * 產生每個單字的得分明細（供頁面表格顯示用）
 * @param {string} str 一串以空白分隔的單字
 * @returns {Array<{original: string, letters: string, score: number}>}
 */
function wordScoreTable(str) {
  return str
    .split(/\s+/)
    .filter((word) => word.length > 0)
    .map((word) => ({
      original: word,
      letters: lettersOnly(word),
      score: wordScore(word),
    }));
}

/* ---------------- 頁面互動 ---------------- */

document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("sentence");
  const form = document.getElementById("word-rank-form");
  const resultBox = document.getElementById("result");
  const tableBody = document.querySelector("#score-table tbody");
  const testBody = document.querySelector("#test-table tbody");

  /**
   * 依輸入內容更新結果與得分表
   */
  function render() {
    const sentence = input.value;
    const winner = wordRank(sentence);
    const rows = wordScoreTable(sentence);

    resultBox.textContent = winner === "" ? "（沒有可計分的單字）" : winner;

    tableBody.innerHTML = "";
    const bestScore = rows.reduce((max, row) => Math.max(max, row.score), -1);
    let highlighted = false;

    for (const row of rows) {
      const tr = document.createElement("tr");
      // 只標記第一個達到最高分的單字（同分取先出現者）
      if (!highlighted && row.score === bestScore) {
        tr.className = "winner";
        highlighted = true;
      }

      const cells = [row.original, row.letters, String(row.score)];
      for (const text of cells) {
        const td = document.createElement("td");
        td.textContent = text;
        tr.appendChild(td);
      }

      tableBody.appendChild(tr);
    }
  }

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    render();
  });

  /* ---------------- 測試案例 ---------------- */

  const testCases = [
    { input: "man i need a taxi up to ubud", expected: "taxi" },
    { input: "what time are we climbing up the volcano", expected: "volcano" },
    { input: "take me to semynak", expected: "semynak" },
    { input: "aa11b", expected: "aab" }, // 數字不計分，且回傳只含字母
    { input: "Hello World", expected: "World" }, // 保留大小寫
    { input: "abc cba", expected: "abc" }, // 同分取先出現者
    { input: "", expected: "" }, // 空字串
  ];

  for (const testCase of testCases) {
    const actual = wordRank(testCase.input);
    const passed = actual === testCase.expected;

    const tr = document.createElement("tr");
    tr.className = passed ? "pass" : "fail";

    const cells = [
      JSON.stringify(testCase.input),
      testCase.expected,
      actual,
      passed ? "PASS" : "FAIL",
    ];

    for (const text of cells) {
      const td = document.createElement("td");
      td.textContent = text;
      tr.appendChild(td);
    }

    testBody.appendChild(tr);
  }

  // 首次載入即顯示預設句子的結果
  render();
});

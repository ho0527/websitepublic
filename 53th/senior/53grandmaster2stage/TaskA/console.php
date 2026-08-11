<?php
/**
 * 內建 GraphQL 查詢主控台（純自製，不使用任何外部 CDN）
 * 僅供開發與驗證使用，由 index.php 以 GET 方式載入。
 */
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>模組 A - GraphQL 圖書管理系統</title>
    <style>
        :root {
            --bg: #10141c;
            --panel: #1b2230;
            --line: #2c3648;
            --text: #e6ebf5;
            --muted: #93a1b8;
            --accent: #f19e0d;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            font-family: "Segoe UI", "Microsoft JhengHei", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        h1 { font-size: 20px; margin: 0 0 4px; }

        p.lead { margin: 0 0 20px; color: var(--muted); font-size: 14px; }

        .layout { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } }

        fieldset {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--panel);
            padding: 14px;
        }

        legend { color: var(--accent); font-weight: 700; padding: 0 6px; }

        label { display: block; font-size: 13px; color: var(--muted); margin: 10px 0 4px; }

        textarea, input, pre, select {
            width: 100%;
            background: #0d1119;
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 10px;
            font-family: Consolas, "Courier New", monospace;
            font-size: 13px;
        }

        textarea { min-height: 220px; resize: vertical; }

        pre { min-height: 220px; overflow: auto; white-space: pre-wrap; word-break: break-word; }

        button {
            margin-top: 12px;
            background: var(--accent);
            color: #201400;
            border: 0;
            border-radius: 6px;
            padding: 10px 18px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover { filter: brightness(1.1); }

        button:active { transform: translateY(1px); }

        .hint { font-size: 12px; color: var(--muted); margin-top: 8px; line-height: 1.6; }

        code { color: var(--accent); }
    </style>
</head>
<body>
    <h1>第 47 屆國手選拔 2 階 - 模組 A：GraphQL 圖書管理系統</h1>
    <p class="lead">端點：<code><?= htmlspecialchars(strtok($_SERVER['REQUEST_URI'] ?? '/', '?'), ENT_QUOTES, 'UTF-8') ?></code>（POST，Content-Type: application/json）</p>

    <div class="layout">
        <fieldset>
            <legend>請求</legend>

            <label for="preset">範例查詢</label>
            <select id="preset">
                <option value="">-- 選擇範例 --</option>
            </select>

            <label for="token">Authorization: Bearer</label>
            <input id="token" type="text" placeholder="登入後取得的 user_token">

            <label for="query">Query / Mutation</label>
            <textarea id="query" spellcheck="false"></textarea>

            <button id="send" type="button">送出查詢</button>

            <p class="hint">
                內建帳號：<code>admin@localhost / adminpass</code>（ADMIN）、<code>user1@localhost / user1pass</code>（USER）。<br>
                需要重建資料庫時請開啟 <code>setup.php</code>。
            </p>
        </fieldset>

        <fieldset>
            <legend>回應</legend>
            <pre id="result">尚未送出查詢。</pre>
        </fieldset>
    </div>

    <script>
        // 範例查詢清單（對應試題的 10 個工作項目）
        const presets = [
            ['1. 訪客登入', 'mutation userLogin {\n  login(email: "admin@localhost", password: "adminpass") {\n    user_token\n  }\n}'],
            ['2. 訪客登出', 'mutation userLogout {\n  logout {\n    message\n  }\n}'],
            ['3. 訪客註冊', 'mutation userRegister {\n  register(email: "user2@localhost", password: "user2pass", username: "user2") {\n    message\n  }\n}'],
            ['4. 取得會員自身資料', 'query getUser {\n  user {\n    id\n    email\n    username\n    role\n  }\n}'],
            ['5. 取得書籍列表', 'query getBooks {\n  books {\n    id\n    name\n    isbn\n    author\n    created_at\n    reader {\n      username\n    }\n  }\n}'],
            ['6. 新增書本', 'mutation createBook {\n  insertBook(name: "Atomic Habits", isbn: "978-073-521-129-2", author: "James Clear") {\n    id\n  }\n}'],
            ['7. 刪除書本', 'mutation deleteBook {\n  removeBook(id: 2) {\n    message\n  }\n}'],
            ['8. 取得會員當前租借列表', 'query getRents {\n  rents {\n    id\n    created_at\n    book {\n      id\n      name\n    }\n  }\n}'],
            ['9. 租借書本', 'mutation borrowBook {\n  insertRent(bookId: 2) {\n    id\n  }\n}'],
            ['10. 歸還書本', 'mutation returnBook {\n  removeRent(id: 2) {\n    message\n  }\n}']
        ];

        const presetSelect = document.getElementById('preset');
        const queryInput = document.getElementById('query');
        const tokenInput = document.getElementById('token');
        const resultBox = document.getElementById('result');

        presets.forEach(function (preset, index) {
            const option = document.createElement('option');
            option.value = String(index);
            option.textContent = preset[0];
            presetSelect.appendChild(option);
        });

        // 預設載入第一個範例
        queryInput.value = presets[0][1];

        presetSelect.addEventListener('change', function () {
            if (this.value !== '') {
                queryInput.value = presets[Number(this.value)][1];
            }
        });

        document.getElementById('send').addEventListener('click', async function () {
            const headers = { 'Content-Type': 'application/json' };
            if (tokenInput.value.trim() !== '') {
                headers.Authorization = 'Bearer ' + tokenInput.value.trim();
            }

            resultBox.textContent = '查詢中…';

            try {
                const response = await fetch(location.pathname, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({ query: queryInput.value })
                });
                const payload = await response.json();

                // 使用 textContent 輸出，避免任何 XSS 風險
                resultBox.textContent = JSON.stringify(payload, null, 4);

                // 登入成功時自動帶入權杖，方便後續操作
                if (payload.data && payload.data.login && payload.data.login.user_token) {
                    tokenInput.value = payload.data.login.user_token;
                }
            } catch (error) {
                resultBox.textContent = '請求失敗：' + error.message;
            }
        });
    </script>
</body>
</html>

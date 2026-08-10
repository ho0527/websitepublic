/**
 * speedtest.js
 * 速度競賽索引頁的共用邏輯：工具列、搜尋、等級/狀態過濾、統計與卡片繪製。
 * 頁面只要提供全域變數 SPEEDTEST_DATA 即可，格式為：
 *   {
 *     "分組名稱": [
 *       { code:"A01", title:"題目", level:1, path:"A01/", status:"done", updated:"20260522" }
 *     ]
 *   }
 * level 可省略(省略時不顯示等級標籤，也不出現等級過濾按鈕)
 * path  可省略(省略時該題不提供連結)
 * status 為 "todo" | "process" | "done"
 */

(function () {
    "use strict"

    const STATUS_LABEL = { todo: "未開始", process: "進行中", done: "已完成" }

    const data = typeof SPEEDTEST_DATA === "undefined" ? {} : SPEEDTEST_DATA
    const toolbarElement = document.getElementById("toolbar")
    const statsElement = document.getElementById("stats")
    const mainElement = document.getElementById("main")

    let currentLevel = "all"
    let currentStatus = "all"
    let currentQuery = ""

    /** 取得所有題目(攤平) */
    function getAllItems() {
        return Object.values(data).reduce(function (all, items) {
            return all.concat(items)
        }, [])
    }

    /** 這份資料是否有標示等級 */
    function hasLevel() {
        return getAllItems().some(function (item) {
            return item.level !== undefined && item.level !== null
        })
    }

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, function (character) {
            return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[character]
        })
    }

    /** 建立工具列(搜尋框 + 等級/狀態過濾按鈕) */
    function renderToolbar() {
        const levelButtons = hasLevel()
            ? '<button class="filter-btn" data-level="1">Lv 1</button>' +
              '<button class="filter-btn" data-level="2">Lv 2</button>' +
              '<button class="filter-btn" data-level="3">Lv 3</button>'
            : ""
        toolbarElement.innerHTML = `
            <input class="search" id="search" type="text" placeholder="搜尋題號或題目..." />
            <button class="filter-btn active" data-level="all">全部</button>
            ${levelButtons}
            <button class="filter-btn" data-status="todo">未做</button>
            <button class="filter-btn" data-status="process">進行中</button>
            <button class="filter-btn" data-status="done">完成</button>
        `

        toolbarElement.querySelectorAll(".filter-btn[data-level]").forEach(function (button) {
            button.addEventListener("click", function () {
                toolbarElement.querySelectorAll(".filter-btn[data-level]").forEach(function (other) {
                    other.classList.remove("active")
                })
                button.classList.add("active")
                currentLevel = button.dataset.level
                render()
            })
        })

        toolbarElement.querySelectorAll(".filter-btn[data-status]").forEach(function (button) {
            button.addEventListener("click", function () {
                if (currentStatus === button.dataset.status) {
                    currentStatus = "all"
                    button.classList.remove("active")
                } else {
                    toolbarElement.querySelectorAll(".filter-btn[data-status]").forEach(function (other) {
                        other.classList.remove("active")
                    })
                    button.classList.add("active")
                    currentStatus = button.dataset.status
                }
                render()
            })
        })

        toolbarElement.querySelector("#search").addEventListener("input", function (event) {
            currentQuery = event.target.value
            render()
        })
    }

    /** 顯示統計數字 */
    function renderStats() {
        const items = getAllItems()
        const count = { todo: 0, process: 0, done: 0 }
        items.forEach(function (item) {
            count[item.status] = (count[item.status] || 0) + 1
        })
        const percent = items.length ? Math.round((count.done / items.length) * 100) : 0
        statsElement.innerHTML = `
            <span>總計 <b>${items.length}</b></span>
            <span style="color:#ff5c5c">未開始 <b>${count.todo}</b></span>
            <span style="color:#f0c419">進行中 <b>${count.process}</b></span>
            <span style="color:#2ecc71">已完成 <b>${count.done}</b> (${percent}%)</span>
        `
    }

    /** 判斷題目是否符合目前的過濾條件 */
    function isMatched(item) {
        const query = currentQuery.trim().toLowerCase()
        const matchLevel = currentLevel === "all" || String(item.level) === currentLevel
        const matchStatus = currentStatus === "all" || item.status === currentStatus
        const matchQuery = !query ||
            item.code.toLowerCase().includes(query) ||
            item.title.toLowerCase().includes(query)
        return matchLevel && matchStatus && matchQuery
    }

    /** 建立單一張卡片，沒有連結的題目不做成 <a> */
    function createCard(item) {
        const status = item.status || "todo"
        const card = document.createElement(item.path ? "a" : "div")
        card.className = "card status-" + status + (item.path ? "" : " card-nolink")
        if (item.path) {
            card.href = item.path
        }
        const levelBadge = item.level ? `<span class="badge lvl-${item.level}">Lv ${item.level}</span>` : ""
        card.innerHTML = `
            <div class="card-row">
                <div class="info">
                    <div class="code">${escapeHtml(item.code)}</div>
                    <div class="title">${escapeHtml(item.title)}</div>
                </div>
                ${levelBadge}
            </div>
            <div class="meta">
                <span class="status-pill">
                    <span class="status-dot"></span>${STATUS_LABEL[status] || status}
                </span>
                <span class="updated">${item.updated ? escapeHtml(item.updated) : "—"}</span>
            </div>
        `
        return card
    }

    /** 依過濾條件重畫整個列表 */
    function render() {
        mainElement.innerHTML = ""
        let shownCount = 0

        Object.keys(data).forEach(function (sectionName) {
            const filtered = data[sectionName].filter(isMatched)
            if (filtered.length === 0) {
                return
            }

            const section = document.createElement("section")
            const heading = document.createElement("h2")
            heading.innerHTML = `${escapeHtml(sectionName)} <span class="count">(${filtered.length})</span>`
            section.appendChild(heading)

            const grid = document.createElement("div")
            grid.className = "main-grid"
            filtered.forEach(function (item) {
                grid.appendChild(createCard(item))
                shownCount = shownCount + 1
            })
            section.appendChild(grid)
            mainElement.appendChild(section)
        })

        if (shownCount === 0) {
            const empty = document.createElement("div")
            empty.className = "empty"
            empty.textContent = "找不到符合條件的題目。"
            mainElement.appendChild(empty)
        }
        renderStats()
    }

    renderToolbar()
    render()
})()

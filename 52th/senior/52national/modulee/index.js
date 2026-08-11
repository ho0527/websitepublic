/* ==========================================================
   模組 E – 學生資料管理系統（單頁式應用 SPA）
   資料一律儲存於 IndexedDB，全程不重新載入頁面
   ========================================================== */

/* ---------- 常數設定 ---------- */
const DB_NAME = "studentdb";           // 資料庫名稱
const DB_VERSION = 3;                  // 資料庫版本
const STORE_STUDENT = "students";      // 學生資料表
const STORE_TRASH = "trash";           // 垃圾桶資料表
const STORE_CLASS = "classes";         // 班級資料表
const ROW_HEIGHT = 76;                 // 單一 .student 高度（含留白，> 56px）
const POOL_MAX = 20;                   // 畫面中 .student 節點數量上限（RecyclerView）
const AVATAR_SIZE = 120;               // 大頭貼壓縮後尺寸 120px * 120px

/* ---------- 全域狀態 ---------- */
let db = null;                         // IndexedDB 連線
let students = [];                     // 所有未刪除的學生
let trashed = [];                      // 垃圾桶內的學生
let classes = [];                      // 所有班級
let visible = [];                      // 目前畫面上要呈現的學生（已過濾、已排序）
let defaultAvatar = "";                // 預設大頭貼（base64）

/* 目前檢視狀態 */
const viewState = {
    type: "all",                       // all | class | trash
    className: "",                     // type 為 class 時的班級名稱
    keyword: "",                       // 搜尋關鍵字
    sortField: "fullname",             // fullname | student_id | email
    sortOrder: "asc"                   // asc | desc
};

/* ---------- 常用 DOM 節點 ---------- */
const el = {
    aside: document.getElementById("aside"),
    studentList: document.getElementById("studentList"),
    classList: document.getElementById("classList"),
    addClass: document.getElementById("addClass"),
    trashList: document.getElementById("trash"),
    addStudent: document.getElementById("addStudent"),
    exportBtn: document.getElementById("export_data"),
    importBtn: document.getElementById("import_data"),
    importFile: document.getElementById("importFile"),
    searchForm: document.getElementById("searchForm"),
    searchInput: document.querySelector('#searchForm input[name="search"]'),
    searchClear: document.querySelector("#searchForm .search-clear"),
    viewTitle: document.querySelector("#main .view-title"),
    sortField: document.getElementById("sortField"),
    sortOrder: document.getElementById("sortOrder"),
    students: document.querySelector("#main .students"),
    scroller: document.querySelector("#main .scroller"),
    phantom: document.querySelector("#main .phantom"),
    pool: document.querySelector("#main .pool"),
    message: document.querySelector("#main .message")
};

/* ---------- 圖示（內嵌 SVG，避免外連 CDN） ---------- */
const ICONS = {
    edit: '<svg viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>',
    delete: '<svg viewBox="0 0 24 24"><path d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>',
    restore: '<svg viewBox="0 0 24 24"><path d="M13 3a9 9 0 0 0-9 9H1l4 4 4-4H6a7 7 0 1 1 7 7 6.9 6.9 0 0 1-4.33-1.5l-1.42 1.44A9 9 0 1 0 13 3z"/></svg>',
    purge: '<svg viewBox="0 0 24 24"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>',
    plus: '<svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>',
    minus: '<svg viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/></svg>',
    folder: '<svg class="item-icon" viewBox="0 0 24 24"><path d="M10 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-8l-2-2z"/></svg>'
};

/* ==========================================================
   一、IndexedDB 存取層（以 Promise 包裝）
   ========================================================== */

/** 開啟資料庫，若無資料表則建立 */
function openDB(){
    return new Promise(function(resolve, reject){
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = function(event){
            const database = event.target.result;
            if(!database.objectStoreNames.contains(STORE_STUDENT)){
                database.createObjectStore(STORE_STUDENT, { keyPath:"id" });
            }
            if(!database.objectStoreNames.contains(STORE_TRASH)){
                database.createObjectStore(STORE_TRASH, { keyPath:"id" });
            }
            if(!database.objectStoreNames.contains(STORE_CLASS)){
                database.createObjectStore(STORE_CLASS, { keyPath:"name" });
            }
        };

        request.onsuccess = function(event){
            resolve(event.target.result);
        };

        request.onerror = function(){
            reject(request.error);
        };
    });
}

/** 取得指定資料表的全部資料 */
function dbGetAll(storeName){
    return new Promise(function(resolve, reject){
        const request = db.transaction(storeName, "readonly").objectStore(storeName).getAll();
        request.onsuccess = function(){ resolve(request.result || []); };
        request.onerror = function(){ reject(request.error); };
    });
}

/** 寫入（新增或更新）一筆資料 */
function dbPut(storeName, data){
    return new Promise(function(resolve, reject){
        const transaction = db.transaction(storeName, "readwrite");
        transaction.objectStore(storeName).put(data);
        transaction.oncomplete = function(){ resolve(); };
        transaction.onerror = function(){ reject(transaction.error); };
    });
}

/** 刪除一筆資料 */
function dbDelete(storeName, key){
    return new Promise(function(resolve, reject){
        const transaction = db.transaction(storeName, "readwrite");
        transaction.objectStore(storeName).delete(key);
        transaction.oncomplete = function(){ resolve(); };
        transaction.onerror = function(){ reject(transaction.error); };
    });
}

/** 將資料由某資料表搬移到另一資料表 */
function dbMove(fromStore, toStore, key){
    return new Promise(function(resolve, reject){
        const transaction = db.transaction([fromStore, toStore], "readwrite");
        const source = transaction.objectStore(fromStore);
        const request = source.get(key);

        request.onsuccess = function(){
            const record = request.result;
            if(record){
                transaction.objectStore(toStore).put(record);
                source.delete(key);
            }
        };

        transaction.oncomplete = function(){ resolve(); };
        transaction.onerror = function(){ reject(transaction.error); };
    });
}

/** 由 IndexedDB 重新載入所有資料到記憶體 */
async function reloadAll(){
    const result = await Promise.all([
        dbGetAll(STORE_STUDENT),
        dbGetAll(STORE_TRASH),
        dbGetAll(STORE_CLASS)
    ]);
    students = result[0];
    trashed = result[1];
    classes = result[2];
}

/* ==========================================================
   二、共用小工具
   ========================================================== */

/** 產生唯一識別碼 */
function makeUid(){
    return "u" + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
}

/** 依現有資料產生下一個學號 */
function nextStudentId(){
    let max = 20230000;
    students.concat(trashed).forEach(function(student){
        const value = parseInt(String(student.student_id).replace(/\D/g, ""), 10);
        if(!isNaN(value) && value > max){ max = value; }
    });
    return String(max + 1);
}

/** HTML 逸出，避免資料內容破壞版面 */
function escapeHtml(text){
    return String(text === undefined || text === null ? "" : text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

/** 正規表示式逸出 */
function escapeRegExp(text){
    return String(text).replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}

/** 將文字中符合關鍵字的部分以 <mark> 高亮 */
function highlight(text, keyword){
    const safe = escapeHtml(text);
    if(!keyword){ return safe; }
    const pattern = new RegExp(escapeRegExp(escapeHtml(keyword)), "gi");
    return safe.replace(pattern, function(matched){ return "<mark>" + matched + "</mark>"; });
}

/** 取得學生全名 */
function fullNameOf(student){
    return (student.last_name || "") + (student.first_name || "");
}

/** 顯示浮動提示訊息 */
let toastTimer = null;
function showToast(text){
    let toast = document.getElementById("toast");
    if(!toast){
        toast = document.createElement("div");
        toast.id = "toast";
        document.body.appendChild(toast);
    }
    toast.textContent = text;
    toast.classList.add("show");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function(){ toast.classList.remove("show"); }, 2200);
}

/* ==========================================================
   三、大頭貼處理
   ========================================================== */

/** 建立預設大頭貼（120x120 JPEG base64） */
function buildDefaultAvatar(){
    const canvas = document.createElement("canvas");
    canvas.width = AVATAR_SIZE;
    canvas.height = AVATAR_SIZE;
    const ctx = canvas.getContext("2d");

    ctx.fillStyle = "#dfe4ea";
    ctx.fillRect(0, 0, AVATAR_SIZE, AVATAR_SIZE);

    ctx.fillStyle = "#9aa5b1";
    // 頭部
    ctx.beginPath();
    ctx.arc(AVATAR_SIZE / 2, 46, 22, 0, Math.PI * 2);
    ctx.fill();
    // 身體
    ctx.beginPath();
    ctx.arc(AVATAR_SIZE / 2, 122, 38, Math.PI, Math.PI * 2);
    ctx.fill();

    return canvas.toDataURL("image/jpeg", 0.9);
}

/**
 * 將使用者選取的圖檔壓縮為 120x120 的 JPEG base64
 * 以「置中裁切」方式維持比例
 */
function compressAvatar(file){
    return new Promise(function(resolve, reject){
        const reader = new FileReader();

        reader.onload = function(){
            const image = new Image();

            image.onload = function(){
                const canvas = document.createElement("canvas");
                canvas.width = AVATAR_SIZE;
                canvas.height = AVATAR_SIZE;
                const ctx = canvas.getContext("2d");

                ctx.fillStyle = "#ffffff";
                ctx.fillRect(0, 0, AVATAR_SIZE, AVATAR_SIZE);

                const side = Math.min(image.width, image.height);
                const sx = (image.width - side) / 2;
                const sy = (image.height - side) / 2;
                ctx.drawImage(image, sx, sy, side, side, 0, 0, AVATAR_SIZE, AVATAR_SIZE);

                resolve(canvas.toDataURL("image/jpeg", 0.85));
            };

            image.onerror = function(){ reject(new Error("圖片讀取失敗")); };
            image.src = reader.result;
        };

        reader.onerror = function(){ reject(new Error("檔案讀取失敗")); };
        reader.readAsDataURL(file);
    });
}

/* ==========================================================
   四、側邊欄
   ========================================================== */

/** 計算指定班級目前的學生人數 */
function countOfClass(name){
    return students.filter(function(student){ return student.class === name; }).length;
}

/** 重新繪製側邊欄的班級清單與各項數量 */
function renderAside(){
    // 所有學生數量
    const allNum = el.studentList.querySelector(".num");
    if(allNum){ allNum.textContent = String(students.length); }

    // 垃圾桶數量
    const trashNum = el.trashList.querySelector(".num");
    if(trashNum){ trashNum.textContent = String(trashed.length); }

    // 班級清單：先移除舊的班級項目（保留「建立班級」）
    Array.prototype.slice.call(el.classList.querySelectorAll("li.item")).forEach(function(item){
        if(item.id !== "addClass"){ item.remove(); }
    });

    classes.slice().sort(function(a, b){
        return a.name.localeCompare(b.name, "zh-Hant");
    }).forEach(function(item){
        const li = document.createElement("li");
        li.className = "item";
        li.dataset.view = "class";
        li.dataset.name = item.name;
        li.innerHTML = ICONS.folder +
            '<span class="label">' + escapeHtml(item.name) + "</span>" +
            '<span class="num">' + countOfClass(item.name) + "</span>";
        el.classList.insertBefore(li, el.addClass);
    });

    highlightCurrentItem();
}

/** 依目前檢視狀態，於側邊欄高亮對應的清單項目 */
function highlightCurrentItem(){
    document.querySelectorAll("#aside li.item").forEach(function(item){
        item.classList.remove("current");
    });

    let target = null;
    if(viewState.type === "all"){
        target = el.studentList.querySelector('li.item[data-view="all"]');
    }else if(viewState.type === "trash"){
        target = el.trashList.querySelector('li.item[data-view="trash"]');
    }else if(viewState.type === "class"){
        target = el.classList.querySelector('li.item[data-name="' + CSS.escape(viewState.className) + '"]');
    }

    if(target){ target.classList.add("current"); }
}

/* ==========================================================
   五、學生清單（虛擬捲動 + 節點重複使用）
   ========================================================== */

/** 判斷單一學生是否符合搜尋關鍵字 */
function matchKeyword(student, keyword){
    const lower = keyword.toLowerCase();
    const candidates = [
        student.last_name || "",                          // 姓氏
        student.first_name || "",                         // 名字
        fullNameOf(student),                              // 姓氏 + 名字
        student.student_id || "",                         // 學號
        student.address || ""                             // 地址
    ];

    (student.emails || []).forEach(function(mail){ candidates.push(mail); });   // 電子郵件
    (student.phones || []).forEach(function(tel){ candidates.push(tel); });     // 電話

    return candidates.some(function(value){
        return String(value).toLowerCase().indexOf(lower) !== -1;
    });
}

/** 依目前檢視狀態組出要呈現的學生清單 */
function buildVisible(){
    let source;
    if(viewState.type === "trash"){
        source = trashed.slice();
    }else if(viewState.type === "class"){
        source = students.filter(function(student){ return student.class === viewState.className; });
    }else{
        source = students.slice();
    }

    if(viewState.keyword){
        source = source.filter(function(student){ return matchKeyword(student, viewState.keyword); });
    }

    const field = viewState.sortField;
    const direction = viewState.sortOrder === "desc" ? -1 : 1;

    source.sort(function(a, b){
        let left, right;
        if(field === "student_id"){
            left = String(a.student_id || "");
            right = String(b.student_id || "");
        }else if(field === "email"){
            left = (a.emails && a.emails[0]) || "";
            right = (b.emails && b.emails[0]) || "";
        }else{
            left = fullNameOf(a);
            right = fullNameOf(b);
        }
        return left.localeCompare(right, "zh-Hant", { numeric:true }) * direction;
    });

    visible = source;
}

/** 建立一個可重複使用的 .student 節點 */
function createStudentNode(){
    const node = document.createElement("div");
    node.className = "student";
    node.innerHTML =
        '<img class="avatar" alt="大頭貼">' +
        '<div class="col col-name">' +
            '<span class="fullname"></span>' +
            '<span class="student_id"></span>' +
        "</div>" +
        '<div class="col col-email"><span class="email"></span></div>' +
        '<div class="col col-phone"><span class="phone"></span></div>' +
        '<div class="col col-address"><span class="address"></span></div>' +
        '<div class="col col-class"><span class="class"></span></div>' +
        '<div class="actions"></div>';

    // 事件委派到節點本身，重複使用時不需重新綁定
    node.addEventListener("click", function(event){
        const button = event.target.closest("button");
        if(!button){ return; }
        const student = node._student;
        if(!student){ return; }

        if(button.classList.contains("edit")){
            openStudentDialog(student);
        }else if(button.classList.contains("delete")){
            moveToTrash(student.id);
        }else if(button.classList.contains("restore")){
            restoreFromTrash(student.id);
        }else if(button.classList.contains("purge")){
            purgeFromTrash(student.id);
        }
    });

    return node;
}

/** 將學生資料填入節點（節點重複使用的核心） */
function bindStudentNode(node, student, index){
    node._student = student;
    node.dataset.id = student.id;
    node.style.transform = "translateY(" + (index * ROW_HEIGHT) + "px)";

    const keyword = viewState.keyword;
    const avatar = node.querySelector("img.avatar");
    const wanted = student.avatar || defaultAvatar;
    if(avatar.getAttribute("src") !== wanted){ avatar.src = wanted; }

    node.querySelector(".fullname").innerHTML = highlight(fullNameOf(student), keyword);
    node.querySelector(".student_id").innerHTML = highlight(student.student_id || "", keyword);
    // 有多組電子郵件或電話時只呈現第一組
    node.querySelector(".email").innerHTML = highlight((student.emails && student.emails[0]) || "", keyword);
    node.querySelector(".phone").innerHTML = highlight((student.phones && student.phones[0]) || "", keyword);
    node.querySelector(".address").innerHTML = highlight(student.address || "", keyword);
    node.querySelector(".class").innerHTML = highlight(student.class || "", keyword);

    // 功能按鈕區：垃圾桶檢視提供還原與永久刪除
    const actions = node.querySelector(".actions");
    const isTrashView = viewState.type === "trash";
    const wantedMode = isTrashView ? "trash" : "normal";
    if(actions.dataset.mode !== wantedMode){
        actions.dataset.mode = wantedMode;
        actions.innerHTML = isTrashView
            ? '<button type="button" class="restore" title="還原">' + ICONS.restore + "</button>" +
              '<button type="button" class="delete purge" title="永久刪除">' + ICONS.purge + "</button>"
            : '<button type="button" class="edit" title="編輯">' + ICONS.edit + "</button>" +
              '<button type="button" class="delete" title="刪除">' + ICONS.delete + "</button>";
    }
}

/** 依捲動位置，重新指派畫面上的 .student 節點 */
function syncPool(){
    const total = visible.length;
    const viewHeight = el.scroller.clientHeight || 1;
    const scrollTop = el.scroller.scrollTop;

    // 需要的節點數量：可視範圍 + 前後緩衝，且不超過 20 個
    let need = Math.min(POOL_MAX, total, Math.ceil(viewHeight / ROW_HEIGHT) + 2);
    if(need < 0){ need = 0; }

    // 調整節點池大小（重複使用既有節點，不足才新增）
    while(el.pool.children.length > need){
        el.pool.removeChild(el.pool.lastElementChild);
    }
    while(el.pool.children.length < need){
        el.pool.appendChild(createStudentNode());
    }

    // 計算起始索引，確保尾端也能填滿
    let start = Math.floor(scrollTop / ROW_HEIGHT) - 1;
    if(start > total - need){ start = total - need; }
    if(start < 0){ start = 0; }

    for(let i = 0; i < need; i = i + 1){
        bindStudentNode(el.pool.children[i], visible[start + i], start + i);
    }
}

/** 重新繪製主內容區 */
function renderMain(){
    buildVisible();

    // 標題
    let title = "所有學生";
    if(viewState.type === "trash"){ title = "垃圾桶"; }
    if(viewState.type === "class"){ title = "班級：" + viewState.className; }
    if(viewState.keyword){ title = title + "（搜尋：" + viewState.keyword + "）"; }
    el.viewTitle.textContent = title;

    // 捲動總高度，依學生數量呈現捲軸
    el.phantom.style.height = (visible.length * ROW_HEIGHT) + "px";
    if(el.scroller.scrollTop > visible.length * ROW_HEIGHT){ el.scroller.scrollTop = 0; }

    // 提示訊息
    if(visible.length === 0){
        el.message.classList.remove("hidden");
        if(viewState.keyword){
            el.message.textContent = "在你的學生中找不到相符的搜尋結果";
        }else if(viewState.type === "trash"){
            el.message.textContent = "垃圾桶是空的";
        }else{
            el.message.textContent = "目前還沒有任何學生";
        }
    }else{
        el.message.classList.add("hidden");
    }

    syncPool();
}

/** 整體重新繪製 */
function renderAll(){
    renderAside();
    renderMain();
}

/* ==========================================================
   六、對話框（顯示前完全不存在於 document 中）
   ========================================================== */

/** 移除目前的對話框（自 document 完全移除） */
function closeDialog(){
    const dialog = document.getElementById("dialog");
    if(dialog){ dialog.remove(); }
}

/**
 * 建立對話框外框
 * @param {string} panelClass 內層面板的額外 class
 * @returns {HTMLElement} dialog 節點（尚未加入 document）
 */
function createDialog(panelClass){
    closeDialog();
    const dialog = document.createElement("div");
    dialog.id = "dialog";
    const panel = document.createElement("div");
    panel.className = "panel" + (panelClass ? " " + panelClass : "");
    dialog.appendChild(panel);

    // 點擊遮罩或按 Esc 皆可關閉
    dialog.addEventListener("mousedown", function(event){
        if(event.target === dialog){ closeDialog(); }
    });

    return dialog;
}

/* ---------- 建立班級對話框 ---------- */
function openClassDialog(){
    const dialog = createDialog("small");
    const panel = dialog.querySelector(".panel");

    panel.innerHTML =
        '<h2 class="title">建立班級</h2>' +
        '<form class="newClass" novalidate>' +
            '<div class="form-body">' +
                '<div class="field">' +
                    '<label for="className">班級名稱 <span class="req">*</span></label>' +
                    '<input type="text" id="className" name="name" placeholder="例如：三年甲班" autocomplete="off">' +
                    '<p class="error" data-for="name"></p>' +
                "</div>" +
            "</div>" +
            '<div class="form-actions">' +
                '<button type="button" class="close">取消</button>' +
                '<button type="submit" class="submit">儲存</button>' +
            "</div>" +
        "</form>";

    const form = panel.querySelector("form.newClass");
    const nameInput = form.querySelector('input[name="name"]');
    const errorBox = form.querySelector('.error[data-for="name"]');

    panel.querySelector("button.close").addEventListener("click", function(){
        closeDialog();
    });

    form.addEventListener("submit", async function(event){
        event.preventDefault();

        const name = nameInput.value.trim();
        // 班級名稱必須填寫文字後才可以儲存
        if(name === ""){
            errorBox.textContent = "請輸入班級名稱";
            errorBox.classList.add("show");
            nameInput.classList.add("invalid");
            nameInput.focus();
            return;
        }
        if(classes.some(function(item){ return item.name === name; })){
            errorBox.textContent = "此班級已存在";
            errorBox.classList.add("show");
            nameInput.classList.add("invalid");
            return;
        }

        await dbPut(STORE_CLASS, { name:name });
        await reloadAll();
        closeDialog();
        renderAll();
        showToast("已建立班級：" + name);
    });

    nameInput.addEventListener("input", function(){
        errorBox.classList.remove("show");
        nameInput.classList.remove("invalid");
    });

    document.body.appendChild(dialog);
    nameInput.focus();
}

/* ---------- 建立／編輯學生對話框 ---------- */

/** 建立一列可移除的多值欄位（電子郵件或電話） */
function createMultiRow(type, value){
    const row = document.createElement("div");
    row.className = "multi-row";

    const input = document.createElement("input");
    if(type === "email"){
        input.type = "email";
        input.name = "email[]";
        input.placeholder = "example@mail.com";
    }else{
        input.type = "tel";
        input.name = "phone[]";
        input.placeholder = "0912345678";
    }
    input.value = value || "";

    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.className = "icon-btn remove";
    removeBtn.title = "移除這一組";
    removeBtn.innerHTML = ICONS.minus;
    removeBtn.addEventListener("click", function(){
        const list = row.parentElement;
        row.remove();
        // 至少保留一組空白欄位
        if(list.querySelectorAll(".multi-row").length === 0){
            list.appendChild(createMultiRow(type, ""));
        }
    });

    row.appendChild(input);
    row.appendChild(removeBtn);
    return row;
}

/**
 * 開啟建立／編輯學生對話框
 * @param {object|null} origin 傳入學生資料表示編輯，未傳入表示新增
 */
function openStudentDialog(origin){
    const isEdit = !!origin;
    const dialog = createDialog("");
    const panel = dialog.querySelector(".panel");

    const classOptions = ['<option value="">未指定班級</option>'].concat(
        classes.map(function(item){
            const selected = isEdit && origin.class === item.name ? " selected" : "";
            return '<option value="' + escapeHtml(item.name) + '"' + selected + ">" + escapeHtml(item.name) + "</option>";
        })
    ).join("");

    panel.innerHTML =
        '<h2 class="title">' + (isEdit ? "編輯學生" : "建立學生") + "</h2>" +
        '<form class="newStudent" novalidate>' +
            '<div class="form-body">' +
                '<div class="avatar-block">' +
                    '<img class="avatar_preview" alt="大頭貼預覽">' +
                    "<div>" +
                        '<label for="avatarFile">大頭貼</label>' +
                        '<input type="file" id="avatarFile" class="avatar" accept=".jpg,.jpeg,.png,image/jpeg,image/png">' +
                        '<p class="hint">僅可選取 .jpg / .jpeg / .png，儲存時會壓縮為 120x120 的 JPEG</p>' +
                    "</div>" +
                "</div>" +

                '<div class="row2">' +
                    '<div class="field">' +
                        "<label>姓氏</label>" +
                        '<input type="text" name="last_name" placeholder="姓氏" autocomplete="off">' +
                    "</div>" +
                    '<div class="field">' +
                        '<label>名字 <span class="req">*</span></label>' +
                        '<input type="text" name="first_name" placeholder="名字" autocomplete="off">' +
                        '<p class="error" data-for="first_name"></p>' +
                    "</div>" +
                "</div>" +

                '<div class="field">' +
                    "<label>學號</label>" +
                    '<input type="text" name="student_id" placeholder="留白將自動產生" autocomplete="off">' +
                "</div>" +

                '<div class="field">' +
                    "<label>電子郵件</label>" +
                    '<div class="email-list"></div>' +
                    '<button type="button" class="add-more add-email">' + ICONS.plus + "新增電子郵件</button>" +
                    '<p class="error" data-for="email"></p>' +
                "</div>" +

                '<div class="field">' +
                    "<label>電話</label>" +
                    '<div class="phone-list"></div>' +
                    '<button type="button" class="add-more add-phone">' + ICONS.plus + "新增電話</button>" +
                "</div>" +

                '<div class="field">' +
                    "<label>地址</label>" +
                    '<input type="text" name="address" placeholder="地址" autocomplete="off">' +
                "</div>" +

                '<div class="field">' +
                    "<label>學生班級</label>" +
                    '<select name="class">' + classOptions + "</select>" +
                "</div>" +

                '<div class="field">' +
                    "<label>備註</label>" +
                    '<textarea name="note" placeholder="備註"></textarea>' +
                "</div>" +
            "</div>" +
            '<div class="form-actions">' +
                '<button type="button" class="close">取消</button>' +
                '<button type="submit" class="submit">儲存</button>' +
            "</div>" +
        "</form>";

    const form = panel.querySelector("form.newStudent");
    const preview = form.querySelector("img.avatar_preview");
    const fileInput = form.querySelector("input.avatar");
    const emailList = form.querySelector(".email-list");
    const phoneList = form.querySelector(".phone-list");
    const emailError = form.querySelector('.error[data-for="email"]');
    const nameError = form.querySelector('.error[data-for="first_name"]');

    // 編輯時自動帶入現有資料；新增時維持預設空白表單
    preview.src = isEdit ? (origin.avatar || defaultAvatar) : defaultAvatar;
    form.last_name.value = isEdit ? (origin.last_name || "") : "";
    form.first_name.value = isEdit ? (origin.first_name || "") : "";
    form.student_id.value = isEdit ? (origin.student_id || "") : "";
    form.address.value = isEdit ? (origin.address || "") : "";
    form.note.value = isEdit ? (origin.note || "") : "";

    const initialEmails = isEdit && origin.emails && origin.emails.length ? origin.emails : [""];
    initialEmails.forEach(function(value){ emailList.appendChild(createMultiRow("email", value)); });

    const initialPhones = isEdit && origin.phones && origin.phones.length ? origin.phones : [""];
    initialPhones.forEach(function(value){ phoneList.appendChild(createMultiRow("phone", value)); });

    // 動態新增多組電子郵件／電話
    form.querySelector(".add-email").addEventListener("click", function(){
        emailList.appendChild(createMultiRow("email", ""));
    });
    form.querySelector(".add-phone").addEventListener("click", function(){
        phoneList.appendChild(createMultiRow("phone", ""));
    });

    // 選取圖檔後立即預覽（同時完成 120x120 JPEG 壓縮）
    let pickedAvatar = null;
    fileInput.addEventListener("change", async function(){
        const file = fileInput.files && fileInput.files[0];
        if(!file){ return; }
        try{
            pickedAvatar = await compressAvatar(file);
            preview.src = pickedAvatar;
        }catch(error){
            showToast("圖片讀取失敗，請重新選取");
        }
    });

    // 取消：關閉對話框且不變動現有資料
    panel.querySelector("button.close").addEventListener("click", function(){
        closeDialog();
    });

    form.addEventListener("submit", async function(event){
        event.preventDefault();

        nameError.classList.remove("show");
        emailError.classList.remove("show");
        form.first_name.classList.remove("invalid");

        // 檢查一：名字為必填
        if(form.first_name.value.trim() === ""){
            nameError.textContent = "名字為必填欄位";
            nameError.classList.add("show");
            form.first_name.classList.add("invalid");
            form.first_name.focus();
            return;
        }

        // 檢查二：電子郵件必須通過瀏覽器內建規則檢查（空白欄位忽略）
        const emailInputs = Array.prototype.slice.call(emailList.querySelectorAll('input[name="email[]"]'));
        let badEmail = null;
        emailInputs.forEach(function(input){
            input.classList.remove("invalid");
            if(input.value.trim() !== "" && !input.checkValidity()){
                input.classList.add("invalid");
                if(!badEmail){ badEmail = input; }
            }
        });
        if(badEmail){
            emailError.textContent = "電子郵件格式不正確：" + badEmail.validationMessage;
            emailError.classList.add("show");
            badEmail.focus();
            return;
        }

        // 過濾空白的電子郵件與電話
        const emails = emailInputs
            .map(function(input){ return input.value.trim(); })
            .filter(function(value){ return value !== ""; });
        const phones = Array.prototype.slice.call(phoneList.querySelectorAll('input[name="phone[]"]'))
            .map(function(input){ return input.value.trim(); })
            .filter(function(value){ return value !== ""; });

        // 大頭貼：新增時未選取則用預設圖片，編輯時未選取則沿用當前圖片
        let avatar;
        if(pickedAvatar){
            avatar = pickedAvatar;
        }else if(isEdit){
            avatar = origin.avatar || defaultAvatar;
        }else{
            avatar = defaultAvatar;
        }

        const record = {
            id: isEdit ? origin.id : makeUid(),
            student_id: form.student_id.value.trim() || (isEdit ? origin.student_id : nextStudentId()),
            last_name: form.last_name.value.trim(),
            first_name: form.first_name.value.trim(),
            emails: emails,
            phones: phones,
            address: form.address.value.trim(),
            class: form.class.value,
            note: form.note.value,
            avatar: avatar,
            created: isEdit ? (origin.created || Date.now()) : Date.now()
        };

        await dbPut(isEdit && viewState.type === "trash" ? STORE_TRASH : STORE_STUDENT, record);
        await reloadAll();
        closeDialog();
        renderAll();
        showToast(isEdit ? "已更新學生資料" : "已新增學生");
    });

    document.body.appendChild(dialog);
    form.last_name.focus();
}

/* ==========================================================
   七、刪除／還原
   ========================================================== */

/** 刪除學生：移動至垃圾桶 */
async function moveToTrash(id){
    await dbMove(STORE_STUDENT, STORE_TRASH, id);
    await reloadAll();
    renderAll();
    showToast("已移至垃圾桶");
}

/** 由垃圾桶還原學生 */
async function restoreFromTrash(id){
    await dbMove(STORE_TRASH, STORE_STUDENT, id);
    await reloadAll();
    renderAll();
    showToast("已還原學生");
}

/** 由垃圾桶永久刪除 */
async function purgeFromTrash(id){
    await dbDelete(STORE_TRASH, id);
    await reloadAll();
    renderAll();
    showToast("已永久刪除");
}

/* ==========================================================
   八、匯出／匯入 CSV
   ========================================================== */

const CSV_HEADER = ["type", "id", "student_id", "last_name", "first_name", "emails", "phones", "address", "class", "note", "avatar", "deleted"];

/** 將單一欄位值轉為 CSV 欄位（一律加上雙引號） */
function csvCell(value){
    return '"' + String(value === undefined || value === null ? "" : value).replace(/"/g, '""') + '"';
}

/** 將一筆學生資料轉成 CSV 列 */
function studentToRow(student, deleted){
    return [
        "student",
        student.id,
        student.student_id || "",
        student.last_name || "",
        student.first_name || "",
        (student.emails || []).join("|"),
        (student.phones || []).join("|"),
        student.address || "",
        student.class || "",
        student.note || "",
        student.avatar || "",
        deleted ? "1" : "0"
    ].map(csvCell).join(",");
}

/** 匯出所有學生資料與班級資料為 csv 檔案 */
function exportData(){
    const lines = [CSV_HEADER.map(csvCell).join(",")];

    classes.forEach(function(item){
        lines.push(["class", "", "", "", "", "", "", "", item.name, "", "", "0"].map(csvCell).join(","));
    });
    students.forEach(function(student){ lines.push(studentToRow(student, false)); });
    trashed.forEach(function(student){ lines.push(studentToRow(student, true)); });

    // 加上 BOM，讓 Excel 正確辨識 UTF-8
    const blob = new Blob(["﻿" + lines.join("\r\n")], { type:"text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = "students_" + new Date().toISOString().slice(0, 10) + ".csv";
    document.body.appendChild(link);
    link.click();
    link.remove();
    setTimeout(function(){ URL.revokeObjectURL(url); }, 1000);
    showToast("已匯出 " + students.length + " 位學生與 " + classes.length + " 個班級");
}

/** 解析 CSV 文字為二維陣列（支援雙引號與換行） */
function parseCsv(text){
    const rows = [];
    let row = [];
    let cell = "";
    let inQuote = false;

    text = text.replace(/^﻿/, "").replace(/\r\n/g, "\n").replace(/\r/g, "\n");

    for(let i = 0; i < text.length; i = i + 1){
        const char = text[i];

        if(inQuote){
            if(char === '"'){
                if(text[i + 1] === '"'){ cell = cell + '"'; i = i + 1; }
                else{ inQuote = false; }
            }else{
                cell = cell + char;
            }
            continue;
        }

        if(char === '"'){ inQuote = true; }
        else if(char === ","){ row.push(cell); cell = ""; }
        else if(char === "\n"){ row.push(cell); rows.push(row); row = []; cell = ""; }
        else{ cell = cell + char; }
    }

    if(cell !== "" || row.length > 0){ row.push(cell); rows.push(row); }
    return rows.filter(function(item){ return item.length > 1 || (item[0] || "").trim() !== ""; });
}

/** 匯入 csv 檔案，將學生與班級資料寫入 IndexedDB */
async function importData(file){
    const text = await file.text();
    const rows = parseCsv(text);
    if(rows.length === 0){
        showToast("檔案內容為空");
        return;
    }

    // 以標頭決定欄位位置，找不到標頭時採預設順序
    let header = rows[0].map(function(name){ return String(name).trim().toLowerCase(); });
    let startIndex = 1;
    if(header.indexOf("type") === -1){
        header = CSV_HEADER.slice();
        startIndex = 0;
    }

    const at = function(row, key){
        const index = header.indexOf(key);
        return index === -1 ? "" : (row[index] || "").trim();
    };

    let classCount = 0;
    let studentCount = 0;

    for(let i = startIndex; i < rows.length; i = i + 1){
        const row = rows[i];
        const type = at(row, "type") || "student";

        if(type === "class"){
            const name = at(row, "class");
            if(name !== ""){
                await dbPut(STORE_CLASS, { name:name });
                classCount = classCount + 1;
            }
            continue;
        }

        const firstName = at(row, "first_name");
        const lastName = at(row, "last_name");
        if(firstName === "" && lastName === ""){ continue; }

        const className = at(row, "class");
        if(className !== ""){
            await dbPut(STORE_CLASS, { name:className });
        }

        const record = {
            id: at(row, "id") || makeUid(),
            student_id: at(row, "student_id"),
            last_name: lastName,
            first_name: firstName,
            emails: at(row, "emails").split("|").filter(function(value){ return value !== ""; }),
            phones: at(row, "phones").split("|").filter(function(value){ return value !== ""; }),
            address: at(row, "address"),
            class: className,
            note: at(row, "note"),
            avatar: at(row, "avatar") || defaultAvatar,
            created: Date.now()
        };

        await dbPut(at(row, "deleted") === "1" ? STORE_TRASH : STORE_STUDENT, record);
        studentCount = studentCount + 1;
    }

    await reloadAll();
    // 補上學號缺漏的資料
    renderAll();
    showToast("已匯入 " + studentCount + " 位學生與 " + classCount + " 個班級");
}

/* ==========================================================
   九、事件綁定
   ========================================================== */

function bindEvents(){
    // 新增學生
    el.addStudent.addEventListener("click", function(){
        openStudentDialog(null);
    });

    // 側邊欄清單項目（含動態產生的班級項目）
    el.aside.addEventListener("click", function(event){
        const item = event.target.closest("li.item");
        if(!item){ return; }

        if(item.id === "addClass"){
            openClassDialog();
            return;
        }

        viewState.keyword = "";
        el.searchInput.value = "";
        el.searchClear.classList.remove("show");

        if(item.dataset.view === "trash"){
            viewState.type = "trash";
            viewState.className = "";
        }else if(item.dataset.view === "class"){
            viewState.type = "class";
            viewState.className = item.dataset.name;
        }else{
            viewState.type = "all";
            viewState.className = "";
        }

        el.scroller.scrollTop = 0;
        highlightCurrentItem();
        renderMain();
    });

    // 搜尋：輸入文字後按下 Enter
    el.searchForm.addEventListener("submit", function(event){
        event.preventDefault();
        viewState.keyword = el.searchInput.value.trim();
        el.searchClear.classList.toggle("show", viewState.keyword !== "");
        el.scroller.scrollTop = 0;
        renderMain();
    });

    el.searchClear.addEventListener("click", function(){
        el.searchInput.value = "";
        viewState.keyword = "";
        el.searchClear.classList.remove("show");
        el.scroller.scrollTop = 0;
        renderMain();
        el.searchInput.focus();
    });

    // 排序欄位與升冪／降冪
    el.sortField.addEventListener("change", function(){
        viewState.sortField = el.sortField.value;
        el.scroller.scrollTop = 0;
        renderMain();
    });

    el.sortOrder.addEventListener("click", function(){
        viewState.sortOrder = viewState.sortOrder === "asc" ? "desc" : "asc";
        el.sortOrder.dataset.order = viewState.sortOrder;
        el.sortOrder.querySelector(".order-text").textContent = viewState.sortOrder === "asc" ? "升冪" : "降冪";
        el.scroller.scrollTop = 0;
        renderMain();
    });

    // 虛擬捲動
    el.scroller.addEventListener("scroll", function(){
        syncPool();
    });

    window.addEventListener("resize", function(){
        syncPool();
    });

    // 匯出與匯入
    el.exportBtn.addEventListener("click", function(){
        exportData();
    });

    el.importBtn.addEventListener("click", function(){
        el.importFile.value = "";
        el.importFile.click();
    });

    el.importFile.addEventListener("change", async function(){
        const file = el.importFile.files && el.importFile.files[0];
        if(!file){ return; }
        try{
            await importData(file);
        }catch(error){
            showToast("匯入失敗，請確認檔案格式");
        }
    });

    // Esc 關閉對話框
    document.addEventListener("keydown", function(event){
        if(event.key === "Escape"){ closeDialog(); }
    });
}

/* ==========================================================
   十、啟動
   ========================================================== */

async function init(){
    defaultAvatar = buildDefaultAvatar();
    db = await openDB();
    await reloadAll();
    bindEvents();
    renderAll();
}

init().catch(function(error){
    console.error(error);
});

/**
 * dialog.js — 對話框
 * 每次開啟都重新建立 #dialog 內容，因此不會保留上一次輸入的資料與錯誤訊息。
 * 只負責產生畫面與收集使用者輸入，資料存取交由 App.Store 處理。
 */
window.App = window.App || {};

App.Dialog = (function () {
    'use strict';

    var dialog = null;
    var backdrop = null;
    var closeHandler = null;

    function ready() {
        if (!dialog) {
            dialog = document.getElementById('dialog');
            backdrop = document.getElementById('backdrop');
            backdrop.addEventListener('click', close);
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    close();
                }
            });
        }
    }

    /** 顯示對話框 */
    function open(className) {
        ready();
        dialog.className = className || '';
        dialog.hidden = false;
        backdrop.hidden = false;
    }

    /** 關閉對話框並清空內容 */
    function close() {
        ready();
        dialog.hidden = true;
        backdrop.hidden = true;
        dialog.textContent = '';
        if (closeHandler) {
            var handler = closeHandler;
            closeHandler = null;
            handler();
        }
    }

    /** 產生標題 */
    function buildTitle(text) {
        var title = document.createElement('h2');
        title.className = 'title';
        title.textContent = text;
        return title;
    }

    /** 產生取消 / 儲存按鈕列（兩顆按鈕皆為表單的直接子節點） */
    function appendActions(form, onCancel) {
        var cancel = document.createElement('button');
        cancel.type = 'button';
        cancel.className = 'cancel';
        cancel.textContent = '取消';
        cancel.addEventListener('click', function () {
            if (onCancel) {
                onCancel();
            }
            close();
        });

        var submit = document.createElement('button');
        submit.type = 'submit';
        submit.className = 'submit';
        submit.textContent = '儲存';

        form.appendChild(cancel);
        form.appendChild(submit);
    }

    // ======================= 建立標籤對話框 =======================

    /**
     * 開啟「建立標籤」對話框
     * @param {Function} onSave 傳入標籤名稱，回傳 Promise
     */
    function openTag(onSave) {
        ready();
        dialog.textContent = '';
        dialog.appendChild(buildTitle('建立標籤'));

        var form = document.createElement('form');
        form.className = 'newTag';
        form.setAttribute('novalidate', 'novalidate');

        var input = document.createElement('input');
        input.type = 'text';
        input.name = 'name';
        input.placeholder = '標籤名稱';
        input.setAttribute('autocomplete', 'off');
        form.appendChild(input);

        appendActions(form);

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var name = input.value.trim();
            if (!name) {
                // 標籤名稱必須填寫文字後才可以儲存
                input.classList.add('invalid');
                input.focus();
                return;
            }
            Promise.resolve(onSave(name)).then(close);
        });

        dialog.appendChild(form);
        open('dialog_tag');
        input.focus();
    }

    // ======================= 聯絡人對話框 =======================

    /** 產生一列可移除的電子郵件輸入框 */
    function buildEmailRow(value) {
        var row = document.createElement('div');
        row.className = 'row row_email';

        var input = document.createElement('input');
        input.type = 'email';
        input.name = 'email[]';
        input.placeholder = '電子郵件';
        input.value = value || '';

        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'del_email icon_btn';
        remove.title = '移除此電子郵件';
        remove.setAttribute('aria-label', '移除此電子郵件');
        remove.addEventListener('click', function () {
            row.parentNode.removeChild(row);
        });

        row.appendChild(input);
        row.appendChild(remove);
        return row;
    }

    /** 產生一列可移除的電話輸入框 */
    function buildPhoneRow(value) {
        var row = document.createElement('div');
        row.className = 'row row_phone';

        var input = document.createElement('input');
        input.type = 'tel';
        input.name = 'phone[]';
        input.placeholder = '電話';
        input.value = value || '';

        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'del_tel icon_btn';
        remove.title = '移除此電話';
        remove.setAttribute('aria-label', '移除此電話');
        remove.addEventListener('click', function () {
            row.parentNode.removeChild(row);
        });

        row.appendChild(input);
        row.appendChild(remove);
        return row;
    }

    /** 產生標籤選取區 */
    function buildTagsField(selected) {
        var field = document.createElement('div');
        field.className = 'field';
        field.innerHTML = '<span class="field_icon icon_tag"></span>';

        var box = document.createElement('div');
        box.className = 'tags';

        var tags = App.Store.getTags();
        if (tags.length === 0) {
            box.innerHTML = '<span class="tags_empty">尚未建立任何標籤</span>';
        }
        tags.forEach(function (tag) {
            var label = document.createElement('label');
            label.className = 'tag';

            var checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'tags[]';
            checkbox.value = tag.id;
            checkbox.checked = selected.indexOf(tag.id) !== -1;

            var text = document.createElement('span');
            text.className = 'tag_text';
            text.textContent = tag.name;

            label.appendChild(checkbox);
            label.appendChild(text);
            box.appendChild(label);
        });

        field.appendChild(box);
        return field;
    }

    /**
     * 開啟「建立 / 編輯聯絡人」對話框
     * @param {Object|null} contact 編輯時傳入既有聯絡人，新增時為 null
     * @param {Function} onSave 傳入整理好的資料物件，回傳 Promise
     */
    function openContact(contact, onSave) {
        ready();
        var isEdit = !!contact;
        var data = contact || { emails: [], phones: [], tags: [], note: '', avatar: null };

        dialog.textContent = '';
        dialog.appendChild(buildTitle(isEdit ? '編輯聯絡人' : '建立新聯絡人'));

        var form = document.createElement('form');
        form.className = isEdit ? 'editContact' : 'newContact';
        form.setAttribute('novalidate', 'novalidate');

        // --- 大頭貼 ---
        var avatarField = document.createElement('div');
        avatarField.className = 'field field_avatar';

        var preview = document.createElement('img');
        preview.className = 'avatar_preview';
        preview.alt = '大頭貼預覽';
        preview.src = data.avatar || App.Avatar.DEFAULT_SRC;

        var fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'avatar';
        fileInput.className = 'avatar';
        fileInput.accept = App.Avatar.ACCEPT.join(',');

        // 以 label 包住預覽圖與檔案選取欄位，點選大頭貼即可開啟檔案選取對話框
        var picker = document.createElement('label');
        picker.className = 'avatar_picker';
        picker.title = '選擇大頭貼';
        picker.appendChild(preview);
        picker.appendChild(fileInput);
        picker.insertAdjacentHTML('beforeend', '<span class="avatar_badge"></span>');

        var fileLabel = document.createElement('span');
        fileLabel.className = 'avatar_hint';
        fileLabel.textContent = '點選頭像選擇大頭貼（JPEG / PNG，將自動壓縮為 120 × 120）';

        avatarField.appendChild(picker);
        avatarField.appendChild(fileLabel);
        form.appendChild(avatarField);

        // 選取的大頭貼壓縮結果（未選取時維持 null，代表沿用原圖 / 使用預設圖片）
        var pendingAvatar = null;
        var avatarError = false;

        fileInput.addEventListener('change', function () {
            var file = fileInput.files && fileInput.files[0];
            pendingAvatar = null;
            avatarError = false;
            if (!file) {
                preview.src = data.avatar || App.Avatar.DEFAULT_SRC;
                return;
            }
            if (!App.Avatar.isValid(file)) {
                avatarError = true;
                preview.src = data.avatar || App.Avatar.DEFAULT_SRC;
                renderErrors(['無效的大頭貼']);
                return;
            }
            App.Avatar.compress(file).then(function (dataUrl) {
                pendingAvatar = dataUrl;
                preview.src = dataUrl;
            }).catch(function () {
                avatarError = true;
                renderErrors(['無效的大頭貼']);
            });
        });

        // --- 姓名 ---
        var nameField = document.createElement('div');
        nameField.className = 'field field_name';
        nameField.innerHTML = '<span class="field_icon icon_person"></span>';

        var lastName = document.createElement('input');
        lastName.type = 'text';
        lastName.name = 'last_name';
        lastName.placeholder = '姓氏';
        lastName.value = data.last_name || '';

        var firstName = document.createElement('input');
        firstName.type = 'text';
        firstName.name = 'first_name';
        firstName.placeholder = '名字';
        firstName.value = data.first_name || '';

        nameField.appendChild(lastName);
        nameField.appendChild(firstName);
        form.appendChild(nameField);

        // --- 電子郵件 ---
        var emailField = document.createElement('div');
        emailField.className = 'field field_email';
        emailField.innerHTML = '<span class="field_icon icon_mail"></span>';

        var emailBox = document.createElement('div');
        emailBox.className = 'rows emails';
        var emails = (data.emails || []).slice();
        if (emails.length === 0) {
            emails = [''];   // 預設顯示一組空的輸入框
        }
        emails.forEach(function (email) { emailBox.appendChild(buildEmailRow(email)); });

        var addEmail = document.createElement('button');
        addEmail.type = 'button';
        addEmail.className = 'add_email';
        addEmail.textContent = '新增電子郵件';
        addEmail.addEventListener('click', function () {
            emailBox.appendChild(buildEmailRow(''));
        });

        emailField.appendChild(emailBox);
        emailField.appendChild(addEmail);
        form.appendChild(emailField);

        // --- 電話 ---
        var phoneField = document.createElement('div');
        phoneField.className = 'field field_phone';
        phoneField.innerHTML = '<span class="field_icon icon_phone"></span>';

        var phoneBox = document.createElement('div');
        phoneBox.className = 'rows phones';
        var phones = (data.phones || []).slice();
        if (phones.length === 0) {
            phones = [''];   // 預設顯示一組空的輸入框
        }
        phones.forEach(function (phone) { phoneBox.appendChild(buildPhoneRow(phone)); });

        var addTel = document.createElement('button');
        addTel.type = 'button';
        addTel.className = 'add_tel';
        addTel.textContent = '新增電話';
        addTel.addEventListener('click', function () {
            phoneBox.appendChild(buildPhoneRow(''));
        });

        phoneField.appendChild(phoneBox);
        phoneField.appendChild(addTel);
        form.appendChild(phoneField);

        // --- 標籤 ---
        form.appendChild(buildTagsField((data.tags || []).slice()));

        // --- 備註 ---
        var noteField = document.createElement('div');
        noteField.className = 'field field_note';
        noteField.innerHTML = '<span class="field_icon icon_note"></span>';

        var note = document.createElement('textarea');
        note.name = 'note';
        note.placeholder = '附註';
        note.value = data.note || '';
        noteField.appendChild(note);
        form.appendChild(noteField);

        // --- 錯誤訊息列表（沒有錯誤時整個 .errors 不存在於 DOM） ---
        var errorsBox = null;

        function renderErrors(messages) {
            if (errorsBox && errorsBox.parentNode) {
                errorsBox.parentNode.removeChild(errorsBox);
            }
            errorsBox = null;
            if (!messages || messages.length === 0) {
                return;
            }
            errorsBox = document.createElement('div');
            errorsBox.className = 'errors';
            messages.forEach(function (message) {
                var item = document.createElement('p');
                item.className = 'error';
                item.textContent = message;
                errorsBox.appendChild(item);
            });
            form.insertBefore(errorsBox, form.querySelector('button.cancel'));
        }

        appendActions(form);

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var emailInputs = Array.prototype.slice.call(form.querySelectorAll('input[name="email[]"]'));
            var phoneInputs = Array.prototype.slice.call(form.querySelectorAll('input[name="phone[]"]'));
            var messages = [];

            // 名字為必填
            if (!firstName.value.trim()) {
                messages.push('名字為必填');
            }
            // 電子郵件必須通過瀏覽器內建規則檢查（空值不列入儲存，也不視為錯誤）
            var invalidEmail = emailInputs.some(function (input) {
                return input.value.trim() !== '' && !input.checkValidity();
            });
            if (invalidEmail) {
                messages.push('無效的電子郵件');
            }
            // 大頭貼格式
            if (avatarError) {
                messages.push('無效的大頭貼');
            }

            renderErrors(messages);
            if (messages.length > 0) {
                return;   // 不符合規則時不關閉、也不儲存
            }

            var payload = {
                last_name: lastName.value.trim(),
                first_name: firstName.value.trim(),
                emails: emailInputs.map(function (input) { return input.value.trim(); })
                    .filter(function (value) { return value !== ''; }),
                phones: phoneInputs.map(function (input) { return input.value.trim(); })
                    .filter(function (value) { return value !== ''; }),
                tags: Array.prototype.slice.call(form.querySelectorAll('input[name="tags[]"]'))
                    .filter(function (input) { return input.checked; })
                    .map(function (input) { return Number(input.value); }),
                note: note.value,
                avatar: pendingAvatar   // null 代表新增時用預設圖片、編輯時沿用當前圖片
            };
            Promise.resolve(onSave(payload)).then(close);
        });

        dialog.appendChild(form);
        open(isEdit ? 'dialog_contact' : 'dialog_contact');
        firstName.focus();
    }

    // ======================= 刪除確認對話框 =======================

    /**
     * 開啟「刪除聯絡人」確認對話框
     * @param {Function} onConfirm 使用者再次點選刪除後執行
     */
    function openDelete(onConfirm) {
        ready();
        dialog.textContent = '';

        var message = document.createElement('p');
        message.className = 'confirm_text';
        message.textContent = '要刪除所選的聯絡人嗎？';
        dialog.appendChild(message);

        var actions = document.createElement('div');
        actions.className = 'confirm_actions';

        // 試題規範取消按鈕為 .cancal，另補上 .cancel 以符合一般語意
        var cancel = document.createElement('button');
        cancel.type = 'button';
        cancel.className = 'cancal cancel';
        cancel.textContent = '取消';
        cancel.addEventListener('click', close);

        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'delete';
        remove.textContent = '刪除';
        remove.addEventListener('click', function () {
            Promise.resolve(onConfirm()).then(close);
        });

        actions.appendChild(cancel);
        actions.appendChild(remove);
        dialog.appendChild(actions);
        open('dialog_confirm');
    }

    return {
        openTag: openTag,
        openContact: openContact,
        openDelete: openDelete,
        close: close
    };
})();

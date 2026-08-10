/**
 * C1: Folder Zip - 前端輔助
 * 顯示選到的資料夾名稱與檔案數，並在資料夾為空時擋下送出。
 */

const folderInput = document.getElementById("folder")
const selectedText = document.getElementById("selected")
const submitButton = document.getElementById("submit")

folderInput.addEventListener("change", function () {
    const files = Array.from(folderInput.files)
    if (files.length === 0) {
        selectedText.textContent = "這個資料夾是空的，請重新選擇"
        submitButton.disabled = true
        return
    }

    // webkitRelativePath 形如 test/subfolder/word.docx，第一段就是資料夾名稱
    const rootFolder = (files[0].webkitRelativePath || files[0].name).split("/")[0]
    selectedText.textContent = "已選擇「" + rootFolder + "」，共 " + files.length + " 個檔案 → " + rootFolder + ".zip"
    submitButton.disabled = false
})

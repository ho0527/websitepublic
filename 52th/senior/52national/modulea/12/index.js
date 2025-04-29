// 宣告一個 JSZip 物件
let zip=new JSZip()

// 取得 DOM 元素
let fileinput=document.getElementById("fileinput")
let folderinput=document.getElementById("folderinput")
let filelist=document.getElementById("filelist")
let input=[fileinput,folderinput]

input.forEach(function(event){
    event.addEventListener("change",function(changeevent){
        let files
        if(changeevent.target.id=="fileinput"){
            files=fileinput.files
        }else{
            files=folderinput.files
        }
        for(let i=0;i<files.length;i=i+1){
            let li=document.createElement("li")// 新增一個檔案項目到 filelist
            li.textContent=files[i].name
            li.classList.add("filelistli")
            filelist.appendChild(li)
            zip.file(files[i].name,files[i])// 將檔案加入 JSZip 物件
        }
    })
})

// 監聽下載按鈕被點擊
document.getElementById("downloadbutton").onclick=function(){
    let filename=document.getElementById("filename").value
    if(filename==""){ filename="download" }// 如果檔案名稱為空，預設為 download
    filename=filename+".zip"// 加上 .zip 副檔名
    // 產生壓縮檔
    zip.generateAsync({ type:"blob" }).then(function(downloadevent){
        let alink=document.createElement("a")
        alink.href=URL.createObjectURL(downloadevent)
        alink.download=filename
        alink.click()
    })
}

document.getElementById("reflashbutton").onclick=function(){
    location.reload()
}
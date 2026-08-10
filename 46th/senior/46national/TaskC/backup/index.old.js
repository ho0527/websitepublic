let difficulty="normal" // 預設難易度

// 難易度調整 START
document.getElementById("normal").onclick=function(){
    document.getElementById("normal").classList.add("selectbutton")
    document.getElementById("hard").classList.remove("selectbutton")
    difficulty="normal"
}

document.getElementById("hard").onclick=function(){
    document.getElementById("hard").classList.add("selectbutton")
    document.getElementById("normal").classList.remove("selectbutton")
    difficulty="hard"
}
// 難易度調整 END

// 開始遊戲
document.getElementById("startgame").onclick=function(){
    localStorage.setItem("difficulty",difficulty) // 上傳資料
    // localStorage.setItem("name",document.getElementById("username").value) // 上傳資料
    location.href="main.html" // 導向
}
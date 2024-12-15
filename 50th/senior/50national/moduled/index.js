let difficulty="normal" // 預設難易度

// 難易度調整 START
docgetid("easy").onclick=function(){
    docgetid("easy").classList.add("selectbutton")
    docgetid("normal").classList.remove("selectbutton")
    docgetid("hard").classList.remove("selectbutton")
    difficulty="easy"
}

docgetid("normal").onclick=function(){
    docgetid("normal").classList.add("selectbutton")
    docgetid("easy").classList.remove("selectbutton")
    docgetid("hard").classList.remove("selectbutton")
    difficulty="normal"
}

docgetid("hard").onclick=function(){
    docgetid("hard").classList.add("selectbutton")
    docgetid("easy").classList.remove("selectbutton")
    docgetid("normal").classList.remove("selectbutton")
    difficulty="hard"
}
// 難易度調整 END

// 開始遊戲
docgetid("startgame").onclick=function(){
    weblsset("50nationalmoduleddifficulty",difficulty) // 上傳資料
    weblsset("50nationalmoduledname",docgetid("username").value) // 上傳資料
    location.href="main.html" // 導向
}

// 開始教學
docgetid("starttutorial").onclick=function(){
    weblsset("50nationalmoduleddifficulty","教學模式") // 上傳資料
    weblsset("50nationalmoduledname",docgetid("username").value) // 上傳資料
    location.href="tutorial.html"
}
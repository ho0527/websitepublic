if(weblsget("49regionalgamedata")){
    let data=JSON.parse(weblsget("49regionalgamedata"))

    docgetid("gamename").innerHTML=`
        (${data["name"]} 電競活動報名表單)
    `

    docgetid("main").innerHTML=`
        <input type="text" class="input" placeholder="姓名"><br><br>
        <input type="text" class="input" placeholder="暱稱"><br><br>
        <input type="text" class="input" placeholder="隊伍"><br><br>
        <input type="text" class="input" placeholder="隊員1"><br><br>
        <input type="text" class="input" placeholder="隊員2"><br><br>
        <input type="text" class="input" placeholder="隊員3"><br><br>
        <input type="button" class="button" id="cancle" value="取消">
        <input type="button" class="button" id="submit" value="送出">
    `

    docgetid("cancle").onclick=function(){
        weblsset("49regionalgamedata",null)
        location.href="main.html"
    }

    docgetid("submit").onclick=function(){
        weblsset("49regionalgamedata",null)
        alert("上傳成功")
        location.href="main.html"
    }
}else{
    location.href="main.html"
}
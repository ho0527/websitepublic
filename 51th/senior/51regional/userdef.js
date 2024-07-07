let userkey

function user(key){
    if(key=="true"){
        docgetid("body").innerHTML=`
            <div class="navigationbar">
                <div class="navigationbarleft"><div class="navigationbartitle">網路問卷管理系統</div></div>
                <div class="navigationbarright">
                    id: <input type="text" class="formtext background_none" id="id" style="width: 40px" disabled>
                    標題: <input type="text" class="formtext background_none" id="title" style="width: 100px" disabled>
                    總數: <input type="text" class="formtext background_none" id="count" style="width: 35px" disabled>
                    <input type="button" class="stbutton outline" onclick="location.href='index.php'" value="返回">
                    <input type="button" class="stbutton outline" onclick="save()" value="送出">
                    <input type="button" class="stbutton outline" onclick="location.href='api.php?logout='" value="登出">
                </div>
            </div>
            <div class="progressdiv">
                <input type="button" class="progressbutton" id="prev" value="上一頁">
                <div class="progressbar textcenter">
                    <div class="progress" id="progress"></div>
                    <div class="progresstext" id="progresstext">0/100%</div>
                </div>
                <input type="button" class="progressbutton" id="next" value="下一頁">
            </div>
            <div class="usermain macosmaindiv macossectiondiv" id="maindiv"></div>
        `
    }else{
        docgetid("body").innerHTML=`
            <div class="navigationbar">
                <div class="navigationbarleft"><div class="navigationbartitle">網路問卷管理系統</div></div>
                <div class="navigationbarright">
                    id: <input type="text" class="formtext background_none" id="id" style="width: 40px" disabled>
                    標題: <input type="text" class="formtext background_none" id="title" style="width: 100px" disabled>
                    <input type="button" class="stbutton outline" onclick="location.href='index.php'" value="返回">
                </div>
            </div>
            <div class="warning center">
                ${key}
            </div>
        `
    }
    userkey=key
}
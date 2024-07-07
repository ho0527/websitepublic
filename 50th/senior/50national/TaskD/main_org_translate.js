let username=localStorage.getItem("50nationalmoduledname")
let difficulty=localStorage.getItem("50nationalmoduleddifficulty")
let mainarray=[]
let score=0
let topstarcount=0
let maininnerhtml2=``
let min=0
let sec=0
let click=false
let blockwh=25 // 一格的長寬
let mid=5
let life=3
let canhit=true
let timestop=false
let invincible=false //無敵
let playerspeed=500 // ms
let timer // 計時器
let ghostinterval
let playerinterval
let starcount
let player // [[x,y],目前方塊狀態]
let ghost1=[]
let ghost2=[]
let ghost3=[]
let doorlist=[]

/*
mainarray 內容:
0=牆
1=道路
2=豆子
3=星星
4=只有鬼能走的地方
5=蘋果
6=鳳梨
7=草莓
*/

function rank(data){
    if(data["success"]){
        let row=data["data"]["data"]
        console.log(row)
        lightbox(null,"lightbox",function(){
            let rankinnerhtml=``
            let rankno=1
            for(let i=0;i<row.length;i=i+1){
                if(row[i-1]){
                    if(row[i][3]==row[i-1][3]&&row[i][2]==row[i-1][2]){
                        if(row[i][0]==data["data"]["userid"]){
                            rankinnerhtml=`
                                ${rankinnerhtml}
                                <tr class="highlighttr">
                                    <td class="td">${rankno}</td>
                                    <td class="td">${row[i][1]}</td>
                                    <td class="td">${row[i][3]}</td>
                                    <td class="td">${row[i][2]}</td&&>
                                </tr>
                            `
                        }else{
                            rankinnerhtml=`
                                ${rankinnerhtml}
                                <tr class="tr">
                                    <td class="td">${rankno}</td>
                                    <td class="td">${row[i][1]}</td>
                                    <td class="td">${row[i][3]}</td>
                                    <td class="td">${row[i][2]}</td>
                                </tr>
                            `
                        }
                    }else{
                        if(row[i][0]==data["data"]["userid"]){
                            rankinnerhtml=`
                                ${rankinnerhtml}
                                <tr class="highlighttr">
                                    <td class="td">${rankno}</td>
                                    <td class="td">${row[i][1]}</td>
                                    <td class="td">${row[i][3]}</td>
                                    <td class="td">${row[i][2]}</td>
                                </tr>
                            `
                        }else{
                            rankinnerhtml=`
                                ${rankinnerhtml}
                                <tr class="tr">
                                    <td class="td">${rankno}</td>
                                    <td class="td">${row[i][1]}</td>
                                    <td class="td">${row[i][3]}</td>
                                    <td class="td">${row[i][2]}</td>
                                </tr>
                            `
                        }
                        rankno=rankno+1
                    }
                }else{
                    if(row[i][0]==data["data"]["userid"]){
                        rankinnerhtml=`
                            ${rankinnerhtml}
                            <tr class="highlighttr">
                                <td class="td">${rankno}</td>
                                <td class="td">${row[i][1]}</td>
                                <td class="td">${row[i][3]}</td>
                                <td class="td">${row[i][2]}</td>
                            </tr>
                        `
                    }else{
                        rankinnerhtml=`
                            ${rankinnerhtml}
                            <tr class="tr">
                                <td class="td">${rankno}</td>
                                <td class="td">${row[i][1]}</td>
                                <td class="td">${row[i][3]}</td>
                                <td class="td">${row[i][2]}</td>
                            </tr>
                        `
                    }
                    rankno=rankno+1
                }
            }
            return `
                <div class="rankdiv macossectiondivy">
                    <table class="ranktable">
                        <tr>
                            <td class="td">no.</td>
                            <td class="td">username</td>
                            <td class="td">score</td>
                            <td class="td">time</td>
                        </tr>
                        ${rankinnerhtml}
                    </table>
                </div>
                <input type="button" class="button longbutton" onclick="location.href='index.html'" value="重新啟動遊戲">
            `
        })
    }
}

// 玩家檢查
function check(){
    if(player[1]==2){ // 豆子
        score=score+10
        mainarray[player[0][0]][player[0][1]]=1
        document.querySelectorAll(".row>div")[player[0][0]*27+player[0][1]].innerHTML=``
        player[1]=1
    }else if(player[1]==3){ // 星星
        score=score+50
        mainarray[player[0][0]][player[0][1]]=1
        document.querySelectorAll(".row>div")[player[0][0]*27+player[0][1]].innerHTML=``
        player[1]=1
    }else if(player[1]==5){ // 生命值加一
        life=life+1
        document.getElementById("life").innerHTML=`
            ${life}
        `
        mainarray[player[0][0]][player[0][1]]=1
        document.querySelectorAll(".row>div")[player[0][0]*27+player[0][1]].innerHTML=``
        player[1]=1
    }else if(player[1]==6){ // 速度x2
        playerspeed=playerspeed*2
        mainarray[player[0][0]][player[0][1]]=1
        document.querySelectorAll(".row>div")[player[0][0]*27+player[0][1]].innerHTML=``
        player[1]=1
    }else if(player[1]==7){ // 無敵
        invincible=true
        document.getElementById("player").classList.add("invincible")
        setTimeout(function(){
            invincible=false
            document.getElementById("player").classList.remove("invincible")
        },15000)
        mainarray[player[0][0]][player[0][1]]=1
        document.querySelectorAll(".row>div")[player[0][0]*27+player[0][1]].innerHTML=``
        player[1]=1
    }
    if(!invincible){
        if((player[0].toString()==ghost1.toString()||player[0].toString()==ghost2.toString()||player[0].toString()==ghost3.toString())&&canhit){
            let playercooldown

            life=life-1

            document.getElementById("life").innerHTML=`
                ${life}
            `

            if(life==0){
                console.log("1")
                clearInterval(timer)
                timestop=true
                document.onkeydown=function(event){
                    if(event.key=="ArrowUp"||event.key=="ArrowDown"||event.key=="ArrowLeft"||event.key=="ArrowRight"){
                        event.preventDefault()
                    }
                }
                oldajax("POST","register.php",JSON.stringify({
                    "adddata": true,
                    "time": min*60+sec,
                    "name": username,
                    "score": score,
                    "difficulty": difficulty
                })).onload=function(){
                    let data=JSON.parse(this.responseText)
                    rank(data) // 顯示排行榜
                }
            }else{
                canhit=false
                document.getElementById("player").style.opacity=0
                setTimeout(function(){
                    document.getElementById("player").style.opacity=1
                },500)
                playercooldown=setInterval(function(){
                    document.getElementById("player").style.opacity=0
                    setTimeout(function(){
                        document.getElementById("player").style.opacity=1
                    },500)
                },1000)
                clearInterval(timer)
                document.onkeydown=function(event){
                    if(event.key=="p"||event.key=="P"){
                        stopstart()
                    }else if(event.key=="ArrowUp"||event.key=="ArrowDown"||event.key=="ArrowLeft"||event.key=="ArrowRight"){
                        event.preventDefault()
                    }
                }
                timestop=true
                setTimeout(function(){
                    timestart()
                    pacmankeydonwn()
                    clearInterval(playercooldown)
                    timestop=false
                    setTimeout(function(){
                        canhit=true
                    },1000)
                },3000)
            }
        }
    }else{
        if(String(player[0])==String(ghost1)){
            score=score+100
            ghost1=[14,11]
            document.getElementById("ghost1").style.top="355px"
            document.getElementById("ghost1").style.left="285px"
        }
        if(String(player[0])==String(ghost2)){
            score=score+100
            ghost2=[14,13]
            document.getElementById("ghost2").style.top="355px"
            document.getElementById("ghost2").style.left="330px"
        }
        if(String(player[0])==String(ghost3)){
            score=score+100
            ghost3=[14,15]
            document.getElementById("ghost3").style.top="355px"
            document.getElementById("ghost3").style.left="380px"
        }
    }
    document.getElementById("score").innerHTML=`
        ${score}
    `
}

// 玩家移動偵測/更新
function moveto(movetype,startx,starty){
    for(let i=0;i<blockwh;i=i+1){
        setTimeout(function(){
            if(movetype=="up"){
                document.getElementById("player").style.top=((startx*25+mid)-i)+"px"
            }else if(movetype=="down"){
                document.getElementById("player").style.top=((startx*25+mid)+i)+"px"
            }else if(movetype=="left"){
                document.getElementById("player").style.left=((starty*25+mid)-i)+"px"
            }else if(movetype=="right"){
                document.getElementById("player").style.left=((starty*25+mid)+i)+"px"
            }else{
                conlog("[ERROR] function moveto movetype error","red")
            }
        },500/blockwh)
    }
}

// 玩家按鍵事件
function pacmankeydonwn(){
    document.onkeydown=function(event){
        if(event.key=="ArrowUp"){
            playermove("up")
            if(!click){
                setTimeout(function(){
                    click=true
                },250)
            }
        }else if(event.key=="ArrowDown"){
            playermove("down")
            if(!click){
                setTimeout(function(){
                    click=true
                },250)
            }
        }else if(event.key=="ArrowLeft"){
            playermove("left")
            if(!click){
                setTimeout(function(){
                    click=true
                },250)
            }
        }else if(event.key=="ArrowRight"){
            playermove("right")
            if(!click){
                setTimeout(function(){
                    click=true
                },250)
            }
        }else if(event.key=="p"||event.key=="P"){
            stopstart()
        }
    }
    document.onkeyup=function(event){
        if(event.key=="ArrowUp"){
            click=false
        }else if(event.key=="ArrowDown"){
            click=false
        }else if(event.key=="ArrowLeft"){
            click=false
        }else if(event.key=="ArrowRight"){
            click=false
        }else if(event.key=="p"){
            click=false
        }
    }
}

// 鬼偵測不重疊
function checkghostnotblock(testarray){
    if(testarray.toString()!=ghost1.toString()&&testarray.toString()!=ghost2.toString()&&testarray.toString()!=ghost3.toString()){
        return true
    }else{
        return false
    }
}

// 鬼啟動
function ghostrun(){
    if(difficulty=="easy"){
        ghostmove("ghost1",ghost1)
    }else if(difficulty=="normal"){
        ghostmove("ghost1",ghost1)
        ghostmove("ghost2",ghost2)
    }else{
        ghostmove("ghost1",ghost1)
        ghostmove("ghost2",ghost2)
        ghostmove("ghost3",ghost3)
    }
}

// 鬼移動
function ghostmove(name,array){
    if(!timestop){
        let type=parseInt(Math.random()*4) // 會有上(0)下(1)左(2)右(3)4種方式
        if(type==0){
            if(mainarray[array[0]-1][array[1]]!=0&&checkghostnotblock([array[0]-1,array[1]])&&mainarray[array[0]-1][array[1]]){
                for(let i=0;i<blockwh;i=i+1){
                    setTimeout(function(){
                        document.getElementById(name).style.top=((array[0]*25+mid)-i)+"px"
                    },500/blockwh)
                }
                if(name=="ghost1"){
                    ghost1=[array[0]-1,array[1]]
                }else if(name=="ghost2"){
                    ghost2=[array[0]-1,array[1]]
                }else{
                    ghost3=[array[0]-1,array[1]]
                }
            }else{ ghostmove(name,array) } // 再用一次
        }else if(type==1){
            if(mainarray[array[0]+1][array[1]]!=0&&checkghostnotblock([array[0]+1,array[1]])&&mainarray[array[0]+1][array[1]]){
                for(let i=0;i<blockwh;i=i+1){
                    setTimeout(function(){
                        document.getElementById(name).style.top=((array[0]*25+mid)+i)+"px"
                    },500/blockwh)
                }
                if(name=="ghost1"){
                    ghost1=[array[0]+1,array[1]]
                }else if(name=="ghost2"){
                    ghost2=[array[0]+1,array[1]]
                }else{
                    ghost3=[array[0]+1,array[1]]
                }
            }else{ ghostmove(name,array) }
        }else if(type==2){
            if(mainarray[array[0]][array[1]-1]!=0&&checkghostnotblock([array[0],array[1]-1])&&mainarray[array[0]][array[1]-1]){
                for(let i=0;i<blockwh;i=i+1){
                    setTimeout(function(){
                        document.getElementById(name).style.left=((array[1]*25+mid)-i)+"px"
                    },500/blockwh)
                }
                if(name=="ghost1"){
                    ghost1=[array[0],array[1]-1]
                }else if(name=="ghost2"){
                    ghost2=[array[0],array[1]-1]
                }else{
                    ghost3=[array[0],array[1]-1]
                }
            }else{ ghostmove(name,array) }
        }else if(type==3){
            if(mainarray[array[0]][array[1]+1]!=0&&checkghostnotblock([array[0],array[1]+1])&&mainarray[array[0]][array[1]+1]){
                for(let i=0;i<blockwh;i=i+1){
                    setTimeout(function(){
                        document.getElementById(name).style.left=((array[1]*25+mid)+i)+"px"
                    },500/blockwh)
                }
                if(name=="ghost1"){
                    ghost1=[array[0],array[1]+1]
                }else if(name=="ghost2"){
                    ghost2=[array[0],array[1]+1]
                }else{
                    ghost3=[array[0],array[1]+1]
                }
            }else{ ghostmove(name,array) }
        }else{ console.log("type error") }

        if((player[0].toString()==ghost1.toString()||player[0].toString()==ghost2.toString()||player[0].toString()==ghost3.toString())&&canhit){
            let playercooldown

            life=life-1

            document.getElementById("life").innerHTML=`
                ${life}
            `

            if(life==0){
                clearInterval(timer)
                timestop=true
                document.onkeydown=function(event){
                    if(event.key=="ArrowUp"||event.key=="ArrowDown"||event.key=="ArrowLeft"||event.key=="ArrowRight"){
                        event.preventDefault()
                    }
                }
                oldajax("POST","register.php",JSON.stringify({
                    "adddata": true,
                    "time": min*60+sec,
                    "name": username,
                    "score": score,
                    "difficulty": difficulty
                })).onload=function(){
                    let data=JSON.parse(this.responseText)
                    rank(data) // 顯示排行榜
                }
            }else{
                canhit=false
                document.getElementById("player").style.opacity=0
                setTimeout(function(){
                    document.getElementById("player").style.opacity=1
                },500)
                playercooldown=setInterval(function(){
                    document.getElementById("player").style.opacity=0
                    setTimeout(function(){
                        document.getElementById("player").style.opacity=1
                    },500)
                },1000)
                clearInterval(timer)
                document.onkeydown=function(event){
                    if(event.key=="p"||event.key=="P"){
                        stopstart()
                    }else if(event.key=="ArrowUp"||event.key=="ArrowDown"||event.key=="ArrowLeft"||event.key=="ArrowRight"){
                        event.preventDefault()
                    }
                }
                timestop=true
                setTimeout(function(){
                    timestart()
                    pacmankeydonwn()
                    clearInterval(playercooldown)
                    timestop=false
                    setTimeout(function(){
                        canhit=true
                    },1000)
                },3000)
            }
        }
    }
}

// 玩家移動
function playermove(type){
    setTimeout(function(){

    },1000)
    if(type=="up"){
        if(mainarray[player[0][0]-1][player[0][1]]!=0&&mainarray[player[0][0]-1][player[0][1]]!=4){
            moveto("up",player[0][0],player[0][1])
            player=[
                [player[0][0]-1,player[0][1]],
                mainarray[player[0][0]-1][player[0][1]]
            ]
            check()
        }
    }else if(type=="down"){
        if(mainarray[player[0][0]+1][player[0][1]]!=0&&mainarray[player[0][0]+1][player[0][1]]!=4){
            moveto("down",player[0][0],player[0][1])
            player=[
                [player[0][0]+1,player[0][1]],
                mainarray[player[0][0]+1][player[0][1]]
            ]
            check()
        }
    }else if(type=="left"){
        if(mainarray[player[0][0]][player[0][1]-1]!=0&&mainarray[player[0][0]][player[0][1]-1]!=4){
            if(isset(mainarray[player[0][0]][player[0][1]-1])){
                moveto("left",player[0][0],player[0][1])
                player=[
                    [player[0][0],player[0][1]-1],
                    mainarray[player[0][0]][player[0][1]-1]
                ]
                check()
            }else{
                if(isset(mainarray[player[0][0]][26])){
                    document.getElementById("player").style.left=((27*25+5)-25)+"px"
                    player=[
                        [player[0][0],26],
                        mainarray[player[0][0]][26]
                    ]
                    check()
                }
            }
        }
    }else if(type=="right"){
        if(mainarray[player[0][0]][player[0][1]+1]!=0&&mainarray[player[0][0]][player[0][1]+1]!=4){
            if(isset(mainarray[player[0][0]][player[0][1]+1])){
                moveto("right",player[0][0],player[0][1])
                player=[
                    [player[0][0],player[0][1]+1],
                    mainarray[player[0][0]][player[0][1]+1]
                ]
                check()
            }else{
                if(isset(mainarray[player[0][0]][0])){
                    document.getElementById("player").style.left=((-1*25+5)+25)+"px"
                    player=[
                        [player[0][0],0],
                        mainarray[player[0][0]][0]
                    ]
                    check()
                }
            }
        }
    }else{ console.log("[ERROR] playermove key type error.","red") }
}

// stop/start
function stopstart(){
    if(document.getElementById("pausecontinue").innerHTML=="暫停"){
        clearInterval(timer)
        document.getElementById("pausecontinue").innerHTML="繼續"
        document.onkeydown=function(event){
            if(event.key=="p"||event.key=="P"){
                stopstart()
            }else if(event.key=="ArrowUp"||event.key=="ArrowDown"||event.key=="ArrowLeft"||event.key=="ArrowRight"){
                event.preventDefault()
            }
        }
        timestop=true
    }else{
        timestart()
        document.getElementById("pausecontinue").innerHTML="暫停"
        pacmankeydonwn()
        timestop=false
    }
}

// timer
function timestart(){
    timer=setInterval(function(){
        let time
        sec=sec+1
        if(sec>=60){
            sec=0
            min=min+1
        }
        if(sec==3&&min==0){
            document.querySelectorAll(".door").forEach(function(event){
                event.style.backgroundColor="black"
            })
            for(let i=0;i<doorlist.length;i=i+1){
                mainarray[doorlist[i][0]][doorlist[i][1]]=4
            }
        }
        time=sec+(min*60)
        if(time>=30&&(((time-30)%15))==0){
            specialitem()
        }
        document.getElementById("timer").innerHTML=`
            ${String(min).padStart(2,"0")}:${String(sec).padStart(2,"0")}
        `
    },1000)
}

// 特殊道具
function specialitem(){
    let item=parseInt(Math.random()*3)+5 // 5~7的隨機數
    let road=[] // 是道路的陣列
    let random // 隨機數
    let itemname // 道具名

    // 製作是道路的陣列
    for(let i=0;i<mainarray.length;i=i+1){
        for(let j=0;j<mainarray[i].length;j=j+1){
            if(mainarray[i][j]=="1"){
                road.push([i,j])
            }
        }
    }

    random=parseInt(Math.random()*road.length)

    // 給定名稱
    if(item==5){
        itemname="apple"
    }else if(item==6){
        itemname="pineapple"
    }else{
        itemname="strawberry"
    }

    mainarray[road[random][0]][road[random][1]]=item // 給mainarray物品代碼
    document.querySelectorAll(".row>div")[road[random][0]*27+road[random][1]].innerHTML=`
        <div class="${itemname}"></div>
    `
}

// 初始化鬼的位置及設定星星數START
if(difficulty=="easy"){
    starcount=10
    maininnerhtml2=`
        <div class="ghost ghost1" id="ghost1" style="width: 20px;height: 20px;top: 355px;left: 330px;"></div>
        <div class="player" id="player" style="width: 20px;height: 20px;"></div>
    `
    ghost1=[14,13]
}else if(difficulty=="normal"){
    starcount=8
    maininnerhtml2=`
        <div class="ghost ghost1" id="ghost1" style="width: 20px;height: 20px;top: 355px;left: 285px;"></div>
        <div class="ghost ghost2" id="ghost2" style="width: 20px;height: 20px;top: 355px;left: 380px;"></div>
        <div class="player" id="player" style="width: 20px;height: 20px;"></div>
    `
    ghost1=[14,11]
    ghost2=[14,15]
}else{
    starcount=6
    maininnerhtml2=`
        <div class="ghost ghost1" id="ghost1" style="width: 20px;height: 20px;top: 355px;left: 285px;"></div>
        <div class="ghost ghost2" id="ghost2" style="width: 20px;height: 20px;top: 355px;left: 330px;"></div>
        <div class="ghost ghost3" id="ghost3" style="width: 20px;height: 20px;top: 355px;left: 380px;"></div>
        <div class="player" id="player" style="width: 20px;height: 20px;"></div>
    `
    ghost1=[14,11]
    ghost2=[14,13]
    ghost3=[14,15]
}
// 初始化鬼的位置及設定星星數END

document.getElementById("difficulty").innerHTML=difficulty // 拿到難易度並顯示
document.getElementById("name").innerHTML=username // 拿到名稱並顯示

// 主程式START
oldajax("GET","map.txt").onload=function(){
    let data=this.responseText.split("\r\n") // 分隔及讀取檔案
    let tempstartcount=starcount
    // 畫面輸出START
    let maininnerhtml=`
        <div class="center">
            <div class="position">
    `

    for(let i=0;i<data.length;i=i+1){
        let row=data[i].split(",")
        mainarray.push([])
        maininnerhtml=`
            ${maininnerhtml}
            <div class="row">
        `
        for(let j=0;j<row.length;j=j+1){
            if(row[j]==0){
                maininnerhtml=`
                    ${maininnerhtml}
                    <div class="wall"></div>
                `
                mainarray[i].push(0)
            }else if(row[j]==1){
                let roadinnerhtml
                if((((i+1)/2)<=7.5&&Math.random()>=0.95&&tempstartcount>0&&topstarcount<(starcount/2))||(((i+1)/2)>=7.5&&Math.random()>=0.95&&tempstartcount>0)){
                    roadinnerhtml=`
                        <div class="star center">&#9733;</div>
                    `
                    mainarray[i].push(3)
                    tempstartcount=tempstartcount-1
                    if((i+1/2)<=15){
                        topstarcount=topstarcount+1
                    }
                }else{
                    roadinnerhtml=`
                        <div class="dot center"></div>
                    `
                    mainarray[i].push(2)
                }
                maininnerhtml=`
                    ${maininnerhtml}
                    <div class="road">
                        ${roadinnerhtml}
                    </div>
                `
            }else if(row[j]==2){
                maininnerhtml=`
                    ${maininnerhtml}
                    <div class="ghostrespawn"></div>
                `
                mainarray[i].push(4)
            }else if(row[j]==3){
                maininnerhtml=`
                    ${maininnerhtml}
                    <div class="door"></div>
                `
                doorlist.push([i,j])
                mainarray[i].push(0)
            }else{ console.log("error") }
        }
        maininnerhtml=`
                ${maininnerhtml}
            </div>
        `
    }
    // 畫面輸出END

    document.getElementById("app").innerHTML=`
                ${maininnerhtml}
                ${maininnerhtml2}
            </div>
        </div>
    `

    // 玩家定位START
    let f=parseInt(Math.random()*25)+1
    let s=parseInt(Math.random()*30)+1
    function xy(){
        if(mainarray[f][s]==0||mainarray[f][s]==1||mainarray[f][s]==4||mainarray[f][s]==6||mainarray[f][s]==7||!mainarray[f][s]){
            f=parseInt(Math.random()*25)+1
            s=parseInt(Math.random()*30)+1
            xy()
        }
    }

    xy()

    document.getElementById("player").style.top=(f*25+5)+"px"
    document.getElementById("player").style.left=(s*25+5)+"px"
    player=[[f,s],mainarray[f][s]]
    // 玩家定位END

    // 遊戲初始化START
    document.getElementById("pausecontinue").onclick=function(){ stopstart() } // 暫停偵測
    pacmankeydonwn() // 玩家偵測
    timestart() // 計時開始
    setInterval(ghostrun,500) // 鬼移動
    // 遊戲初始化END
}
// 主程式END

// 排行榜START
document.getElementById("statisticaldata").onclick=function(){
    if(document.getElementById("pausecontinue").innerHTML=="暫停"){ // 如果是運行中就暫停
        stopstart()
    }
    oldajax("GET","register.php?getrank=").onload=function(){ // 獲取排行榜
        let data=JSON.parse(this.responseText)
        rank(data)
    }
}
// 排行榜END

startmacossection() // 函式庫啟動
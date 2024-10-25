let userid
let nickname
let gameid
let gamename
let timer1
let timer2
let mm=0
let ss=0

function page(pageid){
    document.getElementById("page1").style.display="none"
    document.getElementById("page2").style.display="none"
    document.getElementById("page3").style.display="none"
    document.getElementById("page4").style.display="none"
    document.getElementById("page5").style.display="none"
    document.getElementById("page6").style.display="none"
    document.getElementById("page7").style.display="none"
    document.getElementById("page"+pageid).style.display="block"
}

let ws=new WebSocket("ws://127.54.54.54:1022")

ws.onopen=function(){
    console.log("in")
}

ws.onmessage=function(event){
    let data=JSON.parse(event.data)
    console.log(data)
    if(data["success"]){
        if(data["key"]=="signin"){
            page(2)
            userid=data["userid"]
            nickname=data["nickname"]
            document.getElementById("nickname").innerHTML=nickname
        }else if(data["key"]=="creategame"){
            page(5)
            gameid=data["gameid"]
            gamename=data["gamename"]
            document.getElementById("gamename").innerHTML=gamename
            document.getElementById("player").innerHTML=`
                <div>${nickname}[HOST]</div>
            `
            document.getElementById("page5").innerHTML=`
                <input type="button" onclick="alert('房間尚未滿人')" value="開始遊戲">
            `
        }else if(data["key"]=="getgamelist"){
            page(4)
            document.getElementById("page4").innerHTML=``
            for(let i=0;i<data["data"].length;i=i+1){
                document.getElementById("page4").innerHTML+=`
                    <input type="button" class="game" data-id="${data["data"][i]["id"]}" value="${data["data"][i]["gamename"]}  ${data["data"][i]["user"].length}/2">
                `
            }
            document.querySelectorAll(".game").forEach(function(event){
                event.onclick=function(){
                    ws.send(JSON.stringify({
                        "key": "joingame",
                        "gameid": event.dataset.id,
                        "userid": userid,
                        "nickname": nickname
                    }))
                }
            })
        }else if(data["key"]=="joingame"){
            if(data["data"]["user"].includes(userid)){
                page(5)
                clearInterval(timer1)
                gameid=data["data"]["id"]
                gamename=data["data"]["gamename"]
                document.getElementById("gamename").innerHTML=gamename
                document.getElementById("player").innerHTML=`
                    <div>${data["data"]["userlist"][0]}[HOST]</div>
                    <div>${data["data"]["userlist"][1]}</div>
                `
                if(data["data"]["user"][0]==userid){
                    document.getElementById("page5").innerHTML=`
                        <input type="button" id="startgame" value="開始遊戲">
                    `
                    document.getElementById("startgame").onclick=function(){
                        ws.send(JSON.stringify({
                            "key": "initgame",
                            "gameid": gameid
                        }))
                    }
                }
            }
        }else if(data["key"]=="initgame"){
            if(data["data"]["id"]==gameid){
                page(6)
                document.getElementById("player").innerHTML=`
                    <div>(黑)${data["data"]["userlist"][0]}[YOUR TURN]</div>
                    <div>${data["data"]["userlist"][1]}(白)</div>
                `
                document.getElementById("timer").innerHTML=`00:00`
                timer2=setInterval(function(){
                    ss=ss+1
                    if(ss>=60){
                        mm=mm+1
                        ss=0
                    }
                    document.getElementById("timer").innerHTML=`
                        ${String(mm).padStart(2,"0")}:${String(ss).padStart(2,"0")}
                    `
                },1000)
                ws.send(JSON.stringify({
                    "key": "updategame",
                    "gameid": gameid,
                    "border": [
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"]
                    ],
                    "turn": "B",
                    "timer": [mm,ss]
                }))
            }
        }else if(data["key"]=="updategame"){
            if(data["data"]["id"]==gameid){
                page(6)
                if(data["data"]["turn"]=="B"){
                    document.getElementById("player").innerHTML=`
                        <div>(黑)${data["data"]["userlist"][0]}[YOUR TURN]</div>
                        <div>${data["data"]["userlist"][1]}(白)</div>
                    `
                }else{
                    document.getElementById("player").innerHTML=`
                        <div>(黑)${data["data"]["userlist"][0]}</div>
                        <div>[YOUR TURN]${data["data"]["userlist"][1]}(白)</div>
                    `
                }
                mm=data["data"]["timer"][0]
                ss=data["data"]["timer"][1]
                document.getElementById("timer").innerHTML=`
                    ${String(mm).padStart(2,"0")}:${String(ss).padStart(2,"0")}
                `
                document.getElementById("page6").style.display="grid"
                document.getElementById("page6").innerHTML=``
                for(let i=0;i<data["data"]["border"].length;i=i+1){
                    for(let j=0;j<data["data"]["border"].length;j=j+1){
                        document.getElementById("page6").innerHTML+=`
                            <div class="borderdiv ${data["data"]["border"][i][j]}" data-id="${i}_${j}">
                                <div class="border right bottom"></div>
                                <div class="border left bottom"></div>
                                <div class="border right top"></div>
                                <div class="border left top"></div>
                            </div>
                        `
                    }
                }

                document.querySelectorAll(".borderdiv").forEach(function(event){
                    event.onclick=function(){
                        let i=event.dataset.id.split("_")[0]
                        let j=event.dataset.id.split("_")[1]
                        if((data["data"]["turn"]=="B"&&data["data"]["user"][0]==userid)||(data["data"]["turn"]=="W"&&data["data"]["user"][1]==userid)){
                            if(data["data"]["border"][i][j]=="-"){
                                data["data"]["border"][i][j]=data["data"]["turn"]
                                ws.send(JSON.stringify({
                                    "key": "updategame",
                                    "gameid": gameid,
                                    "border": data["data"]["border"],
                                    "turn": data["data"]["turn"]=="W"?"B":"W",
                                    "timer": [mm,ss]
                                }))
                            }else{
                                alert("無法放置在目前位置。")
                            }
                        }else{
                            alert("不是你的回合。")
                        }
                    }
                })
            }
        }else if(data["key"]=="endgame"){
            if(data["data"]["id"]==gameid){
                page(7)
                clearInterval(timer2)
                document.getElementById("player").innerHTML=``
                if(data["winner"]=="-1"){
                    document.getElementById("page7").innerHTML=`
                        <div>遊戲結果: 平局</div>
                        <input type="button" id="contiune" value="繼續遊戲">
                    `
                }else{
                    if(data["winner"]=="B"){
                        if(data["data"]["user"][0]==userid){
                            document.getElementById("page7").innerHTML=`
                                <div>遊戲結果: 贏</div>
                                <div>勝利玩家暱稱: ${data["data"]["userlist"][0]}</div>
                                <input type="button" id="contiune" value="繼續遊戲">
                            `
                        }else{
                            document.getElementById("page7").innerHTML=`
                                <div>遊戲結果: 輸</div>
                                <div>勝利玩家暱稱: ${data["data"]["userlist"][0]}</div>
                                <input type="button" id="contiune" value="繼續遊戲">
                            `
                        }
                    }else{
                        if(data["data"]["user"][1]==userid){
                            document.getElementById("page7").innerHTML=`
                                <div>遊戲結果: 贏</div>
                                <div>勝利玩家暱稱: ${data["data"]["userlist"][1]}</div>
                                <input type="button" id="contiune" value="繼續遊戲">
                            `
                        }else{
                            document.getElementById("page7").innerHTML=`
                                <div>遊戲結果: 輸</div>
                                <div>勝利玩家暱稱: ${data["data"]["userlist"][1]}</div>
                                <input type="button" id="contiune" value="繼續遊戲">
                            `
                        }
                    }
                }

                document.getElementById("contiune").onclick=function(){
                    page(2)
                    mm=0
                    ss=0
                    gameid=0
                    gamename=0
                    document.getElementById("gamename").innerHTML=``
                    document.getElementById("timer").innerHTML=``
                    document.getElementById("player").innerHTML=``
                }
            }
        }
    }else{
        alert(data["data"])
    }
}

page(1)

document.getElementById("start").onclick=function(){
    ws.send(JSON.stringify({
        "key": "signin",
        "nickname": document.getElementById("nicknameinput").value
    }))
}

document.getElementById("create").onclick=function(){
    page(3)
    document.getElementById("creategame").onclick=function(){
        ws.send(JSON.stringify({
            "key": "creategame",
            "userid": userid,
            "gamename": document.getElementById("gamenameinput").value
        }))
    }
}

document.getElementById("join").onclick=function(){
    ws.send(JSON.stringify({
        "key": "getgamelist"
    }))
    timer1=setInterval(function(){
        ws.send(JSON.stringify({
            "key": "getgamelist"
        }))
    },3000)
}
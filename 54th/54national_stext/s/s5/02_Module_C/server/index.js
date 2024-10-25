let timemm=0
let timess=0
let userid=null
let nickname=null
let roomid=null
let roomname=null
let timer1
let timer2

function clearall(page){
    document.getElementById("page1").style.display="none"
    document.getElementById("page2").style.display="none"
    document.getElementById("page3").style.display="none"
    document.getElementById("page4").style.display="none"
    document.getElementById("page5").style.display="none"
    document.getElementById("page6").style.display="none"
    document.getElementById("page7").style.display="none"
    document.getElementById("page"+page).style.display="block"
}

let websocket=new WebSocket("ws://127.54.54.54:1022")

websocket.onopen=function(){
    console.log("in")
}

websocket.onmessage=function(event){
    let data=JSON.parse(event.data)
    if(data["success"]){
        if(data["key"]=="signin"){
            clearall(2)
            userid=data["userid"]
            nickname=data["nickname"]
            document.getElementById("nicknamediv").innerHTML=nickname
        }else if(data["key"]=="newroom"){
            clearall(5)
            roomid=data["roomid"]
            roomname=data["roomname"]
            document.getElementById("roomdiv").innerHTML=roomname
            document.getElementById("playerlist").innerHTML=`
                <div>${nickname} [HOST]</div>
            `
            document.getElementById("page5").innerHTML=`
                <input type="button" id="startgame" value="開始遊戲">
            `
            document.getElementById("startgame").onclick=function(){
                alert("玩家尚未滿人")
            }
        }else if(data["key"]=="getroomlist"){
            clearall(4)
            document.getElementById("page4").innerHTML=``
            for(let i=0;i<data["data"].length;i=i+1){
                document.getElementById("page4").innerHTML+=`
                    <input type="button" class="joinroom" data-id="${data["data"][i]["id"]}" value="${data["data"][i]["name"]} ${data["data"][i]["player"].length}/2">
                `
            }
            document.querySelectorAll(".joinroom").forEach(function(event){
                event.onclick=function(){
                    websocket.send(JSON.stringify({
                        "key": "joinroom",
                        "roomid": event.dataset.id,
                        "userid": userid,
                        "usernickname": nickname
                    }))
                }
            })
        }else if(data["key"]=="joinroom"){
            if(data["game"]["player"].includes(userid)){
                clearall(5)
                clearInterval(timer1)
                roomid=data["game"]["id"]
                roomname=data["game"]["name"]
                document.getElementById("roomdiv").innerHTML=roomname
                document.getElementById("playerlist").innerHTML=`
                    <div>${data["game"]["playername"][0]} [HOST]</div>
                    <div>${data["game"]["playername"][1]}</div>
                `
                if(data["game"]["player"][0]==userid){
                    document.getElementById("page5").innerHTML=`
                        <input type="button" id="startgame" value="開始遊戲">
                    `
                    document.getElementById("startgame").onclick=function(){
                        websocket.send(JSON.stringify({
                            "key": "initgame",
                            "roomid": roomid
                        }))
                    }
                }
            }
        }else if(data["key"]=="initgame"){
            if(data["game"]["player"].includes(userid)){
                clearall(6)
                clearInterval(timer1)
                document.getElementById("playerlist").innerHTML=`
                    <div>{黑} ${data["game"]["playername"][0]} [YOUR TURN]</div>
                    <div>${data["game"]["playername"][1]} {白}</div>
                `
                document.getElementById("time").innerHTML=`
                    00:00
                `
                timer2=setInterval(function(){
                    timess=timess+1
                    if(timess==60){
                        timemm=timemm+1
                        timess=0
                    }
                    document.getElementById("time").innerHTML=`
                        ${String(timemm).padStart(2,"0")}:${String(timess).padStart(2,"0")}
                    `
                },1000)
                websocket.send(JSON.stringify({
                    "key": "updategame",
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
                    "roomid": roomid
                }))
            }
        }else if(data["key"]=="updategame"){
            if(data["game"]["player"].includes(userid)){
                clearall(6)
                clearInterval(timer1)
                if(data["game"]["turn"]=="B"){
                    document.getElementById("playerlist").innerHTML=`
                        <div>{黑} ${data["game"]["playername"][0]} [YOUR TURN]</div>
                        <div>${data["game"]["playername"][1]} {白}</div>
                    `
                }else{
                    document.getElementById("playerlist").innerHTML=`
                        <div>{黑} ${data["game"]["playername"][0]}</div>
                        <div>[YOUR TURN] ${data["game"]["playername"][1]} {白}</div>
                    `
                }
                document.getElementById("page6").style.display="grid"
                document.getElementById("page6").innerHTML=``
                for(let i=0;i<15;i=i+1){
                    for(let j=0;j<15;j=j+1){
                        document.getElementById("page6").innerHTML+=`
                            <div class="borderdiv ${data["game"]["border"][i][j]}" data-id="${i}_${j}">
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
                        if((data["game"]["turn"]=="B"&&data["game"]["player"][0]==userid)||(data["game"]["turn"]=="W"&&data["game"]["player"][1]==userid)){
                            if(data["game"]["border"][i][j]=="-"){
                                data["game"]["border"][i][j]=data["game"]["turn"]
                                websocket.send(JSON.stringify({
                                    "key": "updategame",
                                    "border": data["game"]["border"],
                                    "turn": data["game"]["turn"]=="B"?"W":"B",
                                    "roomid": roomid
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
            if(data["game"]["player"].includes(userid)){
                clearall(7)
                clearInterval(timer2)
                document.getElementById("playerlist").innerHTML=``
                if(data["winner"]=="B"){
                    document.getElementById("page7").innerHTML=`
                        勝利玩家: ${data["game"]["playername"][0]}<br>
                        遊戲結果: ${data["game"]["player"][0]==userid?"贏":"輸"}<br>
                        <input type="button" id="countiune" value="繼續遊戲">
                    `
                }else if(data["winner"]=="W"){
                    document.getElementById("page7").innerHTML=`
                        勝利玩家: ${data["game"]["playername"][1]}<br>
                        遊戲結果: ${data["game"]["player"][1]==userid?"贏":"輸"}<br>
                        <input type="button" id="countiune" value="繼續遊戲">
                    `
                }else{
                    document.getElementById("page7").innerHTML=`
                        遊戲結果: 平局<br>
                        <input type="button" id="countiune" value="繼續遊戲">
                    `
                }
                document.getElementById("countiune").onclick=function(){
                    clearall(2)
                    document.getElementById("roomdiv").innerHTML=``
                    document.getElementById("time").innerHTML=``
                    timemm=0
                    timess=0
                }
            }
        }
    }else{
        alert(data["data"])
    }
}

clearall(1)

document.getElementById("start").onclick=function(){
    websocket.send(JSON.stringify({
        "key": "signin",
        "nickname": document.getElementById("nickname").value
    }))
}

document.getElementById("newroom").onclick=function(){
    clearall(3)

    document.getElementById("create").onclick=function(){
        websocket.send(JSON.stringify({
            "key": "newroom",
            "roomname": document.getElementById("roomname").value,
            "userid": userid,
            "usernickname": nickname
        }))
    }
}

document.getElementById("joinroom").onclick=function(){
    websocket.send(JSON.stringify({
        "key": "getroomlist"
    }))
    timer1=setInterval(function(){
        websocket.send(JSON.stringify({
            "key": "getroomlist"
        }))
    },3000)
}
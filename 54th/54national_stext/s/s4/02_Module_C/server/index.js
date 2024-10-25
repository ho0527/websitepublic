let socket=new WebSocket("ws://127.54.54.54:1022")
let timemm=0
let timess=0
let userid
let gameid
let nickname
let gamename
let interval
let time
let game

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

socket.onopen=function(){
    console.log("in")
}

socket.onmessage=function(event){
    let data=JSON.parse(event["data"])
    if(data["success"]){
        if(data["key"]=="signin"){
            nickname=document.getElementById("inputnickname").value
            userid=data["data"]
            clearall(2)
            document.getElementById("nickname").innerText=nickname
        }else if(data["key"]=="getgamelist"){
            let tempdata=[...data["data"]]
            tempdata.sort(function(a,b){
                if(a["user"].length>b["user"].length){
                    return 1
                }else if(a["user"].length<b["user"].length){
                    return -1
                }else{
                    return 0
                }
            })
            document.getElementById("page4").innerHTML=``
            for(let i=0;i<tempdata.length;i=i+1){
                document.getElementById("page4").innerHTML+=`
                    <input type="button" class="gamebutton" data-id="${tempdata[i]["id"]}" value="${tempdata[i]["name"]+"  ("+tempdata[i]["user"].length}/2)">
                `
            }
            document.querySelectorAll(".gamebutton").forEach(function(event){
                event.onclick=function(){
                    socket.send(JSON.stringify({
                        "key": "joingame",
                        "userid": userid,
                        "gameid": event.dataset.id
                    }))
                }
            })
        }else if(data["key"]=="creategame"){
            clearall(5)
            gamename=data["data"]["name"]
            gameid=data["gameid"]
            document.getElementById("game").innerText=gamename
            document.getElementById("userlisthost").innerText=nickname+"(HOST)"
            document.getElementById("page5").innerHTML=`
                <input type="button" id="startgame" value="開始遊戲">
            `
            document.getElementById("startgame").onclick=function(){
                socket.send(JSON.stringify({
                    "key": "startgame",
                    "gameid": gameid,
                }))
            }
        }else if(data["key"]=="joingame"){
            if(data["data"]["user"].includes(userid)){
                clearall(5)
                clearInterval(interval)
                gamename=data["data"]["name"]
                gameid=data["gameid"]
                document.getElementById("game").innerText=gamename
                document.getElementById("userlisthost").innerText=data["data"]["usernickname"][0]+"(HOST)"
                document.getElementById("userlistuser").innerText=data["data"]["usernickname"][1]
                if(data["data"]["user"][0]==userid){
                    document.getElementById("page5").innerHTML=`
                        <input type="button" id="startgame" value="開始遊戲">
                    `
                    document.getElementById("startgame").onclick=function(){
                        socket.send(JSON.stringify({
                            "key": "startgame",
                            "gameid": gameid,
                        }))
                    }
                }
            }
        }else if(data["key"]=="startgame"){
            if(data["data"]["user"].includes(userid)){
                clearall(6)
                timemm=0
                timess=0
                document.getElementById("time").innerHTML=`00:00`
                time=setInterval(function(){
                    timess=timess+1
                    if(60<=timess){
                        timemm=timemm+1
                        timess=0
                    }
                    document.getElementById("time").innerHTML=`${String(timemm).padStart(2,"0")}:${String(timess).padStart(2,"0")}`
                },1000)
                document.getElementById("userlisthost").innerText="{黑子}  "+data["data"]["usernickname"][0]+"[你的回合]"
                document.getElementById("userlistuser").innerText=data["data"]["usernickname"][1]+"  {白子}"
                socket.send(JSON.stringify({
                    "key": "initgamefinish",
                    "gameid": gameid
                }))
            }
        }else if(data["key"]=="updategame"){
            if(data["data"]["user"].includes(userid)){
                game=data["data"]
                document.getElementById("page6").style.display="grid"
                document.getElementById("page6").innerHTML=``
                if(game["turn"]==0){
                    document.getElementById("userlisthost").innerText="{黑子}  "+data["data"]["usernickname"][0]+"[你的回合]"
                    document.getElementById("userlistuser").innerText=data["data"]["usernickname"][1]+"  {白子}"
                }else{
                    document.getElementById("userlisthost").innerText="{黑子}  "+data["data"]["usernickname"][0]
                    document.getElementById("userlistuser").innerText="[你的回合]"+data["data"]["usernickname"][1]+"  {白子}"
                }
                for(let i=0;i<15;i=i+1){
                    for(let j=0;j<15;j=j+1){
						let classlist=["gamebordertddiv"]

						if(i==0) classlist.push("bottomshort")
						if(i==14) classlist.push("topshort")
						if(j==0) classlist.push("rightshort")
						if(j==14) classlist.push("leftshort")
                        document.getElementById("page6").innerHTML+=`
							<div class="${classlist.join(" ")}" id="${i}_${j}">${
								(game["border"][i][j]=="B")?
									`<div class="black"></div>`:
										((game["border"][i][j]=="W")?
											`<div class="white"></div>`:
												``)
							}</div>
                        `
                    }
                }
				document.querySelectorAll(".gamebordertddiv").forEach(function(element){
					element.onclick=function(){
						let key=element.id
						if((game["turn"]==0&&game["user"][0]==userid)||(game["turn"]==1&&game["user"][1]==userid)){
							if(document.getElementById(key).querySelectorAll(".black,.white").length==0){
                                console.log(key)
                                let keylist=key.split("_")
                                game["border"][keylist[0]][keylist[1]]=game["turn"]==0?"B":"W"
                                console.log(game["border"][keylist[0]][keylist[1]])
                                console.log(game["turn"]==0)
								socket.send(JSON.stringify({
									"key": "updategame",
									"gameid": gameid,
                                    "border": game["border"]
								}))
							}else{
								alert("無法放置在目前位置")
							}
						}else{
							alert("不是您的回合")
						}
					}
				})
            }
        }else if(data["key"]=="endgame"){
            if(data["data"]["user"].includes(userid)){
                clearall(7)
                document.getElementById("userlist").innerHTML=`
                    <div id="userlisthost"></div>
                    <div id="userlistuser"></div>
                `
                clearInterval(time)
                document.getElementById("time").innerHTML=`${String(timemm).padStart(2,"0")}:${String(timess).padStart(2,"0")}`
                if(data["result"]=="tie"){
                    document.getElementById("page7").innerHTML=`
                        <div>勝利玩家: <span id="winner"></span></div>
                        <div>遊戲結果: <span id="result">平手</span></div>
                        <div><input type="button" id="contiune" value="繼續遊戲"></div>
                    `
                }else if(data["result"]=="B"){
                    document.getElementById("page7").innerHTML=`
                        <div>勝利玩家: <span id="winner">${data["data"]["usernickname"][0]}</span></div>
                        <div>遊戲結果: <span id="result">${userid==data["data"]["user"][0]?"贏":"輸"}</span></div>
                        <div><input type="button" id="contiune" value="繼續遊戲"></div>
                    `
                }else if(data["result"]=="W"){
                    document.getElementById("page7").innerHTML=`
                        <div>勝利玩家: <span id="winner">${data["data"]["usernickname"][1]}</span></div>
                        <div>遊戲結果: <span id="result">${userid==data["data"]["user"][1]?"贏":"輸"}</span></div>
                        <div><input type="button" id="contiune" value="繼續遊戲"></div>
                    `
                }
                document.getElementById("contiune").onclick=function(){
                    clearall(2)
                    document.getElementById("game").innerHTML=``
                    document.getElementById("userlist").innerHTML=`
                        <div id="userlisthost"></div>
                        <div id="userlistuser"></div>
                    `
                    document.getElementById("time").innerHTML=``
                    gameid=null
                    gamename=null
                    interval=null
                    time=null
                    game=null
                }
            }
        }
    }else{
        alert(data["data"])
    }
    console.log(data)
}

clearall(1)

document.getElementById("start").onclick=function(){
    socket.send(JSON.stringify({
        "key": "signin",
        "nickname": document.getElementById("inputnickname").value
    }))
}

document.getElementById("creategame").onclick=function(){
    clearall(3)
}

document.getElementById("joingame").onclick=function(){
    clearall(4)
    socket.send(JSON.stringify({
        "key": "getgamelist"
    }))
    interval=setInterval(function(){
        socket.send(JSON.stringify({
            "key": "getgamelist"
        }))
    },3000)
}

document.getElementById("create").onclick=function(){
    socket.send(JSON.stringify({
        "key": "creategame",
        "userid": userid,
        "name": document.getElementById("gamename").value
    }))
}
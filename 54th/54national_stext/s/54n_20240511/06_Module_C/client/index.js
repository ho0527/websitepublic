let userid=null
let nickname=null
let roomid=null
let roomname=null
let times=0
let timem=0
let call
let timeer

/* button onclick function START */
function signin(){
    websocket.send(JSON.stringify({
        "key": "signin",
        "name": document.getElementById("name").value
    }))
}

function createroom(){
    websocket.send(JSON.stringify({
        "key": "createroom",
        "userid": userid,
        "roomname": document.getElementById("roomname").value
    }))
}

function addroom(){
    clearall(4)
    websocket.send(JSON.stringify({
        "key": "getroomlist"
    }))
    call=setInterval(function(){
        websocket.send(JSON.stringify({
            "key": "getroomlist"
        }))
    },3000)
}

function addtoroom(roomid){
    websocket.send(JSON.stringify({
        "key": "addroom",
        "userid": userid,
        "roomid": roomid
    }))
}

function startgame(roomid){
    websocket.send(JSON.stringify({
        key: "startgame",
        roomid: roomid
    }))
}

function updateroom(roomid,ij,keyname){
    websocket.send(JSON.stringify({
        key: "updateroom",
        userid: userid,
        roomid: roomid,
        i: ij.split("_")[0],
        j: ij.split("_")[1],
        keyname: keyname
    }))
}

function cont(){
    clearall(2)
    document.getElementById("timeer").innerHTML=``
}
/* button onclick function END */

function clearall(key){
    for(let i=0;i<7;i=i+1){
        document.getElementById("page"+(i+1)).classList.add("display-none")
    }
    document.getElementById("page"+key).classList.remove("display-none")
}

let websocket=new WebSocket("ws://127.54.54.54:1022")

websocket.onopen=function(event){
    console.log("success")
}

websocket.onmessage=function(event){
	let data=JSON.parse(event["data"])
    if(data["success"]){
        let key=data["key"]
        if(key=="signin"){
            userid=data["data"]["userid"]
            nickname=data["data"]["name"]
            clearall(2)
            document.getElementById("nickname").innerHTML="玩家暱稱: "+nickname
        }else if(key=="createroom"){
            roomid=data["data"]["roomid"]
            roomname=data["data"]["roomname"]
            clearall(5)
            document.getElementById("page5").innerHTML=`
                ${roomname}<br>
                ${nickname}(建立者)<br>
                <input type="button" onclick="alert('玩家尚未滿人')" value="開始遊戲">
            `
        }else if(key=="getroomlist"){
            let roomdata=data["data"]
            document.getElementById("page4").innerHTML=``
            roomdata.sort(function(a,b){
                if(a.userlist.length>b.userlist.length){
                    return 1
                }else{
                    return -1
                }
            })
            for(let i=0;i<roomdata.length;i=i+1){
                document.getElementById("page4").innerHTML+=`
                    <input type="button" onclick="addtoroom(${roomdata[i]["id"]})" value="${roomdata[i]["roomname"]}(${roomdata[i]["userlist"].length}/2)">
                `
            }
        }else if(key=="addroom"){
            if(data["data"]["userlist"].includes(userid)){
                clearInterval(call)
                roomid=data["data"]["id"]
                roomname=data["data"]["roomname"]
                clearall(5)
                document.getElementById("page5").innerHTML=`
                    遊戲室名稱: ${roomname}<br>
                    <div class="flex">
                        <div>${data["data"]["usernicknamelist"][0]}(建立者)</div>
                        <div>${data["data"]["usernicknamelist"][1]}</div>
                    </div>
                    ${
                        (data["data"]["userlist"][0]==userid)?
                            (`<input type="button" onclick="startgame(${data["data"]["id"]})" value="開始遊戲">`):
                                ``
                    }
                `
            }
        }else if(key=="gamestart"){
            if(data["data"]["userlist"].includes(userid)){
                clearall(6)
                document.getElementById("page6").innerHTML=`
                    遊戲室名稱: ${roomname}<br>
                    <div class="flex">
                        <div>${data["data"]["usernicknamelist"][0]}(黑)(你的回合)</div>
                        <div>${data["data"]["usernicknamelist"][1]}(白)</div>
                    </div>
                    <div class="mainborder" id="border"></div>
                `

                for(let i=0;i<15;i=i+1){
                    for(let j=0;j<15;j=j+1){
                        let classlist=["borderdiv"]
                        
                        if(i==0) classlist.push("bottomborder")
                        if(i==14) classlist.push("topborder")
                        if(j==0) classlist.push("rightborder")
                        if(j==14) classlist.push("leftborder")

                        document.getElementById("border").innerHTML+=`
                            <div class="${classlist.join(" ")}" onclick="updateroom(${roomid},'${i}_${j}','${(data["data"]["turnishost"])?"B":"W"}')">
                            </div>
                        `
                    }
                }

                document.getElementById("timeer").innerHTML=`00:00`
                timeer=setInterval(function(){
                    times=times+1
                    if(times==60){
                        times=0
                        timem=timem+1
                    }
                    document.getElementById("timeer").innerHTML=`${String(timem).padStart(2,"0")+":"+String(times).padStart(2,"0")}`
                },1000)
            }
        }else if(key=="updateroom"){
            if(data["data"]["userlist"].includes(userid)){
                document.getElementById("page6").innerHTML=`
                    遊戲室名稱: ${roomname}<br>
                    <div class="flex">
                        ${
                            (data["data"]["turnishost"])?
                                (`
                                <div>${data["data"]["usernicknamelist"][0]}(黑)(你的回合)</div>
                                <div>${data["data"]["usernicknamelist"][1]}(白)</div>
                                `):
                                (`
                                <div>${data["data"]["usernicknamelist"][0]}(黑)</div>
                                <div>(你的回合)${data["data"]["usernicknamelist"][1]}(白)</div>
                                `)
                        }
                    </div>
                    <div class="mainborder" id="border"></div>
                `

                document.getElementById("border").innerHTML=``

                for(let i=0;i<15;i=i+1){
                    for(let j=0;j<15;j=j+1){
                        let classlist=["borderdiv"]
                        
                        // if(i==0) classlist.push("bottomborder")
                        // if(i==14) classlist.push("topborder")
                        // if(j==0) classlist.push("rightborder")
                        // if(j==14) classlist.push("leftborder")

                        document.getElementById("border").innerHTML+=`
                            <div class="${classlist.join(" ")}" onclick="updateroom(${roomid},'${i}_${j}','${(data["data"]["turnishost"])?"B":"W"}')">
                                <div class="${data["data"]["border"][i][j]}"></div>
                            </div>
                        `
                    }
                }
            }
        }else if(key=="endroom"){
            if(data["data"]["userlist"].includes(userid)){
                clearall(7)
                clearInterval(timeer)
                document.getElementById("timeer").innerHTML=`總時間: ${String(timem).padStart(2,"0")+":"+String(times).padStart(2,"0")}`
                document.getElementById("page7").innerHTML=`
                    遊戲室名稱: ${roomname}<br>
                    ${
                        (!data["tided"])?
                            (`<div>
                                勝利玩家: ${(data["data"]["turnishost"])?(data["data"]["usernicknamelist"][0]):(data["data"]["usernicknamelist"][1])}
                            </div>`):
                            (``)
                    }
                    
                    <div>
                        ${(data["data"]["turnishost"]&&(userid==data["data"]["userlist"][0]))?
                            ("贏"):
                                (!data["data"]["turnishost"]&&(userid==data["data"]["userlist"][1]))?
                                    ("贏"):
                                    (data["tided"]==true)?
                                        ("平局"):
                                        ("輸")
                        }
                    </div>
                    <input type="button" onclick="cont()" value="繼續遊戲">
                `
            }
        }
    }else{
        alert(data["data"])
    }
}

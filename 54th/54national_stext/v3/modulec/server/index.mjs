import{WebSocketServer} from "ws"

const wss=new WebSocketServer({
    host: "127.54.54.54",
    port: 1022,
    perMessageDeflate: false,
})

let user=[]
let room=[]

function checkWin(border){
    let returnvalue=-1

    // 檢查橫向
    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i][j+1]&&key==border[i][j+2]&&key==border[i][j+3]&&key==border[i][j+4]){
                return key
            }
        }
    }

    // 檢查縱向
    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j]&&key==border[i+2][j]&&key==border[i+3][j]&&key==border[i+4][j]){
                return key
            }
        }
    }

    // 檢查斜向（左上到右下）
    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j+1]&&key==border[i+2][j+2]&&key==border[i+3][j+3]&&key==border[i+4][j+4]){
                return key
            }
        }
    }

    // 檢查斜向（右上到左下）
    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j-1]&&key==border[i+2][j-2]&&key==border[i+3][j-3]&&key==border[i+4][j-4]){
                return key
            }
        }
    }

    // 如果棋盤已滿且無人獲勝
    if(returnvalue==-1){
        for(let i=0;i<15;i=i+1){
            for(let j=0;j<15;j=j+1){
                if(border[i][j]=="-"){
                    returnvalue=0
                    break
                }
            }
        }
    }

    // 若以上條件皆未滿足，則返回無人獲勝
    return returnvalue
}
process.on("SIGINT",function(){
    wss.clients.forEach(ws => ws.terminate())
    wss.close()
})

wss.on("connection",function connection(ws){
    ws.isAlive=true

    ws.on("error",console.error)
    ws.on("pong",function heartbeat(){
        this.isAlive=true
    })

    ws.on("close",function close(){
        console.log("disconnected")
    })

    ws.on("message",function message(dataStr){
        console.log("received "+dataStr)
        try{
            let data=JSON.parse(dataStr)
            let key=data["key"]

            if(key=="createplayer"){
                if(data["nickname"]&&!user.includes(data["nickname"])){
                    user.push(data["nickname"])
                    ws.send(JSON.stringify({
                        "key": "createuser",
                        "success": true,
                        "data": "新增成功",
                        "userid": user.indexOf(data["nickname"])
                    }))
                }else{
                    ws.send(JSON.stringify({
                        "key": "createuser",
                        "success": false,
                        "data": "玩家名稱重複或為空"
                    }))
                }
            }else if(key=="newroom"){
                let roomid=room.length
                room.push({
                    "roomid": roomid,
                    "name": data["name"],
                    "playing": false,
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
                    "userid": [data["userid"]],
                    "turnishost": true
                })
                ws.send(JSON.stringify({
                    "key": "newroom",
                    "success": true,
                    "data": "新增成功",
                    "roomid": roomid
                }))
                ws.send(JSON.stringify({
                    "key": "updatewaitroom",
                    "success": true,
                    "data":{
                        "name": room[roomid]["name"],
                        "hostid": room[roomid]["userid"][0],
                        "userid": room[roomid]["userid"],
                        "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                    }
                }))
            }else if(key=="getroomlist"){
                let roomtemp=[...room]

                ws.send(JSON.stringify({
                    "key": "getroomlist",
                    "success": true,
                    "data": roomtemp.sort(function(a,b){
                        if(a["userid"].length>b["userid"].length){
                            return 1
                        }else if(a["userid"].length<b["userid"].length){
                            return -1
                        }else{
                            return 0
                        }
                    })
                }))
            }else if(key=="joinroom"){
                let roomid=data["roomid"]

                if(room[roomid]["userid"].length==1){
                    room[roomid]["userid"].push(data["userid"])
                    ws.send(JSON.stringify({
                        "key": "joinroom",
                        "success": true,
                        "data": "加入成功",
                        "roomid": roomid,
                        "roomname": room[roomid]["name"]
                    }))
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "key": "updatewaitroom",
                            "success": true,
                            "data":{
                                "name": room[roomid]["name"],
                                "hostid": room[roomid]["userid"][0],
                                "userid": room[roomid]["userid"],
                                "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                            }
                        }))
                    })
                }else{
                    ws.send(JSON.stringify({
                        "key": "joinroom",
                        "success": false,
                        "data": "加入失敗，房間已經滿人",
                    }))
                }
            }else if(key=="startgame"){
                let roomid=data["roomid"]
                if(!room[roomid]["playing"]){
                    if(room[roomid]["userid"].length==2){
                        room[roomid]["playing"]=true
                        ws.send(JSON.stringify({
                            "key": "startgame",
                            "success": true,
                            "data": "開始成功"
                        }))
                        wss.clients.forEach(function(client){
                            client.send(JSON.stringify({
                                "key": "gameinit",
                                "success": true,
                                "data":{
                                    "name": room[roomid]["name"],
                                    "hostid": room[roomid]["userid"][0],
                                    "userid": room[roomid]["userid"],
                                    "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                                    "border": room[roomid]["border"],
                                    "turnishost": true
                                }
                            }))
                            client.send(JSON.stringify({
                                "key": "gameupdate",
                                "success": true,
                                "data":{
                                    "name": room[roomid]["name"],
                                    "hostid": room[roomid]["userid"][0],
                                    "userid": room[roomid]["userid"],
                                    "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                                    "border": room[roomid]["border"],
                                    "turnishost": true
                                }
                            }))
                        })
                    }else{
                        ws.send(JSON.stringify({
                            "key": "startgame",
                            "success": false,
                            "data": "開始失敗，房間尚未滿人",
                        }))
                    }
                }else{
                    ws.send(JSON.stringify({
                        "key": "startgame",
                        "success": false,
                        "data": "開始失敗，房間已經有人在玩",
                    }))
                }
            }else if(key=="gameupdate"){
                let roomid=data["roomid"]
                if(room[roomid]["playing"]){
                    room[roomid]["border"][data["x"]][data["y"]]=(room[roomid]["turnishost"]?"B":"W")
                    room[roomid]["turnishost"]=!room[roomid]["turnishost"]

                    // 連線判斷演算法
                    let winner=checkWin(room[roomid]["border"])

                    wss.clients.forEach(function(client){
                        if(winner==0){
                            /* skip */
                        }else if(winner==-1){
                            client.send(JSON.stringify({
                                "key": "gameend",
                                "success": true,
                                "data":{
                                    "name": room[roomid]["name"],
                                    "hostid": room[roomid]["userid"][0],
                                    "userid": room[roomid]["userid"],
                                    "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                                    "border": room[roomid]["border"],
                                    "turnishost": true,
                                    "result": "平局"
                                }
                            }))
                        }else{
                            client.send(JSON.stringify({
                                "key": "gameend",
                                "success": true,
                                "data":{
                                    "name": room[roomid]["name"],
                                    "hostid": room[roomid]["userid"][0],
                                    "userid": room[roomid]["userid"],
                                    "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                                    "border": room[roomid]["border"],
                                    "turnishost": true,
                                    "result": winner
                                }
                            }))
                        }
                        client.send(JSON.stringify({
                            "key": "gameupdate",
                            "success": true,
                            "data":{
                                "name": room[roomid]["name"],
                                "hostid": room[roomid]["userid"][0],
                                "userid": room[roomid]["userid"],
                                "username": [user[room[roomid]["userid"][0]],user[room[roomid]["userid"][1]]],
                                "border": room[roomid]["border"],
                                "turnishost": room[roomid]["turnishost"]
                            }
                        }))
                    })
                }else{
                    ws.send(JSON.stringify({
                        "key": "joinroom",
                        "success": false,
                        "data": "加入失敗，房間已經有人在玩",
                    }))
                }
            }else if(key=="close"){
                ws.terminate()
            }
        } catch(e){
            console.error(e)
        }
    })
})

let heartbeatInterval=setInterval(function ping(){
    wss.clients.forEach(function each(ws){
        if(ws.isAlive==false) return ws.terminate()

        ws.isAlive=false
        ws.ping()
    })
},5*1000)

wss.on("close",function close(){
    console.log("Shutting down the server...")
    clearInterval(heartbeatInterval)
})

console.log(`Websocket Server listen at ${wss.options.host}:${wss.options.port}`)

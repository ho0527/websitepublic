import{WebSocketServer} from "ws"

const wss = new WebSocketServer({
    host: "127.54.54.54",
    port: 1022,
    perMessageDeflate: false,
})

let user=[]
let room=[]

process.on("SIGINT",function (){
    wss.clients.forEach(ws => ws.terminate())
    wss.close()
})

wss.on("connection",function connection(ws){
    ws.isAlive = true

    ws.on("error",console.error)
    ws.on("pong",function heartbeat(){
        this.isAlive = true
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
                room.push({
                    "name": data["name"],
                    "playing": false,
                    "border": [],
                    "userid": [data["userid"]],
                    "turn": data["userid"]
                })
                ws.send(JSON.stringify({
                    "key": "newroom",
                    "success": true,
                    "data": "新增成功",
                    "roomid": room.length-1
                }))
            }else if(key=="getroomlist"){
                ws.send(JSON.stringify({
                    "key": "getroomlist",
                    "success": true,
                    "data": room.sort(function(a,b){
                        if(a.playing&&!b.playing){
                            return 1;
                        }else if(!a.playing&&b.playing){
                            return -1;
                        }else{
                            return 0;
                        }
                    })
                }))
            }else if(key=="joinroom"){
                if(!room[data["roomid"]]["playing"]){
                    room[data["roomid"]]["userid"].push(data["userid"])
                    room[data["roomid"]]["playing"]=true
                    ws.send(JSON.stringify({
                        "key": "joinroom",
                        "success": true,
                        "data": "加入成功",
                        "roomid": data["roomid"],
                    }))
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
        } catch (e){
            console.error(e)
        }
    })

    // ws.send(JSON.stringify({
    //     action: "hello",
    //     data: "world",
    // }))
})

let heartbeatInterval = setInterval(function ping(){
    wss.clients.forEach(function each(ws){
        if (ws.isAlive==false) return ws.terminate()

        ws.isAlive = false
        ws.ping()
    })
},5 * 1000)

wss.on("close",function close(){
    console.log("Shutting down the server...")
    clearInterval(heartbeatInterval)
})

console.log(`Websocket Server listen at ${wss.options.host}:${wss.options.port}`)

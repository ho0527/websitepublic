import {WebSocketServer} from 'ws';

let user=[]
let room=[]

function check(roomid,i,j){
    let border=room[roomid]["border"]
    let oldi=i
    let oldj=j
    // 右斜左
    if(border[i-2]&&border[i+2]&&(border[i][j]==border[i-1][j-1])&&(border[i][j]==border[i-2][j-2])&&(border[i][j]==border[i+1][j+1])&&(border[i][j]==border[i+2][j+2])){
        console.log("123456")
        return true
    }else if(border[i+1]&&border[i-3]&&(border[i][j]==border[i-1][j-1])&&(border[i][j]==border[i-2][j-2])&&(border[i][j]==border[i-3][j-3])&&(border[i][j]==border[i+1][j+1])){
        console.log("123456")
        return true
    }else if(border[i-4]&&(border[i][j]==border[i-1][j-1])&&(border[i][j]==border[i-2][j-2])&&(border[i][j]==border[i-3][j-3])&&(border[i][j]==border[i-4][j-4])){
        console.log("123456")
        return true
    }else if(border[i-1]&&border[i+3]&&(border[i][j]==border[i-1][j-1])&&(border[i][j]==border[i+3][j+3])&&(border[i][j]==border[i+1][j+1])&&(border[i][j]==border[i+2][j+2])){
        console.log("123456")
        return true
    }else if(border[i+4]&&(border[i][j]==border[i+4][j+4])&&(border[i][j]==border[i+3][j+3])&&(border[i][j]==border[i+1][j+1])&&(border[i][j]==border[i+2][j+2])){
        console.log("123456")
        return true
    }
    // 左斜右
    if(border[i+2]&&border[i-2]&&(border[i][j]==border[i+1][j-1])&&(border[i][j]==border[i+2][j-2])&&(border[i][j]==border[i-1][j+1])&&(border[i][j]==border[i-2][j+2])){
        console.log("12345")
        return true
    }else if(border[i-1]&&border[i+3]&&(border[i][j]==border[i+1][j-1])&&(border[i][j]==border[i+2][j-2])&&(border[i][j]==border[i+3][j-3])&&(border[i][j]==border[i-1][j+1])){
        console.log("12345")
        return true
    }else if(true&&border[i+4]&&(border[i][j]==border[i+1][j-1])&&(border[i][j]==border[i+2][j-2])&&(border[i][j]==border[i+3][j-3])&&(border[i][j]==border[i+4][j-4])){
        console.log("12345")
        return true
    }else if(border[i+1]&&border[i-3]&&(border[i][j]==border[i+1][j-1])&&(border[i][j]==border[i-3][j+3])&&(border[i][j]==border[i-1][j+1])&&(border[i][j]==border[i-2][j+2])){
        console.log("12345")
        return true
    }else if(true&&border[i-4]&&(border[i][j]==border[i-4][j+4])&&(border[i][j]==border[i-3][j+3])&&(border[i][j]==border[i-1][j+1])&&(border[i][j]==border[i-2][j+2])){
        console.log("12345")
        return true
    }
    // x
    if(border[i+2]&&border[i-2]&&(border[i][j]==border[i+1][j])&&(border[i][j]==border[i+2][j])&&(border[i][j]==border[i-1][j])&&(border[i][j]==border[i-2][j])){
        console.log("1234")
        return true
    }else if(border[i-1]&&border[i+3]&&(border[i][j]==border[i+1][j])&&(border[i][j]==border[i+2][j])&&(border[i][j]==border[i+3][j])&&(border[i][j]==border[i-1][j])){
        console.log("1234")
        return true
    }else if(true&&border[i+4]&&(border[i][j]==border[i+1][j])&&(border[i][j]==border[i+2][j])&&(border[i][j]==border[i+3][j])&&(border[i][j]==border[i+4][j])){
        console.log("1234")
        return true
    }else if(border[i+1]&&border[i-3]&&(border[i][j]==border[i-1][j])&&(border[i][j]==border[i-2][j])&&(border[i][j]==border[i-3][j])&&(border[i][j]==border[i+1][j])){
        console.log("1234")
        return true
    }else if(true&&border[i-4]&&(border[i][j]==border[i-1][j])&&(border[i][j]==border[i-2][j])&&(border[i][j]==border[i-3][j])&&(border[i][j]==border[i-4][j])){
        console.log("1234")
        return true
    }
    // x
    if((border[i][j]==border[i][j+1])&&(border[i][j]==border[i][j+2])&&(border[i][j]==border[i][j-1])&&(border[i][j]==border[i][j-2])){
        console.log("123")
        return true
    }else if((border[i][j]==border[i][j+1])&&(border[i][j]==border[i][j-3])&&(border[i][j]==border[i][j-1])&&(border[i][j]==border[i][j-2])){
        console.log("123")
        return true
    }else if((border[i][j]==border[i][j-4])&&(border[i][j]==border[i][j-3])&&(border[i][j]==border[i][j-1])&&(border[i][j]==border[i][j-2])){
        console.log("123")
        return true
    }else if((border[i][j]==border[i][j-1])&&(border[i][j]==border[i][j+3])&&(border[i][j]==border[i][j+1])&&(border[i][j]==border[i][j+2])){
        console.log("123")
        return true
    }else if((border[i][j]==border[i][j+4])&&(border[i][j]==border[i][j+3])&&(border[i][j]==border[i][j+1])&&(border[i][j]==border[i][j+2])){
        console.log("123")
        return true
    }

    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            if(border[i][j]=="-"){
                return false
            }
        }
    }

    return "tided"
}

const wss = new WebSocketServer({
    host: '127.54.54.54',
    port: 1022,
    perMessageDeflate: false,
});

process.on('SIGINT', function () {
    wss.clients.forEach(ws => ws.terminate());
    wss.close();
});

wss.on('connection', function connection(ws) {
    ws.isAlive = true;

    ws.on('error', console.error);
    ws.on('pong', function heartbeat() {
        this.isAlive = true;
    });

    ws.on('close', function close() {
        console.log('disconnected');
    });

    ws.on('message', function message(dataStr) {
        console.log('received ' + dataStr);
        try {
            let data = JSON.parse(dataStr);
            let key=data["key"]

            if(key=="signin"){
                if(!user.includes(data["name"])){
                    user.push(data["name"])
                    ws.send(JSON.stringify({
                        "key": "signin",
                        "success": true,
                        "data": {
                            "userid": user.length-1,
                            "name": user[user.length-1],
                        }
                    }))
                }else{
                    ws.send(JSON.stringify({
                        "key": "signin",
                        "success": false,
                        "data": "使用者已存在"
                    }))
                }
            }else if(key=="createroom"){
                room.push({
                    "id": room.length,
                    "roomname": data["roomname"],
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
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"],
                        ["-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"]
                    ],
                    "userlist": [data["userid"]],
                    "usernicknamelist": [user[data["userid"]]],
                    "turnishost": true
                })
                ws.send(JSON.stringify({
                    "key": "createroom",
                    "success": true,
                    "data": {
                        "roomid": room.length-1,
                        "roomname": room[room.length-1]["roomname"]
                    }
                }))
            }else if(key=="getroomlist"){
                ws.send(JSON.stringify({
                    "key": "getroomlist",
                    "success": true,
                    "data": room
                }))
            }else if(key=="addroom"){
                if(room[data["roomid"]]&&room[data["roomid"]].userlist.length==1){
                    room[data["roomid"]].userlist.push(data["userid"])
                    room[data["roomid"]].usernicknamelist.push(user[data["userid"]])
                    ws.send(JSON.stringify({
                        "key": "addroom",
                        "success": true,
                        "data": room[data["roomid"]]
                    }))
                    wss.clients.forEach(function(event){
                        event.send(JSON.stringify({
                            "key": "addroom",
                            "success": true,
                            "data": room[data["roomid"]]
                        }))
                    })
                }else{
                    ws.send(JSON.stringify({
                        "key": "addroom",
                        "success": false,
                        "data": "房間不存在或已經滿人"
                    }))
                }
            }else if(key=="startgame"){
                wss.clients.forEach(function(event){
                    event.send(JSON.stringify({
                        "key": "gamestart",
                        "success": true,
                        "data": room[data["roomid"]]
                    }))
                })
            }else if(key=="updateroom"){
                if(room[data["roomid"]]["border"][data["i"]][data["j"]]=="-"){
                    console.log(room[data["roomid"]]["turnishost"])
                    console.log(data["keyname"])
                    if((room[data["roomid"]]["turnishost"]&&room[data["roomid"]]["userlist"][0]==data["userid"])||(!room[data["roomid"]]["turnishost"]&&room[data["roomid"]]["userlist"][1]==data["userid"])){
                        room[data["roomid"]]["border"][data["i"]][data["j"]]=data["keyname"]
                        let checkdata=check(data["roomid"],[data["i"]],[data["j"]])
                        if(checkdata==false){
                            room[data["roomid"]]["turnishost"]=!room[data["roomid"]]["turnishost"]
                            wss.clients.forEach(function(event){
                                event.send(JSON.stringify({
                                    "key": "updateroom",
                                    "success": true,
                                    "data": room[data["roomid"]]
                                }))
                            })
                        }else{
                            wss.clients.forEach(function(event){
                                event.send(JSON.stringify({
                                    "key": "endroom",
                                    "success": true,
                                    "data": room[data["roomid"]],
                                    "tided": (checkdata=="tided")?true:false
                                }))
                            })
                        }
                    }else{
                        ws.send(JSON.stringify({
                            "key": "updateroom",
                            "success": false,
                            "data": "不是你的回合"
                        }))
                    }
                }else{
                    ws.send(JSON.stringify({
                        "key": "updateroom",
                        "success": false,
                        "data": "不能下在這邊喔"
                    }))
                }
            }
        } catch (e) {
            console.error(e);
        }
    });
});

let heartbeatInterval = setInterval(function ping() {
    wss.clients.forEach(function each(ws) {
        if (ws.isAlive === false) return ws.terminate();

        ws.isAlive = false;
        ws.ping();
    });
}, 5 * 1000);

wss.on('close', function close() {
    console.log('Shutting down the server...');
    clearInterval(heartbeatInterval);
});

console.log(`Websocket Server listen at ${wss.options.host}:${wss.options.port}`);

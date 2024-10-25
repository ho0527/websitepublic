import {WebSocketServer} from 'ws';

const wss = new WebSocketServer({
    host: '127.54.54.54',
    port: 1022,
    perMessageDeflate: false,
});

let user=[]
let game=[]

function checkwin(border){
    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i][j+1]&&key==border[i][j+2]&&key==border[i][j+3]&&key==border[i][j+4]){
                return key
            }
        }
    }

    for(let i=0;i<11;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j]&&key==border[i+2][j]&&key==border[i+3][j]&&key==border[i+4][j]){
                return key
            }
        }
    }

    for(let i=0;i<11;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j+1]&&key==border[i+2][j+2]&&key==border[i+3][j+3]&&key==border[i+4][j+4]){
                return key
            }
        }
    }

    for(let i=0;i<11;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j-1]&&key==border[i+2][j-2]&&key==border[i+3][j-3]&&key==border[i+4][j-4]){
                return key
            }
        }
    }

    for(let i=0;i<15;i=i+1){
        for(let j=0;j<15;j=j+1){
            let key=border[i][j]
            if(key=="-"){
                return 0
            }
        }
    }

    return -1
}

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
            if(data["key"]=="signin"){
                if(!user.includes(data["nickname"])){
                    user.push(data["nickname"])
                    ws.send(JSON.stringify({
                        "success": true,
                        "key": "signin",
                        "data": "新增成功",
                        "userid": user.length-1,
                        "nickname": user[user.length-1]
                    }));
                }else{
                    ws.send(JSON.stringify({
                        "success": false,
                        "key": "signin",
                        "data": "使用者已存在"
                    }));
                }
            }else if(data["key"]=="newroom"){
                game.push({
                    "id": game.length,
                    "name": data["roomname"],
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
                    "player": [data["userid"]],
                    "playername": [data["usernickname"]],
                    "turn": "B"
                })
                ws.send(JSON.stringify({
                    "success": true,
                    "key": "newroom",
                    "data": "新增成功",
                    "roomid": game.length-1,
                    "roomname": data["roomname"]
                }));
            }else if(data["key"]=="getroomlist"){
                let tempgame=[...game]
                tempgame.sort(function(a,b){
                    console.log(tempgame)
                    if(a["player"].length>b["player"].length){
                        return 1
                    }else{
                        return -1
                    }
                })
                ws.send(JSON.stringify({
                    "success": true,
                    "key": "getroomlist",
                    "data": tempgame
                }));
            }else if(data["key"]=="joinroom"){
                if(game[data["roomid"]]["player"].length==1){
                    game[data["roomid"]]["player"].push(data["userid"])
                    game[data["roomid"]]["playername"].push(data["usernickname"])
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "joinroom",
                            "game": game[data["roomid"]]
                        }));
                    })
                }else{
                    ws.send(JSON.stringify({
                        "success": false,
                        "key": "joinroom",
                        "data": "房間已滿人"
                    }));
                }
            }else if(data["key"]=="initgame"){
                wss.clients.forEach(function(client){
                    client.send(JSON.stringify({
                        "success": true,
                        "key": "initgame",
                        "gameid": data["roomid"],
                        "game": game[data["roomid"]]
                    }));
                })
            }else if(data["key"]=="updategame"){
                game[data["roomid"]]["border"]=data["border"]
                game[data["roomid"]]["turn"]=data["turn"]
                let winner=checkwin(data["border"])
                if(winner==0){
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "updategame",
                            "gameid": data["roomid"],
                            "game": game[data["roomid"]]
                        }));
                    })
                }else{
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "endgame",
                            "gameid": data["roomid"],
                            "game": game[data["roomid"]],
                            "winner": winner
                        }));
                    })
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

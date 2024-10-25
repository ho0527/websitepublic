import {WebSocketServer} from 'ws';

const wss = new WebSocketServer({
    host: '127.54.54.54',
    port: 1022,
    perMessageDeflate: false,
});

let user=[]
let game=[]

function checkwin(border){
    for(let i=0;i<border.length;i=i+1){
        for(let j=0;j<border.length;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i][j+1]&&key==border[i][j+2]&&key==border[i][j+3]&&key==border[i][j+4])
                return key
        }
    }
    for(let i=0;i<border.length-4;i=i+1){
        for(let j=0;j<border.length;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j]&&key==border[i+2][j]&&key==border[i+3][j]&&key==border[i+4][j])
                return key
        }
    }
    for(let i=0;i<border.length-4;i=i+1){
        for(let j=0;j<border.length;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j+1]&&key==border[i+2][j+2]&&key==border[i+3][j+3]&&key==border[i+4][j+4])
                return key
        }
    }
    for(let i=0;i<border.length-4;i=i+1){
        for(let j=0;j<border.length;j=j+1){
            let key=border[i][j]
            if(key!="-"&&key==border[i+1][j-1]&&key==border[i+2][j-2]&&key==border[i+3][j-3]&&key==border[i+4][j-4])
                return key
        }
    }
    for(let i=0;i<border.length;i=i+1){
        for(let j=0;j<border.length;j=j+1){
            let key=border[i][j]
            if(key=="-")
                return 0
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
                        "key": "signin",
                        "success": true,
                        "userid": user.length-1,
                        "nickname": data["nickname"]
                    }));
                }else{
                    ws.send(JSON.stringify({
                        "key": "signin",
                        "success": false,
                        "data": "玩家名稱重複。"
                    }));
                }
            }else if(data["key"]=="creategame"){
                game.push({
                    "id": game.length,
                    "gamename": data["gamename"],
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
                    "user": [data["userid"]],
                    "userlist": [user[data["userid"]]],
                    "turn": "B",
                    "time": [0,0]
                })
                ws.send(JSON.stringify({
                    "key": "creategame",
                    "success": true,
                    "gameid": game.length-1,
                    "gamename": data["gamename"]
                }));
            }else if(data["key"]=="getgamelist"){
                let tempgame=[...game]
                tempgame.sort(function(a,b){
                    if(a["user"].length>b["user"].length){
                        return 1
                    }else{
                        return -1
                    }
                })
                ws.send(JSON.stringify({
                    "key": "getgamelist",
                    "success": true,
                    "data": tempgame
                }));
            }else if(data["key"]=="joingame"){
                if(game[data["gameid"]]["user"].length==1){
                    game[data["gameid"]]["user"].push(data["userid"])
                    game[data["gameid"]]["userlist"].push(data["nickname"])
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "key": "joingame",
                            "success": true,
                            "data": game[data["gameid"]]
                        }));
                    })
                }else{
                    ws.send(JSON.stringify({
                        "key": "joingame",
                        "success": false,
                        "data": "遊戲室人數過多，請更換其他遊戲室。"
                    }));
                }
            }else if(data["key"]=="initgame"){
                wss.clients.forEach(function(client){
                    client.send(JSON.stringify({
                        "key": "initgame",
                        "success": true,
                        "data": game[data["gameid"]]
                    }));
                })
            }else if(data["key"]=="updategame"){
                game[data["gameid"]]["border"]=data["border"]
                game[data["gameid"]]["turn"]=data["turn"]
                game[data["gameid"]]["timer"]=data["timer"]
                let winner=checkwin(data["border"])
                if(winner==0){
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "key": "updategame",
                            "success": true,
                            "data": game[data["gameid"]]
                        }));
                    })
                }else{
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "key": "endgame",
                            "success": true,
                            "data": game[data["gameid"]],
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

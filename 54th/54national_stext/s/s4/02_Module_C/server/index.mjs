import {WebSocketServer} from "ws";

const wss = new WebSocketServer({
    host: "127.54.54.54",
    port: 1022,
    perMessageDeflate: false,
});

let user=[]
let game=[]

process.on("SIGINT", function () {
    wss.clients.forEach(ws => ws.terminate());
    wss.close();
});

wss.on("connection", function connection(ws) {
    ws.isAlive = true;

    ws.on("error", console.error);
    ws.on("pong", function heartbeat() {
        this.isAlive = true;
    });

    ws.on("close", function close() {
        console.log("disconnected");
    });

    ws.on("message", function message(dataStr) {
        console.log("received " + dataStr);
        try {
            let data = JSON.parse(dataStr);
            if(data["key"]=="signin"){
                if(!user.includes(data["nickname"])){
                    user.push(data["nickname"])
                    ws.send(JSON.stringify({
                        "success": true,
                        "key": "signin",
                        "data": user.length-1,
                    }))
                }else{
                    ws.send(JSON.stringify({
                        "success": false,
                        "key": "signin",
                        "data": "使用者已存在",
                    }))
                }
            }else if(data["key"]=="getgamelist"){
                ws.send(JSON.stringify({
                    "success": true,
                    "key": "getgamelist",
                    "data": game,
                }))
            }else if(data["key"]=="joingame"){
                if(game[data["gameid"]]&&game[data["gameid"]]["user"].length==1){
                    game[data["gameid"]]["user"].push(data["userid"])
                    game[data["gameid"]]["usernickname"].push(user[data["userid"]])
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "joingame",
                            "data": game[data["gameid"]],
                            "gameid": data["gameid"]
                        }))
                    })
                }else{
                    ws.send(JSON.stringify({
                        "success": false,
                        "key": "joingame",
                        "data": "房間已滿人"
                    }))
                }
            }else if(data["key"]=="creategame"){
                game.push({
                    "id": game.length,
                    "name": data["name"],
                    "user": [data["userid"]],
                    "usernickname": [user[data["userid"]]],
                    "turn": 0,
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
                    ]
                })
                ws.send(JSON.stringify({
                    "success": true,
                    "key": "creategame",
                    "data": game[game.length-1],
                    "gameid": game.length-1
                }))
            }else if(data["key"]=="startgame"){
                if(game[data["gameid"]]&&game[data["gameid"]]["user"].length==2){
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "startgame",
                            "data": game[data["gameid"]]
                        }))
                    })
                }else{
                    ws.send(JSON.stringify({
                        "success": false,
                        "key": "startgame",
                        "data": "房間尚未滿人"
                    }))
                }
            }else if(data["key"]=="initgamefinish"){
                wss.clients.forEach(function(client){
                    client.send(JSON.stringify({
                        "success": true,
                        "key": "updategame",
                        "data": game[data["gameid"]]
                    }))
                })
            }else if(data["key"]=="updategame"){
                let checkwin=-1
                game[data["gameid"]]["border"]=data["border"]
                let border=game[data["gameid"]]["border"]
                game[data["gameid"]]["turn"]=game[data["gameid"]]["turn"]==0?1:0
                for(let i=0;i<15;i=i+1){
                    for(let j=0;j<15;j=j+1){
                        let ch=border[i][j]
                        if(ch!="-"&&ch==border[i][j+1]&&ch==border[i][j+2]&&ch==border[i][j+3]&&ch==border[i][j+4]){
                            checkwin=ch
                        }
                    }
                }
                if(checkwin==-1){
                    for(let i=0;i<11;i=i+1){
                        for(let j=0;j<15;j=j+1){
                            let ch=border[i][j]
                            if(ch!="-"&&ch==border[i+1][j]&&ch==border[i+2][j]&&ch==border[i+3][j]&&ch==border[i+4][j]){
                                checkwin=ch
                            }
                        }
                    }
                }
                if(checkwin==-1){
                    for(let i=0;i<11;i=i+1){
                        for(let j=0;j<15;j=j+1){
                            let ch=border[i][j]
                            if(ch!="-"&&ch==border[i+1][j+1]&&ch==border[i+2][j+2]&&ch==border[i+3][j+3]&&ch==border[i+4][j+4]){
                                checkwin=ch
                            }
                        }
                    }
                }
                if(checkwin==-1){
                    for(let i=0;i<11;i=i+1){
                        for(let j=0;j<15;j=j+1){
                            let ch=border[i][j]
                            if(ch!="-"&&ch==border[i+1][j-1]&&ch==border[i+2][j-2]&&ch==border[i+3][j-3]&&ch==border[i+4][j-4]){
                                checkwin=ch
                            }
                        }
                    }
                }
                if(checkwin==-1){
                    for(let i=0;i<15;i=i+1){
                        for(let j=0;j<15;j=j+1){
                            let ch=border[i][j]
                            if(ch=="-"){
                                checkwin=0
                            }
                        }
                    }
                }
                if(checkwin==0){
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "updategame",
                            "data": game[data["gameid"]]
                        }))
                    })
                }else if(checkwin==-1){
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "endgame",
                            "result": "tie",
                            "data": game[data["gameid"]]
                        }))
                    })
                }else{
                    wss.clients.forEach(function(client){
                        client.send(JSON.stringify({
                            "success": true,
                            "key": "endgame",
                            "result": checkwin,
                            "data": game[data["gameid"]]
                        }))
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

wss.on("close", function close() {
    console.log("Shutting down the server...");
    clearInterval(heartbeatInterval);
});

console.log(`Websocket Server listen at ${wss.options.host}:${wss.options.port}`);

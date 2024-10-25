let userid=null
let roomid=null
let roomname=""
let nickname=""
let roomdata=""
let interval
let socket

function joinroom(roomid){
	socket.send(JSON.stringify({
		"key": "joinroom",
		"roomid": roomid,
		"userid": userid
	}))
}

function clearall(){
	document.getElementById("page1").style.display="none"
	document.getElementById("page2").style.display="none"
	document.getElementById("page3").style.display="none"
	document.getElementById("page4").style.display="none"
	document.getElementById("page5").style.display="none"
	document.getElementById("page6").style.display="none"
}


clearall()
document.getElementById("page1").style.display="block"

try{
	socket=new WebSocket("ws://127.54.54.54:1022/")

	socket.onopen=function(event){
		console.log("WebSocket連接成功")
	}

	socket.onmessage=function(event){
		let data=JSON.parse(event["data"])
		let key=data["key"]
		if(key=="createuser"){
			alert(data["data"])
			if(data["success"]){
				userid=data["userid"]
				nickname=document.getElementById("user").value
				clearall()
				document.getElementById("page2").style.display="block"
				document.getElementById("nickname").innerText=nickname
			}
		}else if(key=="newroom"){
			alert(data["data"])
			if(data["success"]){
				roomid=data["roomid"]
				clearall()
				document.getElementById("page5").style.display="block"
			}
		}else if(key=="getroomlist"){
			let row=data["data"]
			if(data["success"]){
				clearall()
				document.getElementById("page4").style.display="block"
				document.getElementById("page4").innerHTML=``
				for(let i=0;i<row.length;i=i+1){
					document.getElementById("page4").innerHTML=`
						${document.getElementById("page4").innerHTML}
						<input type="button" class="room" onclick="joinroom(${i})" value="${row[i]["name"]} (人數: ${(row[i]["playing"])?"2/2":"1/2"})">
					`
				}
			}
		}else if(key=="joinroom"){
			alert(data["data"])
			roomname=data["roomname"]
			if(data["success"]){
				roomid=data["roomid"]
				clearall()
				document.getElementById("page5").style.display="block"
				clearInterval(interval)
			}
		}else if(key=="room"){
			alert(data["data"])
			if(data["success"]){
				roomid=data["roomid"]
			}
		}else if(key=="startgame"){
			alert(data["data"])
			if(data["success"]){
				clearall()
				document.getElementById("page6").style.display="block"
			}
		}
		if(key=="updatewaitroom"){
			if(data["success"]){
				roomdata=data["data"]
				if(roomdata["userid"].includes(userid)){
					console.log(roomdata)
					document.getElementById("page5").innerHTML=`
						${(userid==roomdata["hostid"])?"<input type='button' id='startgame' value='開始遊戲'>":""}
						<div>${roomdata["name"]}</div>
						<div>${roomdata["username"][0]}(HOST)</div>
						<div>${roomdata["username"][1]??""}</div>
					`

					if(document.getElementById("startgame"))
						document.getElementById("startgame").onclick=function(){
							socket.send(JSON.stringify({
								"key": "startgame",
								"roomid": roomid
							}))
						}
				}
			}
		}
		console.log(event.data)
	}

	socket.onclose=function(event){
		console.log("WebSocket連接關閉")
	}

	socket.onerror=function(event){
		console.log("WebSocket出現錯誤",event)
	}
}catch(error){
	console.log("websocket出現錯誤")
	console.error(error)
}

document.getElementById("newuser").onclick=function(){
    socket.send(JSON.stringify({
        "key": "createplayer",
        "nickname": document.getElementById("user").value
    }))
}

document.getElementById("createroom").onclick=function(){
	clearall()
	document.getElementById("page3").style.display="block"
}

document.getElementById("newroom").onclick=function(){
	roomname=document.getElementById("roomname").value
    socket.send(JSON.stringify({
        "key": "newroom",
        "name": document.getElementById("roomname").value,
        "userid": userid
    }))
}

document.getElementById("joinroom").onclick=function(){
	clearall()
	document.getElementById("page4").style.display="block"
	socket.send(JSON.stringify({
		"key": "getroomlist"
	}))
	interval=setInterval(function(){
		socket.send(JSON.stringify({
			"key": "getroomlist"
		}))
	},3000)
}
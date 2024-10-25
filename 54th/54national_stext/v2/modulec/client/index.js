let userid=null
let roomid=null
let roomname=""
let nickname=""
let roomdata=""
let totaltime=""
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
	document.getElementById("page7").style.display="none"
}

clearall()
document.querySelectorAll(".dispaly-none").forEach(function(event){
	event.classList.remove("display-none")
})
document.getElementById("page1").style.display="block"

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
					<input type="button" class="room" onclick="joinroom(${row[i]["roomid"]})" value="${row[i]["name"]}(人數: ${(row[i]["userid"].length)}/2)">
				`
				console.log(row[i]["userid"])
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
				document.getElementById("page5").innerHTML=`
					${(userid==roomdata["hostid"])?"<input type='button' id='startgame' value='開始遊戲'>":""}
					<div>${roomdata["name"]}</div>
					<div>${roomdata["username"][0]}(HOST)</div>
					<div>${roomdata["username"][1]??""}</div>
				`

				if(document.getElementById("startgame"))
					document.getElementById("startgame").onclick=function(){
						console.log(roomid)
						socket.send(JSON.stringify({
							"key": "startgame",
							"roomid": roomid
						}))
					}
			}
		}
	}
	if(key=="gameinit"){
		if(data["success"]){
			roomdata=data["data"]
			if(roomdata["userid"].includes(userid)){
				let timerm=0
				let timers=0

				clearall()
				document.getElementById("page6").style.display="block"
				document.getElementById("page6").innerHTML=`
					<div>${roomdata["name"]}</div>
					<div id="timer">00:00</div>
					<div class="flex" id="userinfo">
						<div>${roomdata["username"][0]}(黑)[YOUR TURN]</div>
						<div>${roomdata["username"][1]}(白)</div>
					</div>
					<div class="gameborder" id="gameborder">
					</div>
				`

				// 計時器功能
				setInterval(function(){
					timers=timers+1
					if(timers>=60){
						timerm=timerm+1
						timers=0
					}
					totaltime=timerm.toString().padStart(2,"0")+":"+timers.toString().padStart(2,"0")
					document.getElementById("timer").innerHTML=totaltime
				},1000)
			}
		}
	}
	if(key=="gameupdate"){
		if(data["success"]){
			roomdata=data["data"]
			if(roomdata["userid"].includes(userid)){
				document.getElementById("gameborder").innerHTML=``

				document.getElementById("userinfo").innerHTML=`
					${
						(roomdata["turnishost"])?
							(`
								<div>${roomdata["username"][0]}(黑)[YOUR TURN]</div>
								<div>${roomdata["username"][1]}(白)</div>
							`):
							(`
								<div>${roomdata["username"][0]}(黑)</div>
								<div>${roomdata["username"][1]}(白)[YOUR TURN]</div>
							`)
					}
				`

				for(let i=0;i<15;i++){
					for(let j=0;j<15;j++){
						let classlist=["gamebordertddiv"]

						if(i==0) classlist.push("bottomshort")
						if(i==14) classlist.push("topshort")
						if(j==0) classlist.push("rightshort")
						if(j==14) classlist.push("leftshort")

						document.getElementById("gameborder").innerHTML=`
							${document.getElementById("gameborder").innerHTML}
							<div class="${classlist.join(" ")}" id="${i}_${j}">${
								(roomdata["border"][i][j]=="B")?
									`<div class="black"></div>`:
										((roomdata["border"][i][j]=="W")?
											`<div class="white"></div>`:
												``)
							}</div>
						`
					}
				}


				document.querySelectorAll(".gamebordertddiv").forEach(function(element){
					element.onclick=function(){
						let key=element.id
						if((roomdata["turnishost"]&&roomdata["userid"][0]==userid)||(!roomdata["turnishost"]&&roomdata["userid"][1]==userid)){
							if(document.getElementById(key).querySelectorAll(".black, .white").length==0){
								socket.send(JSON.stringify({
									"key": "gameupdate",
									"roomid": roomid,
									"x": parseInt(element.id.split("_")[0]),
									"y": parseInt(element.id.split("_")[1])
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
		}
	}

	if(key=="gameend"){
		if(data["success"]){
			roomdata=data["data"]
			if(roomdata["userid"].includes(userid)){
				clearall()
				document.getElementById("page7").style.display="block"
				document.getElementById
				document.getElementById("totaltime").innerText=totaltime
				document.getElementById("winner").innerText=(roomdata["result"]=="B")?
																roomdata["username"][0]:
																	(roomdata["result"]=="W")?
																		roomdata["username"][1]:
																		("")
				document.getElementById("result").innerText=(roomdata["result"]=="平局")?
																	("平局"):
																		(roomdata["result"]=="B"&&roomdata["userid"][0]==userid||(roomdata["result"]=="W"&&roomdata["userid"][1]==userid))?
																			("贏"):
																				("輸")

				document.getElementById("continue").onclick=function(){
					clearall()
					document.getElementById("page2").style.display="block"
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
let aside="close" // 設定aside為關閉
// 網址列解碼 START
let state=/state=([^&]+)/.exec(location.search) // main | search | album | aside
let text=/text=([^&]+)/.exec(location.search) // search專用
let albumid=/id=([^&]+)/.exec(location.search) // album專用
let playing=/playing=([^&]+)/.exec(location.search) // 是否再播放
let playlist=/playlist=([^&]+)/.exec(location.search) // 播放清單
let playindex=/playindex=([^&]+)/.exec(location.search) // 第幾首歌
let time=/time=([^&]+)/.exec(location.search) // 經過時間(s)
let volume=/volume=([^&]+)/.exec(location.search) // 音量
// 網址列解碼 END
let data

// 初始化網址列資料 START
if(!state){ state="main" }else{ state=state[1] }
if(!text){ text="" }else{ text=text[1] }
if(!albumid){ albumid="0" }else{ albumid=albumid[1] }
if(!playing){ playing="false" }else{ playing=playing[1] }
if(!playlist){ playlist="" }else{ playlist=playlist[1] }
if(!playindex){ playindex="0" }else{ playindex=playindex[1] }
if(!time){ time="0" }else{ time=time[1] }
if(!volume){ volume="0.3" }else{ volume=volume[1] }
albumid=parseInt(albumid)
playing=playing=="true"
playindex=parseInt(playindex)
time=parseInt(time)
volume=parseFloat(volume)
// 初始化網址列資料 END

function url(){
	document.getElementById("player").volume=volume
	history.pushState(null,null,"?state="+state+"&text="+text+"&id="+albumid+"&playing="+playing+"&playlist="+playlist+"&playindex="+playindex+"&time="+time+"&volume="+volume)
}

function main(){ // 主程式(起始)
	document.getElementById("main").innerHTML=`` // 清空主區域
	data["albums"].sort(function(a,b){ return a["title"].localeCompare(b["title"]) }) // 符合字典檔排序

	if(state!="main"){
		state="main"
		url()
		// setTimeout(url,300) // 等畫面跑完
	}

	for(let i=0;i<data["albums"].length;i=i+1){
		let albumartistlist=data["albums"][i]["album_artists"] // 演奏者名字列表
		let albumtitle=data["albums"][i]["title"] // 專輯標題
		let cover=data["albums"][i]["cover"] // 封面
		if(!isset(cover)){ cover="cover/default.png" }// 如果沒有封面就要用預設的
		let albumartist=albumartistlist.join(",") // 將陣列變成字串

		// 設定每張專輯的div
		document.getElementById("main").innerHTML=`
			${document.getElementById("main").innerHTML}
			<div class="album" id="${i}">
				<img src="${cover}" class="cover" draggable="false"><br>
				<div class="albumttext">
					<div class="albumtitle">${albumtitle}</div>
					<div class="albumartist">${albumartist}</div>
				</div>
			</div>
		`
	}
	// 專輯內文
	document.querySelectorAll(".album").forEach(function(event){
		event.onclick=function(){ // 偵測專輯是否被點擊
			albumid=event.id
			album()
		}
	})
}

// 專輯內文
function album(){
	let albumartistlist=data["albums"][albumid]["album_artists"]
	let albumtitle=data["albums"][albumid]["title"]
	let cover=data["albums"][albumid]["cover"]
	let albumartist=albumartistlist.join(",")
	let date=data["albums"][albumid]["attr"]["pubdate"] // 拿到上傳的時間
	let publicdate
	let totalmm=0 // MM
	let totalss=0 // SS
	let tracklength=data["albums"][albumid]["tracks"].length // 歌曲總數
	let albumdescription=data["albums"][albumid]["description"] // 專輯介紹

	document.getElementById("main").innerHTML=`` // 清空主區域
	if(!isset(cover)){ cover="cover/default.png" }
	if(!isset(date)){ publicdate="N/A" }
	else{ publicdate=date.split("-").join("/") }// 改成要求形式

	if(state!="album"){
		state="album"
		url()
	}

	for(let i=0;i<tracklength;i=i+1){
		// 判斷個專輯的時間並加總
		let time=data["albums"][albumid]["tracks"][i]["duration"].split(":")
		totalss=totalss+parseInt(time[1])
		if(totalss>=60){
			totalss=totalss-60
			totalmm=totalmm+1
		}
		totalmm=totalmm+parseInt(time[0])
	}

	// 印出結果
	document.getElementById("main").innerHTML=`
		<div class="top">
			<div class="menu">
				<input type="button" class="menubutton" id="goback" value="index"> > ${albumtitle}專輯詳細位置
			</div>
			<img src="${cover}" class="albumcover">
			<div class="albumtext title">專輯名稱:${albumtitle}</div>
			<div class="albumtext artist">演唱者:${albumartist}</div>
			<div class="albumtext publicdate">發布日期:${publicdate}</div>
			<div class="albumtext trackslengthandtime">
				歌曲總數:${tracklength}
				專輯總時長:${String(totalmm).padStart(2,"0")+":"+String(totalss).padStart(2,"0")}
			</div>
			<div class="albumtext albumdescription">專輯描述:${albumdescription}</div>
			<div class="albumplay icondiv" id="albumpaly"><img src="material/icon/play.svg" class="albumicon"></div>
		</div>
		<div class="albummain macossectiondiv" id="albummain">
			<div class="tracklist grid">
				<div class="tracklisttext no">序號</div>
				<div class="tracklisttext tracktitle">歌曲名稱</div>
				<div class="tracklisttext artists">演唱者</div>
				<div class="tracklisttext albumtitle">專輯名稱</div>
				<div class="tracklisttext time">歌曲時間</div>
				<div class="tracklisttext def">功能區</div>
			</div>
		</div>
	`

	for(let i=0;i<tracklength;i=i+1){
		let trackid=data["albums"][albumid]["tracks"][i]["id"] // 歌曲標題
		let tracktitle=data["albums"][albumid]["tracks"][i]["title"] // 歌曲標題
		let time=data["albums"][albumid]["tracks"][i]["duration"] // 歌曲時間
		let artists=data["albums"][albumid]["tracks"][i]["artists"].join(",") // 歌曲演唱者
		let path=data["albums"][albumid]["tracks"][i]["path"] // 歌曲路徑

		// 創建一個div放每個歌曲
		document.getElementById("albummain").innerHTML=`
			${document.getElementById("albummain").innerHTML}
			<div class="tracklist grid" id="${trackid}">
				<div class="tracklisttext no">${i+1}</div>
				<div class="tracklisttext tracktitle">${tracktitle}</div>
				<div class="tracklisttext artists">${artists}</div>
				<div class="tracklisttext albumtitle">${data["albums"][albumid]["title"]}</div>
				<div class="tracklisttext time">${time}</div>
				<div class="tracklisttext def"><input type="button" class="defbutton" data-id="${trackid}" value="+"></div>
			</div>
		`
	}

	tracklistedit() // 新增歌曲到播放清單

	document.getElementById("goback").onclick=function(){
		main() // 重呼叫(開啟主程式)
	}

	document.getElementById("albumpaly").onclick=function(){
		if(confirm("確定是否取代播放清單成此專輯?")){
			let list=[]
			for(let i=0;i<tracklength;i=i+1){
				let trackid=data["albums"][albumid]["tracks"][i]["id"] // 歌曲標題
				list.push(trackid)
			}
			playlist=list.join(",")
			playindex=0
			url()
		}
	}
}

function createaside(){
	let playlistsplit=playlist.split(",") // 播放列表

	state="aside"

	document.getElementById("aside").innerHTML=`
		<div class="asidelist macossectiondivy" id="playlist"></div>
		<div class="audioplay" id="playerfunction"></div>
	`

	// 創建標題
	document.getElementById("playlist").innerHTML=`
		${document.getElementById("playlist").innerHTML}
		<div class="list grid">
			<div class="tracklisttext no">序號</div>
			<div class="tracklisttext tracktitle">歌曲名稱</div>
			<div class="tracklisttext artists">演唱者</div>
			<div class="tracklisttext albumtitle">專輯名稱</div>
			<div class="tracklisttext time">歌曲時間</div>
			<div class="tracklisttext def">功能區</div>
		</div>
	`

	if(playlistsplit[0]!=""){
		let path=""
		for(let i=0;i<playlistsplit.length;i=i+1){
			let id=playlistsplit[i].split("_")
			let trackid=data["albums"][id[0]]["tracks"][id[1]]["id"]
			let tracktitle=data["albums"][id[0]]["tracks"][id[1]]["title"] // 歌曲標題
			let time=data["albums"][id[0]]["tracks"][id[1]]["duration"] // 歌曲時間
			let artists=data["albums"][id[0]]["tracks"][id[1]]["artists"].join(",") // 歌曲演唱者
			let divinnerhtml=``
			let error=false
			let listdivclasslist="list grid"

			if(i==playindex){ // 如果是該歌曲
				path=data["albums"][id[0]]["tracks"][id[1]]["path"] // 歌曲路徑
				listdivclasslist=listdivclasslist+" playing"
			}

			if(id[2]=="ER"){ // 偵測是否為不可撥放歌曲
				playindex=playindex+1
				error=true
				divinnerhtml=`
					<div class="playerror">此音樂已無法播放</div>
				`
			}else{
				listdivclasslist=listdivclasslist+" playlistsongdiv"
				divinnerhtml=`
					<div class="tracklisttext no">${i+1}.</div>
					<div class="tracklisttext tracktitle">${tracktitle}</div>
					<div class="tracklisttext artists">${artists}</div>
					<div class="tracklisttext albumtitle">${data["albums"][id[0]]["title"]}</div>
					<div class="tracklisttext time">${time}</div>
					<div class="tracklisttext def"><input type="button" class="defbutton" data-id="${trackid}" data-no="${i+1}" value="-"></div>
				`
			}

			// 創建一個div放每個歌曲
			document.getElementById("playlist").innerHTML=`
				${document.getElementById("playlist").innerHTML}
				<div class="${listdivclasslist}" data-id="${i}" data-error="${error}">
					${divinnerhtml}
				</div>
			`
		}

		tracklistedit() // 刪除歌曲

		document.getElementById("playerfunction").innerHTML=`
			<div>
				<div class="icondiv" id="back"><img src="material/icon/play-skip-back.svg" class="icon"></div>
				<div class="icondiv" id="playpause"><img src="" class="icon" id="playpauseicon"></div>
				<div class="icondiv" id="forward"><img src="material/icon/play-skip-forward.svg" class="icon"></div>
				<div class="icondiv" id="volume"><img src="material/icon/volume-high.svg" class="icon"></div>
			</div>
		`

		document.getElementById("player").src=path

		// 初始化圖片
		if(playing){
			document.getElementById("playpauseicon").src="material/icon/pause.svg"
			document.getElementById("player").play()
		}else{
			document.getElementById("playpauseicon").src="material/icon/play.svg"
			document.getElementById("player").pause()
		}

		// 倒退一首歌
		document.getElementById("back").onclick=function(){
			if(parseInt(playindex)-1>=0){
				playindex=parseInt(playindex)-1
			}
			createaside()
		}

		document.getElementById("playpause").onclick=function(){
			if(playing){
				document.getElementById("playpauseicon").src="material/icon/play.svg"
				document.getElementById("player").pause()
				playing=false
			}else{
				document.getElementById("playpauseicon").src="material/icon/pause.svg"
				document.getElementById("player").play()
				playing=true
			}
			url()
		}

		// 前進一首歌
		document.getElementById("forward").onclick=function(){
			if(parseInt(playindex)+1<=playlistsplit.length-1){
				playindex=parseInt(playindex)+1
			}
			createaside()
		}

		document.getElementById("volume").onclick=function(){
			// document.getElementById("volumerange")
		}

		document.getElementById("player").onerror=function(){
			alert("因此音樂無法撥放或損毀，將重新加載並自動撥放下一首音樂")
			let mainlist=[]
			for(let i=0;i<playlistsplit.length;i=i+1){
				if(i==parseInt(playindex)){
					mainlist.push(playlistsplit[i]+"_ER")
				}else{
					mainlist.push(playlistsplit[i])
				}
			}
			playlist=playlistsplit.join(",")
			playindex=parseInt(playindex)+1
			createaside()
		}

		document.getElementById("player").onended=function(){
			if(parseInt(playindex)+1<=playlistsplit.length-1){
				playindex=parseInt(playindex)+1
			}
			createaside()
		}

		// 點歌後可以自動撥放
		document.querySelectorAll(".playlistsongdiv").forEach(function(event){
			event.onclick=function(){
				if(event.dataset.error){
					playindex=event.dataset.id
					playing=true
					document.getElementById("playpauseicon").src="material/icon/pause.svg"
					document.getElementById("player").play()
					createaside()
				}else{
					alert("此音樂無法撥放")
				}
			}
		})
	}else{
		document.getElementById("list").innerHTML=`
			${document.getElementById("list").innerHTML}
			<div class="list warning">
				目前無歌曲
			</div>
		`
	}

	// 設定aside的開啟及關閉
	document.getElementById("openaside").onclick=function(){
		if(aside=="close"){
			document.getElementById("aside").style.width="55%"
			document.getElementById("openaside").style.left="55%"
			document.getElementById("openaside").value="<"
			document.getElementById("asidemask").style.display="block"
			aside="open"
			createaside()
		}else{
			document.getElementById("aside").style.width="0px"
			document.getElementById("openaside").style.left="0px"
			document.getElementById("openaside").value=">"
			document.getElementById("asidemask").style.display="none"
			aside="close"
			document.getElementById("aside").innerHTML=``
			main()
		}
	}

	url()
}

function search(title,artist,album){
	let count=0
	let searchdata=data

	// 解析搜尋條件
	let part=title.concat(artist,album).join(" ").split(" ")
	let term={
		title:[],
		artist:[],
		album:[],
	}

	let field="title" // 預設為標題(title)

	part.forEach(function(event){
		if(event.startsWith("title:")){
			field="title"
			term.title.push(event.substring(6)) // 移除 "title:"
		}else if(event.startsWith("artist:")){
			field="artist"
			term.artist.push(event.substring(7)) // 移除 "artist:"
		}else if(event.startsWith("album:")){
			field="album"
			term.album.push(event.substring(6)) // 移除 "album:"
		}else{
			// 如果不是以上指定的欄位搜尋，將該部分加入當前的搜尋欄位中
			term[field].push(event)
		}
	})


	document.getElementById("main").innerHTML=`
		<div class="counter" id="counter"></div>
		<div class="list macossectiondiv" id="searchlist">
			<div class="tracklist grid">
				<div class="tracklisttext no">序號</div>
				<div class="tracklisttext tracktitle">歌曲名稱</div>
				<div class="tracklisttext artists">演唱者</div>
				<div class="tracklisttext albumtitle">專輯名稱</div>
				<div class="tracklisttext time">歌曲時間</div>
				<div class="tracklisttext def">功能區</div>
			</div>
		</div>
	` // 把主區域消除及放入預設

	for(let i=0;i<searchdata["albums"].length;i=i+1){
		let track=searchdata["albums"][i]["tracks"]
		for(let j=0;j<track.length;j=j+1){
			let titlematch=false
			let artistmatch=false
			let albummatch=false

			// 檢查是否符合搜尋條件
			if(term.title.length>0){
				titlematch=term.title.some(function(term){ return track[j]["title"].match(new RegExp(term,"gi")) })
			}else{
				titlematch=true // 沒有 title 條件時，預設為 true
			}

			if(term.artist.length>0){
				artistmatch=term.artist.some(function(term){ return track[j]["artists"].join(",").match(new RegExp(term,"gi")) })
			}else{
				artistmatch=true // 沒有 artist 條件時，預設為 true
			}

			if(term.album.length>0){
				albummatch=term.album.some(function(term){ return searchdata["albums"][i]["title"].match(new RegExp(term,"gi")) })
			}else{
				albummatch=true // 沒有 album 條件時，預設為 true
			}

			if(titlematch&&artistmatch&&albummatch){
				let id=searchdata["albums"][i]["tracks"][j]["id"]
				let tracktitle = searchdata["albums"][i]["tracks"][j]["title"].replace(new RegExp("("+title.concat(artist,album).join(" ")+")","gi"),"<div class='searchresult'>$1</div>") // 替換成高亮
				let artists=searchdata["albums"][i]["tracks"][j]["artists"].join(",") // 歌曲作者
				let albumtitle=searchdata["albums"][i]["title"] // 專輯標題
				let time=searchdata["albums"][i]["tracks"][j]["duration"] // 歌曲時間

				document.getElementById("searchlist").innerHTML=`
					${document.getElementById("searchlist").innerHTML}
					<div class="tracklist grid" id=${id}>
						<div class="tracklisttext no">${count+1}</div>
						<div class="tracklisttext tracktitle">${tracktitle}</div>
						<div class="tracklisttext artists">${artists}</div>
						<div class="tracklisttext albumtitle">${albumtitle}</div>
						<div class="tracklisttext time">${time}</div>
						<div class="tracklisttext def"><input type="button" class="defbutton" data-id="${id}" value="+"></div>
					</div>
				`
				count=count+1
			}
		}
	}

	if(count>0){
		tracklistedit() // 新增歌曲到播放清單
	}else{
		document.getElementById("searchlist").innerHTML=`
			${document.getElementById("searchlist").innerHTML}
			<div class="list warning">
				查無歌曲
			</div>
		`
	}

	// 輸出結果筆數
	document.getElementById("counter").innerHTML=`
		結果:${count}筆
	`
}

function tracklistedit(key){
	document.querySelectorAll(".defbutton").forEach(function(event){
		event.onclick=function(){
			let id=event.dataset.id // 歌曲id

			if(event.value=="+"){ // 新增專輯

				// 判斷專輯是否存在
				let playlistsplit=playlist.split(",")
				if(playlistsplit.includes(id)){
					alert("該專輯以存在於撥放清單")
				}else{
					list.push(id)
					playlist=playlistsplit.join(",")
				}
				conlog("success add in the track list! trackid="+id,"green","15","bold")
			}else if(event.value=="-"){ // 刪除專輯
				if(playindex!=event.dataset.no||!playing){
					let playlistsplit=playlist.split(",")
					playlistsplit.splice(playlistsplit.indexOf(id),1) // 刪除資料
					playlist=playlistsplit.join(",")
					createaside()
					conlog("success delect in the track list! trackid="+id,"green","15","bold")
				}
			}else{ conlog("[ERROR]tracklistedit function event value error","red","15","bold") }

			url()
		}
	})
}

// 初始化aside START
document.getElementById("aside").style.width="0%"
document.getElementById("openaside").style.left="0%"
// 初始化aside END

// 讓我偷懶用自己的函式~>~
oldajax("GET","albumlist.json").onload=function(){
	data=JSON.parse(this.responseText) // 拿到data
	if(state=="main"){
		main() // 開始主程式
	}else if(state=="search"){
		document.getElementById("search").value=text
	}else if(state=="album"){
		album(albumid)
	}else if(state=="aside"){
		document.getElementById("aside").style.width="55%"
		document.getElementById("openaside").style.left="55%"
		document.getElementById("openaside").value="<"
		document.getElementById("asidemask").style.display="block"
		// document.getElementById("body").innerHTML=`
		//     ${document.getElementById("body").innerHTML}
		//     <div class="mask" id="asidemask"></div>
		// `
		aside="open"
		createaside()
	}else{ conlog("[ERROR]","red","15") }

	// 初始化歌曲/播放器
	document.getElementById("player").volume=volume

	let playlistsplit=playlist.split(",")

	if(playlistsplit[0]!=""){
		let path=""
		for(let i=0;i<playlistsplit.length;i=i+1){
			let id=playlistsplit[i].split("_")

			if(i==playindex){ // 如果是該歌曲
				path=data["albums"][id[0]]["tracks"][id[1]]["path"]
			}
		}

		document.getElementById("player").src=path
		document.getElementById("clearbutton").click()
		console.log("check")

		// 初始化圖片
		if(playing){
			document.getElementById("player").play()
		}else{
			document.getElementById("player").pause()
		}

		document.getElementById("player").onerror=function(){
			alert("因此音樂無法撥放或損毀，將重新加載並自動撥放下一首音樂")
			let mainlist=[]
			for(let i=0;i<playlistsplit.length;i=i+1){
				if(i==parseInt(playindex)){
					mainlist.push(playlistsplit[i]+"_ER")
				}else{
					mainlist.push(playlistsplit[i])
				}
			}
			playlist=playlistsplit.join(",")
			playindex=parseInt(playindex)+1
		}

		document.getElementById("player").onended=function(){
			if(parseInt(playindex)+1<=playlistsplit.length-1){
				playindex=parseInt(playindex)+1
			}
			createaside()
		}

		url()
	}
}

// 設定aside的開啟及關閉
document.getElementById("openaside").onclick=function(){
	if(aside=="close"){
		document.getElementById("aside").style.width="55%"
		document.getElementById("openaside").style.left="55%"
		document.getElementById("openaside").value="<"
		document.getElementById("asidemask").style.display="block"
		aside="open"
		createaside()
	}else{
		document.getElementById("aside").style.width="0%"
		document.getElementById("openaside").style.left="0%"
		document.getElementById("openaside").value=">"
		document.getElementById("asidemask").style.display="none"
		aside="close"
		document.getElementById("aside").innerHTML=``
		main()
	}
}

document.getElementById("search").oninput=function(){
	let value=document.getElementById("search").value
	let title=[]
	let artist=[]
	let album=[]

	if(value.match(new RegExp("title:","gi"))){
		valuetemp=value.split("title:")
		title.push(valuetemp[1])
	}

	if(value.match(new RegExp("artist:","gi"))){
		valuetemp=value.split("artist:")
		artist.push(valuetemp[1])
	}

	if(value.match(new RegExp("album:","gi"))){
		valuetemp=value.split("album:")
		album.push(valuetemp[1])
	}

	if(value.length>=3){
		search(title,artist,album)
	}else{
		main()
	}
}

document.onkeydown=function(event){
	if(event.key=="ArrowRight"){ // 向后跳转5秒
		event.preventDefault()
		document.getElementById("player").currentTime=document.getElementById("player").currentTime+5
	}
	if(event.key=="ArrowLeft"){ // 向前跳转5秒
		event.preventDefault()
		document.getElementById("player").currentTime=document.getElementById("player").currentTime-5
	}
	if(event.key==" "){ // 暫停/開始
		event.preventDefault()
		if(playing){
			document.getElementById("player").pause()
			if(state=="aside"){ document.getElementById("playpauseicon").src="material/icon/pause.svg" }
			playing=false
		}else{
			document.getElementById("player").play()
			if(state=="aside"){ document.getElementById("playpauseicon").src="material/icon/play.svg" }
			playing=true
		}
		url()
	}
}

startmacossection() // 滾動條優化
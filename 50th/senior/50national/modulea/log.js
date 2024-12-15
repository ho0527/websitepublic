if(!weblsget("50nationalmodulea-token")){ href("index.html") }

if(4<=weblsget("50nationalmodulea-permission")){
	innerhtml("#navigationbar",`
		<div class="navigationbarright">
			<input type="button" class="navigationbarbutton" onclick="location.href='index.html'" value="首頁">
			<input type="button" class="navigationbarbutton" onclick="location.href='album.html'" value="專輯列表">
			<input type="button" class="navigationbarbutton" onclick="location.href='music.html'" value="音樂列表">
			<input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='log.html'" value="伺服器紀錄">
			<input type="button" class="navigationbarbutton" onclick="location.href='user.html'" value="使用者管理">
			<input type="button" class="navigationbarbutton" id="signout" value="登出">
		</div>
	`)
}else{
	href("signin.html")
}

ajax("GET",AJAXURL+"getlog",function(event,data){
	if(data["success"]){
		let row=data["data"]

		for(let i=0;i<row.length;i=i+1){
			innerhtml("#maintable",`
				<tr>
					<td class="td textcenter">${String(row[i]["id"]).padStart(3,"0")}</td>
					<td class="td textcenter">${row[i]["userid"]}</td>
					<td class="td textcenter">${row[i]["move"]}</td>
					<td class="td textcenter">${row[i]["movetime"]}</td>
				</tr>
			`)
		}
	}else{
		alert(ERRORMESSAGE[data["data"]])
		href("album.html")
	}
},null,[
	["Authorization","Bearer "+weblsget("50nationalmodulea-token")]
])

onclick("#signout",function(element,event){
	ajax("POST",AJAXURL+"signout",function(evennt,data){
		if(data["success"]){
			weblsset("50nationalmodulea-userid",null)
			weblsset("50nationalmodulea-permission",null)
			weblsset("50nationalmodulea-token",null)
			href("index.html")
		}else{
			alert(ERRORMESSAGE[data["data"]])
			weblsset("50nationalmodulea-userid",null)
			weblsset("50nationalmodulea-permission",null)
			weblsset("50nationalmodulea-token",null)
			href("index.html")
		}
	},null,[
		["Authorization","Bearer "+weblsget("50nationalmodulea-token")]
	])
})
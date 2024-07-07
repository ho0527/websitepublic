if(!weblsget("50nationalmodulea-token")){ href("index.html") }

if(4<=weblsget("50nationalmodulea-permission")){
	innerhtml("#navigationbar",`
		<div class="navigationbarleft">
			<input type="button" class="navigationbarbutton" id="newalbum" value="新增專輯">
		</div>
		<div class="navigationbarright">
			<input type="button" class="navigationbarbutton" onclick="location.href='index.html'" value="首頁">
			<input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='album.html'" value="專輯列表">
			<input type="button" class="navigationbarbutton" onclick="location.href='music.html'" value="音樂列表">
			<input type="button" class="navigationbarbutton" onclick="location.href='log.html'" value="伺服器紀錄">
			<input type="button" class="navigationbarbutton" onclick="location.href='user.html'" value="使用者管理">
			<input type="button" class="navigationbarbutton" id="signout" value="登出">
		</div>
	`)
}else{
	innerhtml("#navigationbar",`
		<div class="navigationbarleft">
			<input type="button" class="navigationbarbutton" id="newalbum" value="新增專輯">
		</div>
		<div class="navigationbarright">
			<input type="button" class="navigationbarbutton" onclick="location.href='index.html'" value="首頁">
			<input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='album.html'" value="專輯列表">
			<input type="button" class="navigationbarbutton" onclick="location.href='music.html'" value="音樂列表">
			<input type="button" class="navigationbarbutton" id="signout" value="登出">
		</div>
	`)
}

ajax("GET",AJAXURL+"getalbumlist",function(event,data){
	if(data["success"]){
		console.log(data)
	}else{
		alert(ERRORMESSAGE[data["data"]])
	}
},null,[
	["Authorization","Bearer "+weblsget("50nationalmodulea-token")]
])

onclick("#newalbum",function(element,event){
	lightbox(null,"lightbox",function(){
		return `
			<div class="adminfoodtitle">新增專輯</div>
			<div class="flex">
				<div class="textleft inputdiv" id="namediv">
					<div class="textlabel light">餐點名稱:</div>
					<div class="input light">
						<input type="text" id="name" value="">
					</div>
					<div class="text large bold error errordiv" id="nameerror"></div>
				</div>
				<div class="textleft inputdiv" id="pricediv">
					<div class="textlabel light">價格:</div>
					<div class="input light">
						<input type="number" id="price" value="">
					</div>
					<div class="text large bold error errordiv" id="usernameerror"></div>
				</div>
			</div>
			<div class="textcenter">
				<input type="button" class="button light" id="close" value="取消">
				<input type="button" class="button light" id="submit" value="完成">
			</div>
		`
	},"close")

	onclick("#submit",function(element,event){
		ajax("POST",AJAXURL+"newablem",function(evennt,data){
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
		},formdata([
			["title",getvalue("title")],
			["publisher",getvalue("publisher")],
			["publicdate",getvalue("publicdate")],
			["description",getvalue("description")],
			["albumartist",getvalue("albumartist")],
			["cover",""]
		]),[
			["Authorization","Bearer "+weblsget("50nationalmodulea-token")]
		])
	})
})

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
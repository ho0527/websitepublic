let id=getget("id")

// 輪播器
function carousel(id,carouselimageclass=".carouseimage",leftrightcontroller=true,indexcontroller="index",carouseltime=1500){
	if(domgetid(id)){
		if(typeof leftrightcontroller=="boolean"){
			if(indexcontroller=="none"||indexcontroller=="index"||indexcontroller=="image"){
				let count=0
				let index=0
				let totalindex=domgetall(carouselimageclass).length

				// change image
				function change(){
					index=(index+1)%totalindex

					// 將所有圖片隱藏
					style(carouselimageclass,[
						["display","none"]
					])

					// 將index圖片顯示
					style(domgetall(carouselimageclass)[index],[
						["display","block"]
					])

					count=0
				}

				change()
				setInterval(function(){
					count=count+1
					if(carouseltime/10<=count){ change() } // 換圖片
				},10)
			}else{
				console.error("[KEYTYPEIN ERROR]function carousel error: indexcontroller parameter KEYTYPEIN error(must be none|index|image)")
			}
		}else{
			console.error("[KEYTYPEIN ERROR]function carousel error: leftrightcontroller is not boolean")
		}
	}else{
		console.error("[DOM_NOTFOUND ERROR]function carousel error: dom id not found")
	}
}

if(!id){ location.href="index.html" }

ajax("GET",AJAXURL+"house/"+id,function(event,data){
	if(data["success"]){
		let image=``

		for(let i=0;i<data["data"]["image"].length;i=i+1){
			image=`
				${image}
				<img src="${data["data"]["image"][i]}" class="carouseimage image">
			`
		}

		innerhtml("#main",`
			<div class="housedivcarousel" id="carousel">${image}</div>
			<div class="housedivtitle">${data["data"]["title"]}</div>
			<div class="housedivdescription">
				<span class="noselect">description:</span> ${data["data"]["description"]}
			</div>
			<div class="housedivprice">
				<span class="noselect">價格:</span> ${data["data"]["price"]}
				<span class="noselect"> / </span>
				<span class="noselect">單價:</span> ${data["data"]["price"]/data["data"]["square"]}
			</div>
			<div class="housedivsquareroom">
				<span class="noselect">坪數:</span> ${data["data"]["square"]}
				<span class="noselect"> / </span>
				<span class="noselect">房數:</span> ${data["data"]["room"]}
			</div>
			<div class="housedivfloor">
				<span class="noselect">樓層:</span> ${data["data"]["floor"]}
				<span class="noselect"> / </span>
				<span class="noselect">總樓層:</span> ${data["data"]["total_floor"]}
				</div>
			<div class="housedivage">
				<span class="noselect">屋齡:</span> ${data["data"]["age"]}
			</div>
			<div class="housedivaddress">
				<span class="noselect">地址:</span> ${data["data"]["address"]}
			</div>
			<div class="housedivnickname">
				<span class="noselect">發表者:</span> ${data["data"]["publisher"]["nickname"]}
			</div>
			<div class="housedivemail">
				<span class="noselect">email:</span> ${data["data"]["publisher"]["email"]}
			</div>
			<div class="housedivpublishdate">
				<span class="noselect">發表日期:</span> ${data["data"]["published_at"].split("T").join(" ")}
			</div>
		`,false)

		carousel("carousel") // 開啟輪播器功能
	}else{
		alert(ERRORMESSAGE[data["message"]])
	}
})

onclick("#signout",function(element,event){
	ajax("POST",AJAXURL+"user/logout",function(event,data){
		if(data["success"]){
			alert("登出成功")
			weblsset("51nationalmoduled-userid",null)
			weblsset("51nationalmoduled-permission",null)
			weblsset("51nationalmoduled-token",null)
			href("index.html")
		}else{
			alert(ERRORMESSAGE[data["message"]])
		}
	},[],[
		["Authorization","Bearer "+weblsget("51nationalmoduled-token")]
	])
})
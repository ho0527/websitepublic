let page=0
let size=10
let sortby="title"
let sorttype="asc"

function main(){
	ajax("GET",AJAXURL+"api/v1/games?page="+page+"&size="+size+"&sortBy="+sortby+"&sortDir="+sorttype,function(event,data){
		let total=data["totalElements"]
		for(let i=0;i<data["content"].length;i=i+1){
			let pictureurl="material/picture/default.jpg"

			// 不會接image
			if(data["content"][i]["thumbnail"]){
				pictureurl=data["content"][i]["thumbnail"]
			}

			// game div
			innerhtml("#main",`
				<div class="game grid" data-slug="${data["content"][i]["slug"]}">
					<div class="title">${data["content"][i]["title"]}</div>
					<div class="author">by ${data["content"][i]["author"]}</div>
					<div class="description">${data["content"][i]["description"]}</div>
					<div class="scorecount">score submit: ${data["content"][i]["scoreCount"]}</div>
					<div class="imagediv"><img src="${pictureurl}" class="image"></div>
				</div>
			`)
		}
		domgetid("gamecount").innerHTML=`${total}`

		domgetall(".game").forEach(function(event){
			event.onclick=function(){
				location.href="game.html?slug="+event.dataset.slug
			}
		})
	})
	page=page+1
}

function clearselectbutton(){
	domgetall(".indexfunctionbutton").forEach(function(event){
		event.classList.remove("bluebuttonselect")
	})
}

// show signin/signup || signout button
if(isset(weblsget("worldskill2022MDtoken"))){
	domgetid("navigationbarright").innerHTML=`
		<a href="profile.html" class="a navigationbara">${weblsget("worldskill2022MDusername")} profile</a>
		<input type="button" class="navigationbarbutton" id="signout" value="Sign Out">
	`

	// logout
	domgetid("signout").onclick=function(){
		ajax("POST",AJAXURL+"api/v1/auth/signout",function(event,data){
			if(data["status"]=="success"){
				weblsset("worldskill2022MDtoken",null)
				weblsset("worldskill2022MDusername",null)
				location.href="signout.html"
			}else{
				alert(data["message"])
				if(data["message"]=="invalid token"){
					weblsset("worldskill2022MDtoken",null)
					weblsset("worldskill2022MDusername",null)
					location.href="index.html"
				}
			}
		},null,[
			["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
		])
	}
}else{
	domgetid("navigationbarright").innerHTML=`
		<input type="button" class="navigationbarbutton" onclick="location.href='signinsignup.html?key=signup'" value="Sign Up">
		<input type="button" class="navigationbarbutton" onclick="location.href='signinsignup.html?key=signin'" value="Sign In">
	`
}

if(!isset(weblsget("worldskill2022MDindexsortby"))){
	weblsset("worldskill2022MDindexsortby","title")
}

if(!isset(weblsget("worldskill2022MDindexsorttype"))){
	weblsset("worldskill2022MDindexsorttype","asc")
}

clearselectbutton()
domgetid(weblsget("worldskill2022MDindexsortby")).classList.add("bluebuttonselect")
domgetid(weblsget("worldskill2022MDindexsorttype")).classList.add("bluebuttonselect")
sortby=weblsget("worldskill2022MDindexsortby")
sorttype=weblsget("worldskill2022MDindexsorttype")


domgetall(".indexfunctionbutton").forEach(function(event){
	event.onclick=function(){
		if(event.id=="asc"||event.id=="desc"){
			weblsset("worldskill2022MDindexsorttype",event.id)
			sorttype=event.id
		}else{
			weblsset("worldskill2022MDindexsortby",event.id)
			sortby=event.id
		}
		clearselectbutton()
		domgetid(weblsget("worldskill2022MDindexsortby")).classList.add("bluebuttonselect")
		domgetid(weblsget("worldskill2022MDindexsorttype")).classList.add("bluebuttonselect")
		domgetid("main").innerHTML=``
		page=page-1
		main()
	}
})


main()

document.onscroll=function(){
	if((window.innerHeight+Math.round(window.scrollY))>=document.body.offsetHeight){
		setTimeout(main,500)
	}
}
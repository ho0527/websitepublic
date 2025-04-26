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

ajax("GET",AJAXURL+"api/v1/users/"+weblsget("worldskill2022MDusername"),async function(event,data){
	console.log(data)
	// navbar
	domgetid("navigationbartitle2").innerHTML=`
		(User Profile: ${weblsget("worldskill2022MDusername")})
	`

	// 製作的遊戲
	for(let i=0;i<data["authoredGames"].length;i=i+1){
		let request=await fetch(AJAXURL+"api/v1/games/"+data["authoredGames"][i]["slug"])
		let data2=await request.json()
		let pictureurl=data2["thumbnail"]??"material/picture/default.jpg"

		// game div
		innerhtml("#profilegamediv",`
			<div class="game profilegame grid" data-slug="${data2["slug"]}">
				<div class="title">${data2["title"]}</div>
				<div class="description">${data2["description"]}</div>
				<div class="scorecount">score submit: ${data2["scoreCount"]}</div>
				<div class="imagediv"><img src="${pictureurl}" class="image"></div>
				<div class="managergamebuton" data-slug="${data2["slug"]}">Manager Game</div>
			</div>
		`)
	}
	onclick(".managergamebuton",function(element,event){
		event.stopPropagation()
		location.href="managergame.html?slug="+element.dataset.slug
	})

	data["highscores"].sort(function(a,b){
		if(b["game"]["title"].toLowerCase()<=a["game"]["title"].toLowerCase()){
			return 1
		}else{
			return -1
		}
	})

	for(let i=0;i<data["highscores"].length;i=i+1){
		innerhtml("#profilehightscore",`
			<div class="profilehightscorediv">
				<input type="button" class="buttonghost" onclick="location.href='game.html?slug=${data["highscores"][i]["game"]["slug"]}'" value="${data["highscores"][i]["game"]["title"]}">
				<div>${data["highscores"][i]["score"]}</div>
			</div>
		`)
	}
},null,[
	["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
])

domgetid("usernamelink").innerHTML=`
	${weblsget("worldskill2022MDusername")} profile
`

if(!isset(weblsget("worldskill2022MDtoken"))){
	location.href="signinsignup.html?key=signin"
}
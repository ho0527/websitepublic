let slug=getget("slug")

if(slug){
	function leaderboard(){
		ajax("GET",AJAXURL+"api/v1/games/"+slug+"/scores",function(event,data){
			let username=false
			let usercheck=false

			domgetid("gameleaderboard").innerHTML=``

			if(weblsget("worldskill2022MDusername")){
				username=weblsget("worldskill2022MDusername")
			}

			for(let i=0;i<Math.min(10,data["scores"].length);i=i+1){
				if(username==data["scores"][i]["username"]&&username){
					domgetid("gameleaderboard").innerHTML=`
						${domgetid("gameleaderboard").innerHTML}
						<tr class="highlightuser">
							<td class="gamenotd">#${i+1}</td>
							<td class="gameusernametd">${data["scores"][i]["username"]}</td>
							<td class="gamescoretd">${data["scores"][i]["score"]}</td>
						</tr>
					`
					usercheck=true
				}else{
					domgetid("gameleaderboard").innerHTML=`
						${domgetid("gameleaderboard").innerHTML}
						<tr class="gametr">
							<td class="gamenotd">#${i+1}</td>
							<td class="gameusernametd">${data["scores"][i]["username"]}</td>
							<td class="gamescoretd">${data["scores"][i]["score"]}</td>
						</tr>
					`
				}
			}

			if(!usercheck){
				for(let i=0;i<data["scores"].length;i=i+1){
					if(username==data["scores"][i]["username"]&&username){
						domgetid("gameleaderboard").innerHTML=`
							${domgetid("gameleaderboard").innerHTML}
							<tr class="highlightuser">
								<td class="gamenotd"></td>
								<td class="gameusernametd">${data["scores"][i]["username"]}</td>
								<td class="gamescoretd">${data["scores"][i]["score"]}</td>
							</tr>
						`
						usercheck=true
					}
				}
			}
		})
	}

	ajax("GET",AJAXURL+"api/v1/games/"+slug,function(event,data){
		console.log(data)
		domgetid("navigationbartitle2").innerHTML=`
			(Game: ${data["title"]})
		`
		domgetid("gamedescription").innerHTML=`
			${data["description"]}
		`

		domgetid("game").innerHTML=`
			<iframe src="${data["gamePath"]}/" class="iframe" title="這是遊戲"></iframe>
		`

		// 回傳分數
		getmessage(function(element,event){
			if(event.data.score&&window.confirm("Are you sure you want to submit your score?")){
				ajax("POST","/backend/worldskill2022modulec/api/v1/games/"+slug+"/scores",function(event,data){
					if(data["status"]=="success"){
						location.reload()
					}else{
						alert(data["message"])
					}
				},JSON.stringify({
					"score": event.data.score
				}),[
					["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
				])
			}
		})
	})

	leaderboard()
	setInterval(leaderboard,5000)

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
}else{
	href("index.html")
}
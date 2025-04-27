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
		if(data["author"]==weblsget("worldskill2022MDusername")){
			domgetid("navigationbartitle2").innerHTML=`
				(Game: ${data["title"]})
			`
			domgetid("gamedescription").innerHTML=`
				${data["description"]}
			`

			domgetid("game").innerHTML=`
				<div class="game grid" data-slug="${data["content"][i]["slug"]}">
					<input type="text" class="title" id="title" value="${data["content"][i]["title"]}">
					<div class="author">by ${data["content"][i]["author"]}</div>
					<textarea class="description" id="description">${data["content"][i]["description"]}</textarea>
					<div class="scorecount">score submit: ${data["content"][i]["scoreCount"]}</div>
					<div class="imagediv"><img src="${pictureurl}" class="image"></div>
				</div>

				<div>
					<input type="button" class="uploadbutton" id="uploadbutton" value="upload new version">
					<input type="button" class="deletebutton" id="deletebutton" value="delete">
					<input type="file" id="zipfile" accept=".zip">
				</div>
			`

			onclick("#uploadbutton",function(element,event){
				click("#zipfile")
			})

			onchange("#zipfile",function(element,event){
				let zipfile=domgetid("zipfile").files[0]

				ajax("POST",AJAXURL+"api/v1/games/"+slug+"/upload",function(event,data){
					if(data["success"]){
						alert("upload success")
						href("")
					}else{
						alert(data)
					}
				},formdata([
					["zipfile",zipfile],
					["token",weblsget("worldskill2022MDtoken")]
				]))
			})

			onclick("#deletebutton",function(element,event){
				ajax("DELETE",AJAXURL+"api/v1/games/"+slug,function(event,data){
					if(data["success"]){
						alert("delete success")
						href("profile.html")
					}else{
						alert(data["message"])
					}
				},null,[
					["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
				])
			})
		}else{
			href("game.html?slug="+slug)
		}
	})

	leaderboard()
	// setInterval(leaderboard,5000)

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
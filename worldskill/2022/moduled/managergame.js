let slug=getget("slug")

if(slug){
	ajax("GET",AJAXURL+"api/v1/games/"+slug,function(event,data){
		if(data["author"]==weblsget("worldskill2022MDusername")){
			domgetid("game").innerHTML=`
				<div class="game grid" data-slug="${data["slug"]}">
					<input type="text" class="title" id="title" value="${data["title"]}">
					<div class="author">by ${data["author"]}</div>
					<textarea class="description" id="description">${data["description"]}</textarea>
					<div class="scorecount">score submit: ${data["scoreCount"]}</div>
					<div class="imagediv"><img src="${data["thumbnail"]??"material/picture/default.jpg"}" class="image"></div>
				</div>

				<div>
					<input type="button" class="updatebutton" id="updatebutton" value="Save Changes">
					<input type="button" class="uploadbutton" id="uploadbutton" value="upload new version">
					<input type="button" class="deletebutton" id="deletebutton" value="delete">
					<input type="file" class="display-none" id="zipfile" accept=".zip">
				</div>
			`

			onclick("#updatebutton",function(element,event){
				ajax("PUT",AJAXURL+"api/v1/games/"+slug,function(event,data){
					if(data["status"]=="success"){
						alert("update success")
						href("")
					}else{
						alert(data["message"])
					}
				},str({
					"title": getvalue("title"),
					"description": getvalue("description")
				}),[
					["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
				])
			})

			onclick("#uploadbutton",function(element,event){
				click("#zipfile")
			})

			onchange("#zipfile",function(element,event){
				let zipfile=domgetid("zipfile").files[0]

				ajax("POST",AJAXURL+"api/v1/games/"+slug+"/upload",function(event,data){
					if(data==""){
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
				if(confirm("Are you sure to delete this game?")){
					ajax("DELETE",AJAXURL+"api/v1/games/"+slug,function(event,data){
						if(data["status"]=="forbidden"){
							alert(data["message"])
						}else{
							alert("delete success")
							href("profile.html")
						}
					},null,[
						["Authorization","Bearer "+weblsget("worldskill2022MDtoken")]
					])
				}
			})
		}else{
			href("game.html?slug="+slug)
		}
	})


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
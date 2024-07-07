if(weblsget("50nationalmodulea-token")){ href("album.html") }

onclick("#signin",function(element,event){
	ajax("POST",AJAXURL+"signin",function(event,data){
		if(data["success"]){
			weblsset("50nationalmodulea-userid",data["data"]["userid"])
			weblsset("50nationalmodulea-permission",data["data"]["permission"])
			weblsset("50nationalmodulea-token",data["data"]["token"])
			href("album.html")
		}else{
			domgetid("username").parentNode.classList.add("error")
			domgetid("password").parentNode.classList.add("error")
			innerhtml("#error",ERRORMESSAGE[data["data"]],false)
		}
	},JSON.stringify({
		"username": getvalue("username"),
		"password": getvalue("password")
	}))
})

onclick("#signup",function(element,event){
	ajax("POST",AJAXURL+"signup",function(event,data){
		if(data["success"]){
			weblsset("50nationalmodulea-userid",data["data"]["userid"])
			weblsset("50nationalmodulea-permission",data["data"]["permission"])
			weblsset("50nationalmodulea-token",data["data"]["token"])
			href("album.html")
		}else{
			domgetid("username").parentNode.classList.add("error")
			innerhtml("#error",ERRORMESSAGE[data["data"]],false)
		}
	},JSON.stringify({
		"username": getvalue("username"),
		"password": getvalue("password")
	}))
})


passwordshowhide()
onclick("#submit",function(element,event){
	ajax("POST",AJAXURL+"user/register",function(event,data){
		if(data["success"]){
			weblsset("51nationalmoduled-userid",data["data"]["id"])
			weblsset("51nationalmoduled-permission",data["data"]["role"])
			weblsset("51nationalmoduled-token",data["data"]["token"])
			href("index.html")
		}else{
			let errordata=[]
			if(data["message"]=="MSG_MISSING_FIELD"){
				if(domgetid("email").value==""){
					errordata.push("email")
					domgetid("email").parentNode.classList.add("error")
				}
				if(domgetid("password").value==""){
					errordata.push("password")
					domgetid("password").parentNode.classList.add("error")
				}
				if(domgetid("nickname").value==""){
					errordata.push("nickname")
					domgetid("nickname").parentNode.classList.add("error")
				}
			}
			domgetid("error").innerHTML=`
				${ERRORMESSAGE[data["message"]]}<br>
				${errordata.join("、")}
			`
		}
	},JSON.stringify({
		"email": domgetid("email").value,
		"password": domgetid("password").value,
		"nickname": domgetid("nickname").value
	}))
})

document.onkeydown=function(event){
	if(event.key=="Enter"){
		domgetid("submit").click()
	}
}

passwordshowhide()
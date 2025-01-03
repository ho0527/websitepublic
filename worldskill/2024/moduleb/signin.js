document.getElementById("submit").onclick=function(){
	if(document.getElementById("password").value=="admin"){
		alert("login succcess")
		localStorage.setItem(WEBLSNAME+"signined","true")
		location.href="company.html"
	}else{
		alert("password incorrect")
	}
}

document.getElementById("password").onkeydown=function(event){
	if(event.key=="Enter"){
		document.getElementById("submit").click()
	}
}
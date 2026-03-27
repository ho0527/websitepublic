const WEBLSNAME="18thabilympics-"

function logout(){
	alert("登出成功")
	localStorage.removeItem(WEBLSNAME+"signin")
	location.href="signin.php"
}

if(localStorage.getItem(WEBLSNAME+"signin")=="true"){
	document.getElementById("header").innerHTML=`
		${document.getElementById("header").innerHTML}
		<input type="button" class="button" onclick="logout()" value="登出">
	`
}
const WEBLSNAME="19thabilympics-"

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

//手機版收合選單
if(document.getElementById("burgerbutton")!=null){
	document.getElementById("burgerbutton").onclick=function(){
		document.getElementById("nav").classList.toggle("open")
	}
}

//平板版下拉選單
if(document.getElementById("navselect")!=null){
	document.getElementById("navselect").onchange=function(){
		location.href=this.value
	}
}

//已登入就直接進後台
if(localStorage.getItem(WEBLSNAME+"signin")=="true"){
	location.href="admin.php"
}

document.getElementById("signinform").onsubmit=function(event){
	event.preventDefault();

	let username=document.getElementById("username").value
	let password=document.getElementById("password").value

	if(username=="admin"&&password=="1234"){
		alert("登入成功");
		localStorage.setItem(WEBLSNAME+	"signin","true")
		location.href="admin.php"
	}else{
		alert("帳號或密碼輸入錯誤");
	}
}
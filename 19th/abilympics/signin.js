//已登入就直接進後台
if(localStorage.getItem(WEBLSNAME+"signin")=="true"){
	location.href="admin.php"
}

//帳密由 signin.php 向 admin 資料表驗證，驗證成功後才呼叫
function signin(){
	localStorage.setItem(WEBLSNAME+"signin","true")
	alert("登入成功")
	location.href="admin.php"
}

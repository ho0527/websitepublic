let ver="1234"

if(localStorage.getItem(WEBLSNAME+"signin")=="true"){
	location.href="admin.php"
}

document.getElementById("verimg").onclick=function(){
	if(ver=="1234"){
		ver="5678"
	}else{
		ver="1234"
	}
	document.getElementById("verimg").src="ver"+ver+".png"
}

document.getElementById("signinform").onsubmit=function(event){
	event.preventDefault();

	let username=document.getElementById("username").value
	let password=document.getElementById("password").value
	let verinput=document.getElementById("verinput").value

	if(verinput==ver){
		if(username=="admin"&&password=="1234"){
			alert("登入成功");
			localStorage.setItem(WEBLSNAME+	"signin","true")
			location.href="admin.php"
		}else{
			alert("帳號或密碼輸入錯誤");
		}
	}else{
		alert("驗證碼輸入錯誤")
	}

}
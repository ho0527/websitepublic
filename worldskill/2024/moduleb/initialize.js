const WEBLSNAME="worldskill2024moduleb-"
const AJAXURL="/worldskill2024moduleb/"

let file=location.href.split("/")[location.href.split("/").length-1]

function ajax(method,url,callback,data=null){

}

if(localStorage.getItem(WEBLSNAME+"signined")=="true"){
	if(file=="login.html"){
		location.href="company.html"
	}
}else{
	if(file!="login.html"){
		alert("401 error: not logined")
		location.href="login.html"
	}
}
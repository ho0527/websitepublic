const WEBLSNAME="worldskill2024moduleb-"
const AJAXURL="/backend/worldskill2024moduleb/"

let file=location.href.split("/")[location.href.split("/").length-1]

function getget(key){
	return new URLSearchParams(location.search).get(key)
}

function formsubmit(formid,callback=function(){}){
	document.getElementById(formid).onsubmit=function(event){
		event.preventDefault()
		callback()
	}
}

function ajax(method,url,callback=function(){},data=null){
	let ajax=new XMLHttpRequest()

	ajax.onload=function(event){
		let response=ajax.responseText
		try{
			response=JSON.parse(response)
		}finally{
			callback(response,event)
		}
	}

	ajax.open(method,url)
	ajax.send(JSON.stringify(data))

	return ajax
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
const WEBLSNAME="worldskill2024moduleb-"
const AJAXURL="/backend/worldskill2024moduleb/"
const UPLOADFILEURL="/backenduploadfile/worldskill2024moduleb/"

let file=location.href.split("/")[location.href.split("/").length-1]
let dontneedsigninfile=["","index.html","login.html"]

function getget(key){
	return new URLSearchParams(location.search).get(key)
}

function formsubmit(formid,callback=function(){}){
	document.getElementById(formid).onsubmit=function(event){
		event.preventDefault()
		callback()
	}
}

function ajax(method,url,callback=function(){},data=null,header={}){
	let xmlhttprequest=new XMLHttpRequest()

	xmlhttprequest.open(method,url)

	for(let key in header){
		xmlhttprequest.setRequestHeader(key,header[key])
	}

	xmlhttprequest.onload=function(event){
		let response=xmlhttprequest.responseText
		try{
			response=JSON.parse(response)
		}finally{
			callback(response,event)
		}
	}

	if(typeof data=="object"&&!(data instanceof FormData))
		xmlhttprequest.send(JSON.stringify(data))
	else
		xmlhttprequest.send(data)

	return xmlhttprequest
}

if(localStorage.getItem(WEBLSNAME+"signined")=="true"){
	if(dontneedsigninfile.includes(file)){
		location.href="company.html"
	}
}else{
	if(dontneedsigninfile.includes(file)){
		alert("401 error: not logined")
		location.href="login.html"
	}
}
document.getElementById("submit").onclick=function(){
	let ajax=new XMLHttpRequest()

	ajax.onload=function(event){

	}

	ajax.open("POST",AJAXURL+"newcompany")
	ajax.send(JSON.stringify({
		"name": document.getElementById("name").value,
		"address": document.getElementById("address").value,
		"phone": document.getElementById("phone").value,
		"email": document.getElementById("email").value,
		"ownername": document.getElementById("ownername").value,
		"ownerphone": document.getElementById("ownerphone").value,
		"owneraddress": document.getElementById("owneraddress").value,
		"contactname": document.getElementById("contactname").value,
		"contactphone": document.getElementById("contactphone").value,
		"contactaddress": document.getElementById("contactaddress").value
	}))
}
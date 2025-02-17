document.getElementById("submit").onclick=function(){
	ajax("POST",AJAXURL+"newcompany",function(data,event){
		if(data["success"]){
			alert("create success")
			location.href="company.html"
		}else{
			alert("未知錯誤 請聯繫管理員")
			console.log(data["data"])
		}
	},{
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
	})
}
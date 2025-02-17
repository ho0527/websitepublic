if(getget("id")){
	ajax("GET",AJAXURL+"getcompany/"+getget("id"),function(data,event){
		if(data["success"]){
			document.getElementById("name").value=data["data"]["name"]
			document.getElementById("address").value=data["data"]["address"]
			document.getElementById("phone").value=data["data"]["phone"]
			document.getElementById("email").value=data["data"]["email"]
			document.getElementById("ownername").value=data["data"]["ownername"]
			document.getElementById("ownerphone").value=data["data"]["ownerphone"]
			document.getElementById("owneraddress").value=data["data"]["owneraddress"]
			document.getElementById("contactname").value=data["data"]["contactname"]
			document.getElementById("contactphone").value=data["data"]["contactphone"]
			document.getElementById("contactaddress").value=data["data"]["contactaddress"]

			formsubmit("form",function(){
				ajax("PUT",AJAXURL+"editcompany/"+getget("id"),function(data,event){
					if(data["success"]){
						alert("edit success")
						location.href="company.html"
					}else{
						alert("未知錯誤 請聯繫管理員"+data["data"])
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
			})
		}else{
			if(data["data"]=="company not found"){
				alert("company not found")
				location.href="company.html"
			}
			alert("未知錯誤 請聯繫管理員"+data["data"])
			console.log(data["data"])
		}
	})
}else{
	location.href="company.html"
}
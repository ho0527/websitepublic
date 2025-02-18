if(getget("companyid")){
	formsubmit("form",function(){
		let formdata=new FormData()

		formdata.append("file",document.getElementById("file").files)
		formdata.append("name",document.getElementById("name").value)
		formdata.append("enname",document.getElementById("enname").value)
		formdata.append("gtin",document.getElementById("gtin").value)
		formdata.append("description",document.getElementById("description").value)
		formdata.append("endescription",document.getElementById("endescription").value)
		formdata.append("brandname",document.getElementById("brandname").value)
		formdata.append("country",document.getElementById("country").value)
		formdata.append("grossweight",document.getElementById("grossweight").value)
		formdata.append("contentweight",document.getElementById("contentweight").value)
		formdata.append("unit",document.getElementById("unit").value)

		ajax("POST",AJAXURL+"newproduct/"+getget("companyid"),function(data,event){
			if(data["success"]){
				alert("create success")
				location.href="companydetail.html?companyid="+getget("companyid")
			}else{
				if(data["data"]=="gtin error"){
					alert("GTIN error")
				}else if(data["data"]=="gtin error"){
					alert("GTIN error")
				}else{
					alert("未知錯誤 請聯繫管理員")
					console.log(data["data"])
				}
			}
		},formdata)
	})
}else{
	location.href="company.html"
}
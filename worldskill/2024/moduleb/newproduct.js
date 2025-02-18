if(getget("companyid")){
	formsubmit("form",function(){
		let formdata=new FormData()

		formdata.append("file",document.getElementById("file").files)
		formdata.append("name",document.getElementById("name").value)
		formdata.append("engname",document.getElementById("engname").value)
		formdata.append("gtin",document.getElementById("gtin").value)
		formdata.append("description",document.getElementById("description").value)
		formdata.append("engdescription",document.getElementById("engdescription").value)
		formdata.append("brandname",document.getElementById("brandname").value)
		formdata.append("country",document.getElementById("country").value)
		formdata.append("grossweight",document.getElementById("grossweight").value)
		formdata.append("contentweight",document.getElementById("contentweight").value)

		ajax("POST",AJAXURL+"newproduct/"+getget("companyid"),function(data,event){
			if(data["success"]){
				alert("create success")
				location.href="companydetail.html?id="+getget("companyid")
			}else{
				alert("未知錯誤 請聯繫管理員")
				console.log(data["data"])
			}
		},formdata)
	})
}else{
	location.href="company.html"
}
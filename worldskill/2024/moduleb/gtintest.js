formsubmit("form",function(){
	ajax("GET",AJAXURL+"gtintest",function(data,event){
		if(data["success"]){
			let row=data["data"]

			for(let i=0;i<row.length;i=i+1){
				document.getElementById("main").innerHTML=`
					${document.getElementById("main").innerHTML}
					<div class="companydiv">
						<div>name: ${row[i]["name"]}</div>
						<div>address: ${row[i]["address"]}</div>
						<div>phone: ${row[i]["phone"]}</div>
						<div>email: ${row[i]["email"]}</div>
						<div>
							owner:
							<div>name: ${row[i]["ownername"]}</div>
							<div>address: ${row[i]["owneraddress"]}</div>
							<div>phone: ${row[i]["ownerphone"]}</div>
						</div>
						<div>
							contact:
							<div>name: ${row[i]["contactname"]}</div>
							<div>address: ${row[i]["contactaddress"]}</div>
							<div>phone: ${row[i]["contactphone"]}</div>
						</div>
						<div>
							<input type="button" class="button edit" data-id="${row[i]["id"]}" value="edit">
							<input type="button" class="button deactivate" data-id="${row[i]["id"]}" value="deactivate">
						</div>
					</div>
				`
			}

			document.querySelectorAll(".companydiv").forEach(function(element){
				element.onclick=function(){
					location.href="companydetail.html?id="+element.dataset.id
				}
			})

			document.querySelectorAll(".edit").forEach(function(element){
				element.onclick=function(event){
					event.preventDefault()
					event.stopPropagation()
					location.href="editcompany.html?id="+element.dataset.id
				}
			})

			document.querySelectorAll(".deactivate").forEach(function(element){
				element.onclick=function(event){
					event.preventDefault()
					event.stopPropagation()
					if(confirm("confirm deactivate?")){
						ajax("PUT",AJAXURL+"deactivatecompany/"+element.dataset.id,function(data,event){
							if(data["success"]){
								alert("deactivate success")
								location.href="company.html"
							}else{
								alert("未知錯誤 請聯繫管理員"+data["data"])
								console.log(data["data"])
							}
						})
					}
				}
			})
		}else{
			alert("未知錯誤 請聯繫管理員"+data["data"])
			console.log(data["data"])
		}
	})
})
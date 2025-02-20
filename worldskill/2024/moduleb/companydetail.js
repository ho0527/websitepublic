if(getget("companyid")){
	ajax("GET",AJAXURL+"getcompanyproductlist/"+getget("companyid"),function(data,event){
		if(data["success"]){
			let row=data["data"]["product"]

			document.getElementById("main").innerHTML=`
				<div class="companydiv fill cursor-initial" data-id="${data["data"]["company"]["id"]}">
					<div>name: ${data["data"]["company"]["name"]}</div>
					<div>address: ${data["data"]["company"]["address"]}</div>
					<div>phone: ${data["data"]["company"]["phone"]}</div>
					<div>email: ${data["data"]["company"]["email"]}</div>
					<div>
						owner:
						<div>name: ${data["data"]["company"]["ownername"]}</div>
						<div>address: ${data["data"]["company"]["owneraddress"]}</div>
						<div>phone: ${data["data"]["company"]["ownerphone"]}</div>
					</div>
					<div>
						contact:
						<div>name: ${data["data"]["company"]["contactname"]}</div>
						<div>address: ${data["data"]["company"]["contactaddress"]}</div>
						<div>phone: ${data["data"]["company"]["contactphone"]}</div>
					</div>
					<div>
						<input type="button" class="button" id="editcompany" data-id="${data["data"]["company"]["id"]}" value="edit">
						<input type="button" class="button" id="deactivatecompany" data-id="${data["data"]["company"]["id"]}" value="deactivate">
					</div>
				</div>
			`

			for(let i=0;i<row.length;i=i+1){
				document.getElementById("main").innerHTML=`
					${document.getElementById("main").innerHTML}
					<div class="companydiv cursor-initial">
						<div><img src="${UPLOADFILEURL}${row[i]["imagelink"]}" class="image"></div>
						<div>name: ${row[i]["name"]}</div>
						<div>enname: ${row[i]["enname"]}</div>
						<div>gtin: ${row[i]["gtin"]}</div>
						<div>description: ${row[i]["description"]}</div>
						<div>endescription: ${row[i]["endescription"]}</div>
						<div>brandname: ${row[i]["brandname"]}</div>
						<div>country: ${row[i]["country"]}</div>
						<div>grossweight: ${row[i]["grossweight"]}(${row[i]["unit"]})</div>
						<div>contentweight: ${row[i]["contentweight"]}(${row[i]["unit"]})</div>
						<div>
							<input type="button" class="button edit" data-id="${row[i]["id"]}" value="edit">
							<input type="button" class="button deactivate" data-id="${row[i]["id"]}" value="deactivate">
						</div>
					</div>
				`
			}

			document.getElementById("newproduct").href="newproduct.html?companyid="+getget("companyid")

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

			document.getElementById("editcompany").onclick=function(event){
				event.preventDefault()
				event.stopPropagation()
				location.href="editcompany.html?id="+getget("companyid")
			}

			document.getElementById("deactivatecompany").onclick=function(event){
				event.preventDefault()
				event.stopPropagation()
				if(confirm("confirm deactivate?")){
					ajax("PUT",AJAXURL+"deactivatecompany/"+getget("companyid"),function(data,event){
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
		}else{
			if(data["data"]=="company not found"){
				alert("company not found")
				location.href="company.html"
			}else{
				alert("未知錯誤 請聯繫管理員"+data["data"])
				console.log(data["data"])
			}
		}
	})
}else{
	location.href="company.html"
}
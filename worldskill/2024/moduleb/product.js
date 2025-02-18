if(getget("productgtin")){
	ajax("GET",AJAXURL+"getproduct/"+getget("productgtin"),function(data,event){
		if(data["success"]){
			document.getElementById("main").innerHTML=`
				<div class="companydiv">
					<div><img src="${data["data"]["imagelink"]}" class="image"></div>
					<div id="name">name: ${data["data"]["name"]}</div>
					<div>phone: ${data["data"]["gtin"]}</div>
					<div id="description">description: ${data["data"]["description"]}</div>
					<div>brand name: ${data["data"]["brandname"]}</div>
					<div>country: ${data["data"]["country"]}</div>
					<div>gross weight: ${data["data"]["grossweight"]}(${data["data"]["unit"]})</div>
					<div>content weight: ${data["data"]["contentweight"]}(${data["data"]["unit"]})</div>
				</div>
			`

			document.getElementById("lang").onclick=function(){
				if(this.value=="中文"){
					document.getElementById("name").innerHTML=`name: ${data["data"]["engname"]}`
					document.getElementById("description").innerHTML=`description: ${data["data"]["engdescription"]}`
					this.value="EN"
					document.querySelectorAll("html")[0].lang="en"
				}else{
					document.getElementById("name").innerHTML=`name: ${data["data"]["name"]}`
					document.getElementById("description").innerHTML=`description: ${data["data"]["description"]}`
					this.value="中文"
					document.querySelectorAll("html")[0].lang="zh-tw"
				}
			}
		}else{
			if(data["data"]=="product not found"){
				alert("product not found")
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
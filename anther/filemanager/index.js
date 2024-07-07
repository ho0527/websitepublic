let mainfolder="upload"
let folderlist
let folder
let locaitonfolderdata=""
let pass=""

function main(){
	folderlist=[mainfolder]
	folder=mainfolder+"/"

	if(isset(location.href.split("#")[1])){
		locaitonfolderdata=location.href.split("#")[1]
		folder=folder+locaitonfolderdata
		for(let i=0;i<locaitonfolderdata.split("/").length;i=i+1){
			if(isset(locaitonfolderdata.split("/")[i])){
				folderlist.push(locaitonfolderdata.split("/")[i])
			}
		}
	}

	ajax("GET","filelist.php?folder="+folder,function(event,data){
		let row=data["filelist"]
		let folderpath=""

		if(data["success"]){
			for(let i=0;i<folderlist.length;i=i+1){
				folderpath=folderpath+"<div class=\"pathlink\" id=\""+i+"\">"+folderlist[i]+"</div>"+"/"
			}

			domgetall("pathlink").forEach(function(event){
				// event.onclick=function(){
				//     location.href="#"+folderlist.join("/")+"/"+event.innerText
				//     href("")
				// }
			})

			domgetid("pathlist").innerHTML=`${folderpath}`

			if(folderlist.length==1){
				domgetid("pathgoback").innerHTML=`已經在最前一頁了`
			}else{
				domgetid("pathgoback").innerHTML=`
					<div class="goback" id="goback">回到上一頁</div>
				`
				domgetid("goback").onclick=function(){
					folderlist.pop()
					console.log(folderlist)
					if(folderlist.length>1){
						location.href="#"+folderlist.join("/")
						href("")
					}else{
						location.href=""
					}
				}
			}

			domgetid("filelist").innerHTML=``
			for(let i=0;i<row.length;i=i+1){
				let div2=``
				let div3=``

				if(row[i]["isfolder"]){
					div2=`
						<div class="filename folder" data-id="${i}">
							${row[i]["name"]}
						</div>
					`
				}else{
					div2=`
						<div class="filename">
							${row[i]["name"]}
						</div>
					`
				}

				// let div3=doccreate("div")
				// div3.classList.add("fileitembuttondiv")

				if(!row[i]["isfolder"]){
					div3=`
						<div class="filename">
							<input type="button" class="bluebutton fileitembutton downloadbutton" data-href="${folderlist.join("/")}/${row[i]["name"]}" data-download="${row[i]["name"]}" value="下載">
						</div>
					`
					// let input=doccreate("input")
					// input.classList.add("fileitembutton")
					// input.classList.add("bluebutton")
					// input.type="button"
					// input.value="下載"
					// input.onclick=function(){
					// 	let a=doccreate("a")
					// 	a.href=folderlist.join("/")+"/"+row[i]["name"]
					// 	a.download=row[i]["name"]
					// 	a.click()
					// }
					// div3.appendChild(input)
				}

				// let deleteButton=doccreate("button")
				// deleteButton.classList.add("fileitembutton")
				// deleteButton.classList.add("bluebutton")
				// deleteButton.innerText="刪除"
				// deleteButton.addEventListener("click",() => {
				// 	deletefile(row[i]["name"],row[i]["isfolder"])
				// })
				// div3.appendChild(deleteButton)

				// div.appendChild(div2)
				// div.appendChild(div3)

				innerhtml("#filelist",`
					<div class="grid fileitem">
						${div2}
						<div class="fileitembuttondiv">
							${div3}
							<input type="button" class="bluebutton fileitembutton deletebutton" data-name="${row[i]["name"]}" data-isfolder="${row[i]["isfolder"]}" value="刪除">
						</div>
					</div>
				`)
			}

			onclick(".folder",function(element,event){
				href("#"+locaitonfolderdata+row[dataset(element)]["name"],"id")
				href("")
			})

			onclick(".downloadbutton",function(element,event){
				innerhtml("body",`
					<a href="${dataset(element,"href",element)}" download="${dataset("download")}" id="download"></a>
				`)

				click("#download")
			})

			onclick(".deletebutton",function(element,event){
				deletefile(dataset(element),"name",element),dataset("isfolder")
			})
		}else{
			innerhtml("#pathgoback",`
				<div class="goback" id="goback">回到最前頁</div>
			`,false)

			innerhtml("#filelist",`
				<div class="warning">查無此路徑</div>
			`,false)

			onclick("goback",function(element,event){
				href("")
			})
		}
	})
}

function deletefile(filename,isfolder){
	Swal.fire({
		title: "確定刪除?",
		text: "刪除後將無法恢復。",
		icon: "warning",
		showCancelButton: true,
		confirmButtonText: "確定",
		cancelButtonText: "取消",
	}).then(function(result){
		if(result.isConfirmed){
			ajax("DELETE","delete.php?file="+filename+"&isfolder="+isfolder,function(event,data){
				if(data["success"]){
					Swal.fire({
						title: "刪除成功",
						icon: "success",
						showConfirmButton: false,
						timer: 1500,
					}).then(function(){
						href("")
					})
				}else{
					Swal.fire({
						title: "刪除失敗",
						text: "請聯繫管理員或稍後再試。",
						icon: "error",
						confirmButtonText: "確定",
					})
				}
			})
		}
	})
}

if(weblsget("filemanager-signin")!="true"){
	while(true){
		pass=prompt("aaa")
		if(pass=="chris0527"){
			weblsset("filemanager-signin","true")
			break
		}
	}
}

domgetid("submit").onclick=function(){
	lightbox(null,"lightbox",function(){
		return `
			<h2>UPLOADING...</h2>
			<div id="percent"></div>
			<progress id="progress" max="100" value="0"></progress>
		`
	})

	ajax("POST","upload.php",function(event,data){
		if(data["success"]){
			Swal.fire({
				title: "Upload Success",
				icon: "success",
				showConfirmButton: false,
				timer: 1500
			}).then(function(){
				href("")
			})
		}else{
			Swal.fire({
				title: "Upload Failed",
				text: response.message,
				icon: "error",
				confirmButtonText: "OK"
			}).then(function(){
				domgetid("progress").value=0
				domgetid("fileinput").disabled=false
				domgetid("folderinput").disabled=false
			})
		}
	},new FormData(domgetid("form")),[],function(event,progress){
		if(domgetid("progress")){
			domgetid("progress").value=progress
			if(progress==100){
				innerhtml("#percent",`
					已完成上傳!
				`,false)
			}else{
				innerhtml("#percent",`
					已完成: ${progress}%
				`,false)
			}
		}else{
			href("./")
		}
	})
}

setInterval(function(){
	main()
},10000)

main()

startmacossection()
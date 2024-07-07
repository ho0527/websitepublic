let page=/page=([^&]+)/.exec(location.search)
let orderaside=/orderaside=([^&]+)/.exec(location.search)
let category=/category=([^&]+)/.exec(decodeURI(location.search))
let dataall=[]
let data={
	"inorout": "in",
	"count": 1,
	"orderlist": []
}
let studentdb
let classdb
let tablenotable
let tableid
let dbdata

function updatedb(data){
	localStorage.setItem("54national_modulee",JSON.stringify(data))
}

function getdb(){
	localStorage.getItem("54national_modulee")
}

function totalprice(){
	let total=0
	for(let i=0;i<data["orderlist"].length;i=i+1){
		if(!data["orderlist"][i]["ordered"])
			total=total+data["orderlist"][i]["price"]*data["orderlist"][i]["num"]
	}
	return total
}

async function main(){
	if(page=="order"){
		let response=await fetch("foodlist.json")
		let foodlist=await response.json()
		let tempfoodlist=foodlist.filter(function(event){
			return event["type"]==category||category=="全部"
		})
		let menuinnerhtml=``
		let orderlistinnerhtml=``

		document.getElementById("navigationbar").innerHTML=`
			<div class="navigationbar">
				<div class="navigationbarleft">
					<div class="text huge bold maintitle" onclick="href('orderfood.html')">新北訂餐網</div>
					<div style="width: 25px"></div>
					<div class="text large" id="titletext"></div>
				</div>
				<div class="flex">
					<input type="button" class="button light" id="exit" value="離開點餐">
					<div class="selection">
						<label class="item">
							<input type="radio" name="inorout" id="in" checked>
							<div class="text">內用</div>
						</label>
						<label class="item">
							<input type="radio" name="inorout" id="out">
							<div class="text">外帶</div>
						</label>
					</div>
					<div class="inputdiv">
						<div class="label">用餐人數</div>
						<div class="input">
							<input type="number" id="count" value="1">
						</div>
					</div>
				</div>
				<div class="navigationbarright">
					<div class="navigationbarrightphonebar" id="phonebar">
						<div class="navigationbarrightphonebarline"></div>
						<div class="navigationbarrightphonebarline"></div>
						<div class="navigationbarrightphonebarline"></div>
					</div>
					<div class="navigationbarrightbuttonlist" id="navbarbuttonlist">
						<input type="button" class="navigationbarbutton linkbutton" id="index" value="首頁">
						<input type="button" class="navigationbarbutton linkbutton" id="comment" value="訪客留言">
						<input type="button" class="navigationbarbutton linkbutton" id="orderfood" value="訪客訂餐">
						<input type="button" class="navigationbarbutton linkbutton" id="signin" value="網站管理">
					</div>
				</div>
			</div>
		`
		document.getElementById("main").innerHTML=`
			<ul class="list categorylist" id="category">
				<li class="item type" data-id="全部">全部</li>
				<li class="item type" data-id="漢堡">漢堡</li>
				<li class="item type" data-id="點心">點心</li>
			</ul>
		`
		document.getElementById("aside").innerHTML=`
			<div class="selection fill">
				<label class="item">
					<input type="radio" name="orderaside" id="yetOrderBtn" checked>
					<div class="text">未點</div>
				</label>
				<label class="item">
					<input type="radio" name="orderaside" id="haveOrderBtn">
					<div class="text">已點</div>
				</label>
			</div>
			<div id="orderlist">
			</div>
		`
		document.getElementById("footer").innerHTML=`
			<div>
			</div>
			<div class="flex margin-0px_10px">
				<div class="total-price" id="totalprice">總計: ${totalprice()}元</div>
				<button class="btn btn-primary" id="checkout">結帳</button>
			</div>
		`

		for(let i=0;i<tempfoodlist.length;i=i+1){
			menuinnerhtml=`
				${menuinnerhtml}
				<div class="imagediv menuitem">
					<div class="menuimagediv"><img src="${tempfoodlist[i]["image"]}" alt="${tempfoodlist[i]["name"]}" class="meal image"></div>
					<div class="flex">
						<div>
							<div class="meal-name">${tempfoodlist[i]["name"]}</div>
							<div class="meal-price">${tempfoodlist[i]["price"]}元</div>
						</div>
						<button class="addMeal" data-id="${tempfoodlist[i]["id"]}">+</button>
					</div>
				</div>
			`
		}

		document.getElementById("main").innerHTML=`
			${document.getElementById("main").innerHTML}
			<div class="macossectiondivy" id="menu">
			 	${menuinnerhtml}
			</div>
		`

		if(orderaside=="yet"){
			for(let i=0;i<data["orderlist"].length;i=i+1){
				orderlistinnerhtml=`
					${orderlistinnerhtml}
					<li class="item">
						<div class="flex">
							<div class="name">${data["orderlist"][i]["name"]}</div>
							<div class="num">x${data["orderlist"][i]["num"]}</div>
						</div>
						<div class="flex">
							<div class="delete" data-id="${data["orderlist"][i]["id"]}">刪除</div>
							<div class="amount">${data["orderlist"][i]["price"]*data["orderlist"][i]["num"]}元</div>
						</div>
					</li>
				`
			}
		}else{
			for(let i=0;i<dataall.length;i=i+1){
				for(let j=0;j<dataall[i]["orderlist"].length;j=j+1){
					orderlistinnerhtml=`
						${orderlistinnerhtml}
						<li class="item">
							<div class="flex">
								<div class="name">${dataall[i]["orderlist"][j]["name"]}</div>
								<div class="num">x${dataall[i]["orderlist"][j]["num"]}</div>
							</div>
							<div class="flex">
								<div></div>
								<div class="amount">${dataall[i]["orderlist"][j]["price"]*dataall[i]["orderlist"][j]["num"]}</div>
							</div>
						</li>
					`
				}
			}
		}

		document.getElementById(orderaside+"OrderBtn").checked=true
		document.getElementById(data["inorout"]).checked=true
		document.getElementById("orderlist").innerHTML=`
			<ul class="orderlist list" id="${orderaside}OrderList">
				${orderlistinnerhtml}
			</ul>
		`

		document.querySelectorAll(".categorylist li").forEach(function(event){
			event.onclick=function(){
				category=event.dataset.id

				history.pushState(null,null,"?page=order&orderaside="+orderaside+"&category="+category)
				main()
			}

			if(event.dataset.id==category){
				event.classList.add("active")
			}
		})

		document.querySelectorAll(".addMeal").forEach(function(event){
			event.onclick=function(){
				let food=foodlist[event.dataset.id]
				let check=false

				for(let i=0;i<data["orderlist"].length;i=i+1)
					if(data["orderlist"][i]["id"]==event.dataset.id&&data["orderlist"][i]["ordered"]==false){
						data["orderlist"][i]["num"]=data["orderlist"][i]["num"]+1
						check=true
						break
					}

				if(!check)
					data["orderlist"].push({
						"id": event.dataset.id,
						"name": food["name"],
						"price": food["price"],
						"num": 1,
						"ordered": false
					})

				updatedb(data)
				if(orderaside=="yet"){
					let orderlistinnerhtml=``

					for(let i=0;i<data["orderlist"].length;i=i+1){
						if(!data["orderlist"][i]["ordered"]){
							orderlistinnerhtml=`
								${orderlistinnerhtml}
								<li class="item">
									<div class="flex">
										<div class="name">${data["orderlist"][i]["name"]}</div>
										<div class="num">x${data["orderlist"][i]["num"]}</div>
									</div>
									<div class="flex">
										<div class="delete" data-id="${data["orderlist"][i]["id"]}">刪除</div>
										<div class="amount">${data["orderlist"][i]["price"]*data["orderlist"][i]["num"]}元</div>
									</div>
								</li>
							`
						}
					}

					document.getElementById("yetOrderBtn").classList.add("active")

					document.getElementById("orderlist").innerHTML=`
						<ul class="orderlist list" id="yetOrderList">
							${orderlistinnerhtml}
						</ul>
					`

					document.querySelectorAll(".delete").forEach(function(event){
						event.onclick=function(){
							for(let i=0;i<data["orderlist"].length;i=i+1){
								if(data["orderlist"][i]["id"]==event.dataset.id){
									data["orderlist"].splice(i,1)
									break
								}
							}
							updatedb(data)
							main()
						}
					})
				}
				document.getElementById("totalprice").innerHTML=`總計: ${totalprice()}元`
			}
		})

		document.getElementById("checkout").onclick=function(){
			if(0<domgetid("count").value){
				page="pay"
				data["count"]=domgetid("count").value
				updatedb(data)
				history.pushState(null,null,"?page=pay")
				main()
			}else{
				alert("用餐人數輸入錯誤")
			}
		}

		document.getElementById("yetOrderBtn").onclick=function(){
			orderaside="yet"
			history.pushState(null,null,"?page=order&orderaside=yet")
			main()
		}

		document.getElementById("haveOrderBtn").onclick=function(){
			orderaside="have"
			history.pushState(null,null,"?page=order&orderaside=have")
			main()
		}

		document.getElementById("in").onclick=function(){
			data["inorout"]="in"
			updatedb(data)
			history.pushState(null,null,"?page=order&orderaside=yet")
			main()
		}

		document.getElementById("out").onclick=function(){
			data["inorout"]="out"
			updatedb(data)
			history.pushState(null,null,"?page=order&orderaside=have")
			main()
		}

		document.getElementById("main").classList.add("asideisshow")
		document.getElementById("aside").classList.add("asideisshow")

		document.getElementById("exit").onclick=function(){
			data={
				"inorout": "in",
				"count": 1,
				"orderlist": []
			}

			updatedb(data)

			href("index.html")
		}

		document.querySelectorAll(".delete").forEach(function(event){
			event.onclick=function(){
				for(let i=0;i<data["orderlist"].length;i=i+1){
					if(data["orderlist"][i]["id"]==event.dataset.id){
						data["orderlist"].splice(i,1)
						break
					}
				}
				updatedb(data)
				main()
			}
		})

		onclick(".imagediv .image",function(element,event){
			lightbox(null,"lightbox",function(){
				return `
					<div class="position-relative">
						<div class="imagediv2">
							<img src="${element.src}" class="image" id="bigimage" style="opacity: 1;" draggable="false">
						</div>
						<input type="button" class="button light orderfoodclosebutton" id="close" value="X">
					</div>
				`
			},"close")
		})
	}else if(page=="pay"){
		let orderlistinnerhtml=``

		document.getElementById("navigationbar").innerHTML=`
			<div class="navigationbar">
				<div class="navigationbarleft">
					<div class="text huge bold maintitle" onclick="href('orderfood.html')">新北訂餐網</div>
					<div style="width: 25px"></div>
					<div class="text large" id="titletext"></div>
				</div>
				<div class="flex">
					<input type="button" class="button light" id="exit" value="離開點餐">
				</div>
				<div class="navigationbarright">
					<div class="navigationbarrightphonebar" id="phonebar">
						<div class="navigationbarrightphonebarline"></div>
						<div class="navigationbarrightphonebarline"></div>
						<div class="navigationbarrightphonebarline"></div>
					</div>
					<div class="navigationbarrightbuttonlist" id="navbarbuttonlist">
						<input type="button" class="navigationbarbutton linkbutton" id="index" value="首頁">
						<input type="button" class="navigationbarbutton linkbutton" id="comment" value="訪客留言">
						<input type="button" class="navigationbarbutton linkbutton" id="orderfood" value="訪客訂餐">
						<input type="button" class="navigationbarbutton linkbutton" id="signin" value="網站管理">
					</div>
				</div>
			</div>
		`
		document.getElementById("main").innerHTML=`
			<div class="flex">
				<h1 id="title">確認餐點</h1>
				<div class="text big">
					${data["inorout"]=="in"?"內用":"外帶"} - ${data["count"]}人
				</div>
			</div>
		`
		document.getElementById("aside").innerHTML=``
		document.getElementById("footer").innerHTML=`
			<button class="btn btn-primary" id="return">返回點餐頁</button>
			<button class="btn btn-primary" id="byCash">現金付款</button>
		`

		document.getElementById("main").classList.remove("asideisshow")
		document.getElementById("aside").classList.remove("asideisshow")

		for(let i=0;i<data["orderlist"].length;i=i+1){
			if(!data["orderlist"][i]["ordered"]){
				orderlistinnerhtml=`
					${orderlistinnerhtml}
					<li class="item">
						<div class="flex">
							<div class="name">${data["orderlist"][i]["name"]}</div>
							<div class="num">${data["orderlist"][i]["price"]}元 x${data["orderlist"][i]["num"]}</div>
						</div>
						<div class="flex">
							<div></div>
							<div class="amount">${data["orderlist"][i]["price"]*data["orderlist"][i]["num"]}元</div>
						</div>
					</li>
				`
			}
		}

		document.getElementById("main").innerHTML=`
			${document.getElementById("main").innerHTML}
			<ul class="list" id="orderList">
				${orderlistinnerhtml}
			</ul>
			<span class="total">總計: ${totalprice()} 元</span>
		`

		document.querySelectorAll(".categorylist li").forEach(function(event){
			event.onclick=function(){
				category=event.dataset.id

				history.pushState(null,null,"?page=order&orderaside="+orderaside+"&category="+category)
				main()
			}

			if(event.dataset.id==category){
				event.classList.add("active")
			}
		})

		document.getElementById("byCash").onclick=function(){
			dataall.push(data)
			localStorage.setItem("54national_modulee_all",str(dataall))
			page="success"
			history.pushState(null,null,"?page=success")
			main()
		}

		document.getElementById("return").onclick=function(){
			page="order"
			history.pushState(null,null,"?page=order&orderaside="+orderaside+"&category="+category)
			main()
		}

		document.getElementById("exit").onclick=function(){
			data={
				"inorout": "in",
				"count": 1,
				"orderlist": []
			}

			updatedb(data)

			href("index.html")
		}
	}else if(page=="success"){
		let orderlistinnerhtml=``

		document.getElementById("navigationbar").innerHTML=`
			<div class="navigationbar">
				<div class="navigationbarleft">
					<div class="text huge bold maintitle" onclick="href('orderfood.html')">新北訂餐網</div>
					<div style="width: 25px"></div>
					<div class="text large" id="titletext"></div>
				</div>
				<div class="flex">
					<input type="button" class="button light" id="exit" value="離開點餐">
				</div>
				<div class="navigationbarright">
					<div class="navigationbarrightphonebar" id="phonebar">
						<div class="navigationbarrightphonebarline"></div>
						<div class="navigationbarrightphonebarline"></div>
						<div class="navigationbarrightphonebarline"></div>
					</div>
					<div class="navigationbarrightbuttonlist" id="navbarbuttonlist">
						<input type="button" class="navigationbarbutton linkbutton" id="index" value="首頁">
						<input type="button" class="navigationbarbutton linkbutton" id="comment" value="訪客留言">
						<input type="button" class="navigationbarbutton linkbutton" id="orderfood" value="訪客訂餐">
						<input type="button" class="navigationbarbutton linkbutton" id="signin" value="網站管理">
					</div>
				</div>
			</div>
		`
		document.getElementById("main").innerHTML=`
			<div class="flex">
				<h1 id="title">訂單成立</h1>
				<div class="text big">
					${data["inorout"]=="in"?"內用":"外帶"} - ${data["count"]}人
				</div>
			</div>
		`
		document.getElementById("aside").innerHTML=``
		document.getElementById("footer").innerHTML=`
			<div></div>
			<button class="btn btn-primary" id="continue">繼續點餐</button>
		`

		document.getElementById("main").classList.remove("asideisshow")
		document.getElementById("aside").classList.remove("asideisshow")

		for(let i=0;i<data["orderlist"].length;i=i+1){
			if(!data["orderlist"][i]["ordered"]){
				orderlistinnerhtml=`
					${orderlistinnerhtml}
					<li class="item">
						<div class="flex">
							<div class="name">${data["orderlist"][i]["name"]}</div>
							<div class="num">${data["orderlist"][i]["price"]}元 x${data["orderlist"][i]["num"]}</div>
						</div>
						<div class="flex">
							<div></div>
							<div class="amount">${data["orderlist"][i]["price"]*data["orderlist"][i]["num"]}元</div>
						</div>
					</li>
				`
			}
		}

		updatedb(data)

		document.getElementById("main").innerHTML=`
			${document.getElementById("main").innerHTML}
			<ul class="list" id="orderList">
				${orderlistinnerhtml}
			</ul>
			<span class="total">總計: ${totalprice()} 元</span>
		`

		document.querySelectorAll(".categorylist li").forEach(function(event){
			event.onclick=function(){
				category=event.dataset.id

				history.pushState(null,null,"?page=order&orderaside="+orderaside+"&category="+category)
				main()
			}

			if(event.dataset.id==category){
				event.classList.add("active")
			}
		})

		document.getElementById("continue").onclick=function(){
			data={
				"inorout": "in",
				"count": 1,
				"orderlist": []
			}
			page="order"
			orderaside="yet"
			category="全部"
			updatedb(data)
			history.pushState(null,null,"?page=order")
			main()
		}

		document.getElementById("exit").onclick=function(){
			data={
				"inorout": "in",
				"count": 1,
				"orderlist": []
			}

			updatedb(data)

			href("index.html")
		}
	}else{ console.error("[ERROR]main error") }

	docgetid("phonebar").onclick=function(){
		console.log("in")
		if(docgetid("navbarbuttonlist").style.display=="block"){
			docgetid("navbarbuttonlist").style.display="none"
			docgetid("phonebar").style.rotate="0deg"
		}else{
			docgetid("navbarbuttonlist").style.display="block"
			docgetid("phonebar").style.rotate="90deg"
		}
	}

	innerhtml(domgetid("titletext"),`訪客訂餐`,false)

    onclick(".linkbutton",function(element,event){
        location.href=element.id+".html"
    })
}

if(!page){ page="order" }else{ page=page[1] }
if(!orderaside){ orderaside="yet" }else{ orderaside=orderaside[1] }
if(!category){ category="全部" }else{ category=category[1] }

if(!localStorage.getItem("54national_modulee"))
	localStorage.setItem("54national_modulee",JSON.stringify(data))
else
	data=JSON.parse(localStorage.getItem("54national_modulee"))

if(!localStorage.getItem("54national_modulee_all"))
	localStorage.setItem("54national_modulee_all",JSON.stringify(dataall))
else
	dataall=JSON.parse(localStorage.getItem("54national_modulee_all"))

main()

window.onbeforeunload=null
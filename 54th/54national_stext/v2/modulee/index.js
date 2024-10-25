let page=/page=([^&]+)/.exec(location.search)
let orderaside=/orderaside=([^&]+)/.exec(location.search)
let category=/category=([^&]+)/.exec(decodeURI(location.search))
let data={
	"id": 1,
	"table": "",
	"headcount": "",
	"orderlist": []
}
let studentdb
let classdb
let tablenotable
let tableid
let dbdata

function updatedb(data){
	// put data to key==2
	let transaction=dbdata.transaction(["user"],"readwrite")
	let objectstore=transaction.objectStore("user")
	let request=objectstore.put(data)

	request.onsuccess=function(){ console.log("Data updated successfully.") }

	request.onerror=function(event){ console.error("Data updated error.",event.target.errorCode) }
}

function getdb(){
	// get data from key==1
	let transaction=dbdata.transaction(["user"],"readwrite")
	let objectstore=transaction.objectStore("user")
	let request=objectstore.get(1)
	request.onsuccess=function(event){
		data=event.target.result
	}
	request.onerror=function(event){
		console.error("Data get error.",event.target.errorCode)
	}
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
	if(page=="index"){
		document.getElementById("header").innerHTML=`
			<h1 id="logo">LOGO</hi>
		`
		document.getElementById("main").innerHTML=`
			<div class="center">
				<div class="lable">請輸入桌號</div>
				<input type="text" id="table" name="table">
				<div class="lable">請輸入人數</div>
				<input type="text" id="headcount" name="headcount">
			</div>
		`
		document.getElementById("footer").innerHTML=`
			<button class="confirmbutton" id="confirm">確定</button>
		`

		document.getElementById("confirm").onclick=function(){
			data["table"]=document.getElementById("table").value
			data["headcount"]=document.getElementById("headcount").value

			updatedb(data)

			page="order"
			history.pushState(null,null,"?page=order")
			main()
		}
	}else if(page=="order"){
		let response=await fetch("foodlist.json")
		let foodlist=await response.json()
		let tempfoodlist=foodlist.filter(function(event){
			return event["type"]==category||category=="全部"
		})
		let menuinnerhtml=``
		let orderlistinnerhtml=``

		document.getElementById("header").innerHTML=`
			<h1 id="logo">LOGO</h1>
			<button id="exit">離開點餐</button>
			<form id="searchForm">
				<input type="search" name="search">
				<input type="submit" name="submit" value="查詢">
			</form>
		`
		document.getElementById("main").innerHTML=`
			<ul class="list categorylist" id="category">
				<li class="item type" data-id="全部">全部</li>
				<li class="item type" data-id="特餐">特餐</li>
				<li class="item type" data-id="吐司">吐司</li>
				<li class="item type" data-id="漢堡">漢堡</li>
				<li class="item type" data-id="點心">點心</li>
				<li class="item type" data-id="飲料">飲料</li>
			</ul>
		`
		document.getElementById("aside").innerHTML=`
			<div class="btn-group text-center fill">
				<button class="btn btn-primary" id="yetOrderBtn">未點</button>
				<button class="btn btn-primary" id="haveOrderBtn">已點</button>
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
				<div class="menuitem">
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
			<div id="menu">
			 	${menuinnerhtml}
			</div>
		`

		for(let i=0;i<data["orderlist"].length;i=i+1){
			if(orderaside=="yet"){
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
			}else{
				if(data["orderlist"][i]["ordered"]){
					orderlistinnerhtml=`
						${orderlistinnerhtml}
						<li class="item">
							<div class="flex">
								<div class="name">${data["orderlist"][i]["name"]}</div>
								<div class="num">x${data["orderlist"][i]["num"]}</div>
							</div>
							<div class="flex">
								<div></div>
								<div class="amount">${data["orderlist"][i]["price"]*data["orderlist"][i]["num"]}</div>
							</div>
						</li>
					`
				}
			}
		}

		document.getElementById(orderaside+"OrderBtn").classList.add("active")
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
					if(data["orderlist"][i]["id"]==event.dataset.id){
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
			page="pay"
			history.pushState(null,null,"?page=pay")
			main()
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

		document.getElementById("main").classList.add("asideisshow")
		document.getElementById("aside").classList.add("asideisshow")

		document.getElementById("exit").onclick=function(){
			data={
				"id": 1,
				"table": "",
				"headcount": "",
				"orderlist": []
			}

			updatedb(data)

			page="index"
			history.pushState(null,null,"?page=index")
			main()
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
	}else if(page=="pay"){
		let response=await fetch("foodlist.json")
		let foodlist=await response.json()
		let menuinnerhtml=``
		let orderlistinnerhtml=``

		document.getElementById("header").innerHTML=`
			<h1 id="logo">LOGO</h1>
			<button id="exit">離開點餐</button>
			<form id="searchForm">
				<input type="search" name="search">
				<input type="submit" name="submit" value="查詢">
			</form>
		`
		document.getElementById("main").innerHTML=`
			<h1 id="title">確認餐點</h1>
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
							<div class="num">x${data["orderlist"][i]["num"]}</div>
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
				"id": 1,
				"table": "",
				"headcount": "",
				"orderlist": []
			}

			updatedb(data)

			page="index"
			history.pushState(null,null,"?page=index")
			main()
		}
	}else if(page=="success"){
		let response=await fetch("foodlist.json")
		let foodlist=await response.json()
		let menuinnerhtml=``
		let orderlistinnerhtml=``

		document.getElementById("header").innerHTML=`
			<h1 id="logo">LOGO</h1>
			<button id="exit">離開點餐</button>
			<form id="searchForm">
				<input type="search" name="search">
				<input type="submit" name="submit" value="查詢">
			</form>
		`
		document.getElementById("main").innerHTML=`
			<h1 id="title">確認餐點</h1>
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
							<div class="num">x${data["orderlist"][i]["num"]}</div>
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
			for(let i=0;i<data["orderlist"].length;i=i+1){
				if(!data["orderlist"][i]["ordered"]){
					data["orderlist"][i]["ordered"]=true
				}
			}
			page="order"
			orderaside="yet"
			category="全部"
			history.pushState(null,null,"?page=order")
			main()
		}

		document.getElementById("return").onclick=function(){
			page="order"
			history.pushState(null,null,"?page=order&orderaside="+orderaside+"&category="+category)
			main()
		}

		document.getElementById("exit").onclick=function(){
			data={
				"id": 1,
				"table": "",
				"headcount": "",
				"orderlist": []
			}

			updatedb(data)

			page="index"
			history.pushState(null,null,"?page=index")
			main()
		}
	}else{ console.error("[ERROR]main error") }
}

if(!page){ page="index" }else{ page=page[1] }
if(!orderaside){ orderaside="yet" }else{ orderaside=orderaside[1] }
if(!category){ category="全部" }else{ category=category[1] }

let request=indexedDB.open("restaurant",1)

request.onsuccess=function(event){
	console.log(event.target.result)
	dbdata=event.target.result

	getdb()
	main()
}

request.onupgradeneeded=function(event){
	console.log(event.target.result)

	dbdata=event.target.result

	let objectstore=dbdata.createObjectStore("user",{ keyPath: "id" })
    objectstore.add(data)
}

request.onerror=function(event){ console.error(event.target.errorCode) }

window.onbeforeunload=null
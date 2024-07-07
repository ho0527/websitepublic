let maxpage=0
// search data START
let title=""
let minprice=""
let maxprice=""
let room=""
let minage=""
let maxage=""
let sortby=""
let order=""
let page=0
// search data END

// 查詢房屋
function main(){
	innerhtml("#houselist",`
		<div id="adslist"></div>
		<div id="normallist"></div>
	`,false) // 初始化房屋
	innerhtml("#page",``,false) // 初始化分頁切換器

	ajax("GET",AJAXURL+"user/house?title="+title+"&minprice="+minprice+"&maxprice="+maxprice+"&room="+room+"&minage="+minage+"&maxage="+maxage+"&sortby="+sortby+"&order="+order+"&page="+page,function(event,data){
		if(data["success"]){
			let row=data["data"]["houses"]

			maxpage=Math.floor(data["data"]["total_count"]/10)

			// house顯示
			for(let i=0;i<row.length;i=i+1){
				let adsinnerhtml=``
				let div=""

				if(row[i]["is_ads"]){
					adsinnerhtml=`精選房屋`
					div="#adslist"
				}else{
					adsinnerhtml=`一般房屋`
					div="#normallist"
				}

				innerhtml(div,`
					<div class="house grid" data-id="${row[i]["id"]}">
						<div class="houseimage"><img src="${row[i]["cover_image_url"]}" class="image"></div>
						<div class="houseads">${adsinnerhtml}</div>
						<div class="housetitle">${row[i]["title"]}</div>
						<div class="houseprice">價格: ${row[i]["price"]} / 單價: ${row[i]["price"]/row[i]["square"]}</div>
						<div class="housesquareroom">坪數: ${row[i]["square"]} / 房數: ${row[i]["room"]}</div>
					</div>
				`)
			}

			innerhtml("#houselist",`
				${getinnerhtml("adslist")}
				${getinnerhtml("normallist")}
			`,false)

			onclick(".house",function(element,event){
				href("house.html?id="+dataset(element),"id")
			})

			// page控制
			innerhtml("#page",`
				<input type="button" class="buttonghost" id="prev" value="<">
				${page+1}
				<input type="button" class="buttonghost" id="next" value=">">
			`)

			onclick("prev",function(element,event){
				if(0<page){
					page=page-1
					main()
				}
			})

			onclick("next",function(element,event){
				if(page<maxpage){
					page=page+1
					main()
				}
			})
		}else{
			alert(ERRORMESSAGE[data["message"]])
		}
	},[],[
		["Authorization","Bearer "+weblsget("51nationalmoduled-token")]
	])
}

main()

onclick("#newhouse",function(element,event){
	href("newhouse.html")
})

onclick("#submit",function(element,event){
	title=getvalue("keyword")
	if(getvalue("minprice")>=0){
		minprice=getvalue("minprice")
	}
	if(getvalue("maxprice")>=0){
		maxprice=getvalue("maxprice")
	}
	room=getvalue("room")
	if(getvalue("age")!=""){
		minage=getvalue("age").split("~")[0]
		maxage=getvalue("age").split("~")[1]
	}
	sortby=getvalue("sortby")
	order=getvalue("order")
	main()
})

onclick("#signout",function(element,event){
	ajax("POST",AJAXURL+"user/logout",function(event,data){
		if(data["success"]){
			alert("登出成功")
			weblsset("51nationalmoduled-userid",null)
			weblsset("51nationalmoduled-permission",null)
			weblsset("51nationalmoduled-token",null)
			href("index.html")
		}else{
			alert(ERRORMESSAGE[data["message"]])
		}
	},[],[
		["Authorization","Bearer "+weblsget("51nationalmoduled-token")]
	])
})
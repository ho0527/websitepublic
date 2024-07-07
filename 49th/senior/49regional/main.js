function main(request){
    docgetid("main").innerHTML=``

    oldajax("GET","api.php?product=").onload=function(){
        let productdata=JSON.parse(this.responseText)

        if(productdata["success"]){
            let product=productdata["data"]
            let url="api.php?game="
            if(request){
                url=url+"&search=&"+request
            }
            oldajax("GET",url).onload=function(){
                let data=JSON.parse(this.responseText)
                let maindata=""
                let edit=""

                if(data["success"]){
                    for(let i=0;i<product.length;i=i+1){
                        if(data["pin"][0]["version"]==product[i][0]){
                            projectid=i
                            break
                        }
                    }
                    let description=data["pin"][0]["description"]
                    if(description.length>200){
                        description=description.slice(0,200)+`
                            <input type="button" class="more" id="pinmore" value="more...">
                        `
                    }
                    if(weblsget("49regionalpermission")=="管理者"){
                        edit=`
                            <input type="button" class="edit" id="pinedit" value="修改">
                        `
                    }
                    maindata=`
                        ${maindata}
                        <div class="pindiv product macossectiondivy grid">
                            ${edit}
                            <div class="id">id: ${data["pin"][0]["id"]}</div>
                            <div class="date" style="${product[projectid][3]}">競賽日期: ${data["pin"][0]["date"]}</div>
                            <div class="description pindescription" style="${product[projectid][4]}">電競活動簡介: <br>${description}</div>
                            <div class="link" style="${product[projectid][5]}">活動新聞連結: ${data["pin"][0]["link"]}</div>
                            <div class="signupbutton mainsignupbutton" id="pinsignup" style="${product[projectid][6]}">${data["pin"][0]["signupbutton"]}</div>
                            <div class="name" style="${product[projectid][2]}">電競名稱: ${data["pin"][0]["name"]}</div>
                            <div class="picture" style="${product[projectid][1]}"><img src="${data["pin"][0]["picture"]}" class="image" draggable="false"></div>
                        </div>
                    `

                    for(let i=0;i<data["data"].length;i=i+1){
                        let projectid
                        for(let j=0;j<product.length;j=j+1){
                            if(data["data"][i]["version"]==product[j][0]){
                                projectid=j
                                break
                            }
                        }

                        if(weblsget("49regionalpermission")=="管理者"){
                            edit=`
                                <input type="button" class="pin" data-id="${i}" value="訂選">
                                <input type="button" class="edit" data-id="${i}" value="修改">
                            `
                        }

                        let description=data["data"][i]["description"]
                        if(description.length>100){
                            description=description.slice(0,100)+`
                                <input type="button" class="more" data-id="${i}" value="more...">
                            `
                        }

                        if(i%2==0){
                            maindata=`
                                ${maindata}
                                <div class="productdiv">
                                    <div class="productleft product macossectiondivy grid">
                                        ${edit}
                                        <div class="id">id: ${data["data"][i]["id"]}</div>
                                        <div class="date" style="${product[projectid][3]}">競賽日期: ${data["data"][i]["date"]}</div>
                                        <div class="description" style="${product[projectid][4]}">電競活動簡介: <br>${description}</div>
                                        <div class="link" style="${product[projectid][5]}">活動新聞連結: ${data["data"][i]["link"]}</div>
                                        <div class="signupbutton mainsignupbutton" data-id="${i}" style="${product[projectid][6]}">${data["data"][i]["signupbutton"]}</div>
                                        <div class="name" style="${product[projectid][2]}">電競名稱: ${data["data"][i]["name"]}</div>
                                        <div class="picture" style="${product[projectid][1]}"><img src="${data["data"][i]["picture"]}" class="image" draggable="false"></div>
                                    </div>
                            `
                        }else{
                            maindata=`
                                ${maindata}
                                    <div class="productright product macossectiondivy grid">
                                        ${edit}
                                        <div class="id">id: ${data["data"][i]["id"]}</div>
                                        <div class="date" style="${product[projectid][3]}">競賽日期: ${data["data"][i]["date"]}</div>
                                        <div class="description" style="${product[projectid][4]}">電競活動簡介: <br>${description}</div>
                                        <div class="link" style="${product[projectid][5]}">活動新聞連結: ${data["data"][i]["link"]}</div>
                                        <div class="signupbutton mainsignupbutton" data-id="${i}" style="${product[projectid][6]}">${data["data"][i]["signupbutton"]}</div>
                                        <div class="name" style="${product[projectid][2]}">電競名稱: ${data["data"][i]["name"]}</div>
                                        <div class="picture" style="${product[projectid][1]}"><img src="${data["data"][i]["picture"]}" class="image"></div>
                                    </div>
                                </div>
                            `
                        }
                        if(i%2==0&&data["data"].length-1==i){
                            maindata=`
                                ${maindata}
                                </div>
                            `
                        }
                    }

                    docgetid("main").innerHTML=maindata

                    docgetall(".signupbutton").forEach(function(event){
                        event.onclick=function(){
                            weblsset("49regionalgamedata",JSON.stringify(data["data"][event.dataset.id]))
                            location.href="game.html"
                        }
                    })

                    docgetid("pinsignup").onclick=function(){
                        weblsset("49regionalgamedata",JSON.stringify(data["pin"][0]))
                        location.href="game.html"
                    }

                    docgetall(".searchinput").forEach(function(event){
                        event.onkeydown=function(ketdownevent){
                            if(ketdownevent.key=="Enter"){
                                docgetid("submit").click()
                            }
                        }
                    })

                    docgetall(".pin").forEach(function(event){
                        event.onclick=function(){
                            oldajax("GET","api.php?pin=&id="+data["data"][event.dataset.id]["id"]).onload=function(){
                                let data=JSON.parse(this.responseText)
                                if(data["success"]){
                                    main()
                                }
                            }
                        }
                    })

                    docgetall(".edit").forEach(function(event){
                        event.onclick=function(){
                            let game=data["data"][event.dataset.id]
                            weblsset("49regionalproductdate",game["date"])
                            weblsset("49regionalproductdescription",game["description"])
                            weblsset("49regionalproductlink",game["link"])
                            weblsset("49regionalproductsignupbutton",game["signupbutton"])
                            weblsset("49regionalproductname",game["name"])
                            weblsset("49regionalproductfile",game["picture"])
                            weblsset("49regionalproductid",game["version"])
                            weblsset("49regionalproductedit","true")
                            weblsset("49regionalgameid",game["id"])

                            location.href="productindex.html"
                        }
                    })

                    docgetall("pinedit").onclick=function(){
                        let game=data["pin"][0]
                        weblsset("49regionalproductdate",game["date"])
                        weblsset("49regionalproductdescription",game["description"])
                        weblsset("49regionalproductlink",game["link"])
                        weblsset("49regionalproductsignupbutton",game["signupbutton"])
                        weblsset("49regionalproductname",game["name"])
                        weblsset("49regionalproductfile",game["picture"])
                        weblsset("49regionalproductid",game["version"])
                        weblsset("49regionalproductedit","true")
                        weblsset("49regionalgameid",game["id"])

                        location.href="productindex.html"
                    }

                    docgetall(".more").forEach(function(event){
                        event.onclick=function(){
                            event.parentNode.innerHTML=`
                                電競活動簡介: <br>
                                ${data["data"][event.dataset.id]["description"]}
                            `
                        }
                    })

                    if(docgetid("pinmore")){
                        docgetid("pinmore").onclick=function(){
                            this.parentNode.innerHTML=`
                                電競活動簡介: <br>
                                ${data["pin"][0]["description"]}
                            `
                        }
                    }
                }
            }
        }
    }
}

if(weblsget("49regionalpermission")=="管理者"){
    docgetid("navigationbar").innerHTML=`
        <div class="maintitle">電子競技網站管理</div>
        <div class="navigationbarbuttondiv">
            <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='main.html'" value="首頁">
            <input type="button" class="navigationbarbutton" onclick="location.href='productindex.html'" value="電競活動管理精靈">
            <input type="button" class="navigationbarbutton" onclick="location.href='admin.php'" value="會員管理">
            <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
        </div>
    `
}else{
    docgetid("navigationbar").innerHTML=`
        <div class="maintitle">電子競技網站管理</div>
        <div class="navigationbarbuttondiv">
            <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='main.html'" value="首頁">
            <input type="button" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
        </div>
    `
}

main()

docgetid("submit").onclick=function(){
    let start=docgetid("start").value
    let end=docgetid("end").value
    let keyword=docgetid("keyword").value

    if(!start){
        start="00000-01-01"
    }

    if(!end){
        end="99999-12-31"
    }

    main(`start=${start}&end=${end}&keyword=${keyword}`)
}
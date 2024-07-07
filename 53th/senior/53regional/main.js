let page

function main(url){
    page=weblsget("53regionalpage")
    if(!url){ url="/backend/53regional/getproduct?page="+page }
    docgetid("main").innerHTML=``
    oldajax("GET",url).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            let row=data["data"]["data"]
            let product=data["data"]["product"]
            let maininnerhtml=""
            let maxcount=Math.ceil(data["data"]["maxcount"]/4)-1
            if(maxcount<parseInt(weblsget("53regionalpage"))&&parseInt(weblsget("53regionalpage"))!=0){
                weblsset("53regionalpage",0)
                main(url+"&page="+weblsget("53regionalpage"))
            }
            for(let i=0;i<row.length;i=i+1){
                let classname="productleft"
                let edit=""

                for(let j=0;j<product.length;j=j+1){
                    if(row[i][7]==product[j][0]){
                        if(weblsget("53regionalpermission")=="管理者"){
                            edit=`<div class="id"><input type="button" class="edit" data-id="${row[i][0]}" value="修改"></div>`
                        }
                        if(i%2==0){
                            maininnerhtml=`
                                ${maininnerhtml}
                                <div class="productdiv">
                            `
                        }
                        if(i%2==1){
                            classname="productright"
                        }

                        maininnerhtml=`
                            ${maininnerhtml}
                            <div class="${classname} product macossectiondivy">
                                ${edit}
                                <div class="name macossectiondivy" style="${product[j][1]}">商品名稱: ${row[i][2]}</div>
                                <div class="cost macossectiondivy" style="${product[j][2]}">費用: ${row[i][4]}</div>
                                <div class="date macossectiondivy" style="${product[j][3]}">發布日期: ${row[i][5]}</div>
                                <div class="link macossectiondivy" style="${product[j][4]}">相關連結: ${row[i][6]}</div>
                                <div class="description macossectiondivy" style="${product[j][5]}">
                                    商品簡介<br>${row[i][3]}
                                </div>
                                <div class="picture macossectiondivy" style="${product[j][6]}"><img src="${row[i][1]}" class="image"></div>
                            </div>
                        `
                        if(i%2==1||row.length-1==i){
                            maininnerhtml=`
                                ${maininnerhtml}
                            </div>
                            `
                        }
                        break
                    }
                }
            }
            docgetid("main").innerHTML=`
                商品展示區 頁數: ${parseInt(weblsget("53regionalpage"))+1}<br>
                <div class="searchform">
                    <input type="text" id="keyword" placeholder="關鍵字">
                    <input type="number" id="start" placeholder="最低價位">~
                    <input type="number" id="end" placeholder="最高價位">
                    <input type="button" id="submit" value="查詢"><br><br>
                    <input type="button" class="numberspeedsearch" value="1000~2000">
                    <input type="button" class="numberspeedsearch" value="2000~5000">
                    <input type="button" class="numberspeedsearch" value="5000~10000">
                </div><br>
                <input type="button" id="pagefirst" value="到最前一頁">
                <input type="button" id="pageprev" value="上一頁">
                <input type="button" id="pagenext" value="下一頁">
                <input type="button" id="pageend" value="到最後一頁">
                ${maininnerhtml}
            `
            docgetid("pagefirst").onclick=function(){
                weblsset("53regionalpage",0)
                main(url+"&page="+weblsget("53regionalpage"))
            }

            docgetid("pageprev").onclick=function(){
                if(weblsget("53regionalpage")>0){
                    weblsset("53regionalpage",parseInt(weblsget("53regionalpage"))-1)
                    main(url+"&page="+weblsget("53regionalpage"))
                }
            }

            docgetid("pagenext").onclick=function(){
                if(weblsget("53regionalpage")<maxcount){
                    weblsset("53regionalpage",parseInt(weblsget("53regionalpage"))+1)
                    main(url+"&page="+weblsget("53regionalpage"))
                }
            }

            docgetid("pageend").onclick=function(){
                weblsset("53regionalpage",maxcount)
                main(url+"&page="+weblsget("53regionalpage"))
            }

            docgetall(".numberspeedsearch").forEach(function(event){
                event.onclick=function(){
                    main("/backend/53regional/getproduct?page="+page+"&start="+parseInt(event.value.split("~")[0])+"&end="+parseInt(event.value.split("~")[1]))
                }
            })

            docgetid("submit").onclick=function(){
                main("/backend/53regional/getproduct?page="+page+"&keyword="+docgetid("keyword").value+"&start="+docgetid("start").value+"&end="+docgetid("end").value)
            }

            docgetall(".edit").forEach(function(event){
                event.onclick=function(){
                    oldajax("GET","/backend/53regional/getproduct?id="+event.dataset.id).onload=function(){
                        let data=JSON.parse(this.responseText)
                        if(data["success"]){
                            row=data["data"][0]
                            weblsset("53regionalproductfile",row[1])
                            weblsset("53regionalproductname",row[2])
                            weblsset("53regionalproductdescription",row[3])
                            weblsset("53regionalproductcost",row[4])
                            weblsset("53regionalproductlink",row[6])
                            weblsset("53regionalproductid",row[7])
                            weblsset("53regionalproductedit","true")
                            weblsset("53regionalcoffeeid",row[0])
                            location.href="productindex.html"
                        }else{
                            alert(data["data"])
                        }
                    }
                }
            })
        }else{
            alert(data["data"])
        }
    }
}

if(weblsget("53regionalpermission")=="管理者"){
    docgetid("navigationbarbuttondiv").innerHTML=`
        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='main.html'" value="首頁">
        <input type="button" class="navigationbarbutton" onclick="location.href='productindex.html'" value="上架商品">
        <input type="button" class="navigationbarbutton" onclick="location.href='admin.html'" value="會員管理">
        <input type="button" class="navigationbarbutton" id="logout" value="登出">
    `
}else{
    docgetid("navigationbarbuttondiv").innerHTML=`
        <input type="button" class="navigationbarbutton navigationbarselect" onclick="location.href='main.html'" value="首頁">
        <input type="button" class="navigationbarbutton" id="logout" value="登出">
    `
}

if(!isset(weblsget("53regionalpage"))){ weblsset("53regionalpage",0) }

main()

startmacossection()
oldajax("GET","/backend/53regional/gettemplate").onload=function(){
    let data=JSON.parse(this.responseText)

    if(data["success"]){
        let row=data["data"]
        let maindata=""

        for(let i=0;i<row.length;i=i+1){
            if(i%2==0){
                maindata=`
                    ${maindata}
                    <div class="productdiv">
                        <div class="productleft product" id="${row[i][0]}">
                            <div class="id">版型: ${row[i][0]}</div>
                            <div class="name" style="${row[i][1]}">商品名稱</div>
                            <div class="cost" style="${row[i][2]}">費用</div>
                            <div class="date" style="${row[i][3]}">發布日期</div>
                            <div class="link" style="${row[i][4]}">相關連結</div>
                            <div class="description" style="${row[i][5]}">商品簡介</div>
                            <div class="picture" style="${row[i][6]}">圖片</div>
                        </div>
                `
            }else{
                maindata=`
                    ${maindata}
                        <div class="productright product" id="${row[i][0]}">
                            <div class="id">版型: ${row[i][0]}</div>
                            <div class="name" style="${row[i][1]}">商品名稱</div>
                            <div class="cost" style="${row[i][2]}">費用</div>
                            <div class="date" style="${row[i][3]}">發布日期</div>
                            <div class="link" style="${row[i][4]}">相關連結</div>
                            <div class="description" style="${row[i][5]}">商品簡介</div>
                            <div class="picture" style="${row[i][6]}">圖片</div>
                        </div>
                    </div>
                `
            }
            if(i%2==0&&row.length-1==i){
                maindata=`
                    ${maindata}
                    </div>
                `
            }
        }

        docgetid("main").innerHTML=maindata
        docgetid(weblsget("53regionalproductid")).style.backgroundColor="rgb(203, 203, 38)"

        docgetall(".product").forEach(function(event){
            event.onclick=function(){
                docgetall(".product").forEach(function(event){
                    event.style.backgroundColor=""
                    event.classList.remove("macossectiondivy")
                })
                weblsset("53regionalproductid",event.id)
                event.style.backgroundColor="rgb(203, 203, 38)"
                event.classList.add("macossectiondivy")
            }
        })
    }
}

docgetid("index").classList.add("selectbut")

startmacossection()
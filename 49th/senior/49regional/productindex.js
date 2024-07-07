if(weblsget("49regionalproductid")==null){
    weblsset("49regionalproductid",1)
}

oldajax("GET","api.php?product=").onload=function(){
    let data=JSON.parse(this.responseText)

    if(data["success"]){
        let row=data["data"]
        let maindata=""

        for(let i=0;i<row.length;i=i+1){
            if(i%2==0){
                maindata=`
                    ${maindata}
                    <div class="productdiv">
                        <div class="productleft product macossectiondiv grid" id="${row[i][0]}">
                            <div class="id">版型: ${row[i][0]}</div>
                            <div class="date" id="date" style="${row[i][3]}">競賽日期</div>
                            <div class="description" id="description" style="${row[i][4]}">電競活動 簡介</div>
                            <div class="link" id="link" style="${row[i][5]}">活動新聞連結</div>
                            <div class="signupbutton" id="button" style="${row[i][6]}">報名(按鈕)</div>
                            <div class="name" id="name" style="${row[i][2]}">電競名稱</div>
                            <div class="picture" id="picture" style="${row[i][1]}">圖片</div>
                        </div>
                `
            }else{
                maindata=`
                    ${maindata}
                        <div class="productright product macossectiondiv grid" id="${row[i][0]}">
                            <div class="id">版型: ${row[i][0]}</div>
                            <div class="date" id="date" style="${row[i][3]}">競賽日期</div>
                            <div class="description" id="description" style="${row[i][4]}">電競活動 簡介</div>
                            <div class="link" id="link" style="${row[i][5]}">活動新聞連結</div>
                            <div class="signupbutton" id="button" style="${row[i][6]}">報名(按鈕)</div>
                            <div class="name" id="name" style="${row[i][2]}">電競名稱</div>
                            <div class="picture" id="picture" style="${row[i][1]}">圖片</div>
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
        docgetid(weblsget("49regionalproductid")).style.backgroundColor="rgb(203, 203, 38)"

        docgetall(".product").forEach(function(event){
            event.onclick=function(){
                docgetall(".product").forEach(function(event){
                    event.style.backgroundColor=""
                })
                weblsset("49regionalproductid",event.id)
                event.style.backgroundColor="rgb(203, 203, 38)"
            }
        })
    }
}

startmacossection()
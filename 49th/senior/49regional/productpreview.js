if(weblsget("49regionalproductid")==null){
    weblsset("49regionalproductid",1)
}

oldajax("GET","api.php?product=&id="+weblsget("49regionalproductid")).onload=function(){
    let data=JSON.parse(this.responseText)

    if(data["success"]){
        let row=data["data"][0]
        docgetid("main").innerHTML=`
            <div class="main productpreviewmain grid">
                <div class="date" id="date" style="${row[3]}">競賽日期: ${weblsget("49regionalproductdate")}</div>
                <div class="description" id="description" style="${row[4]}">電競活動簡介: ${weblsget("49regionalproductdescription")}</div>
                <div class="link" id="link" style="${row[5]}">活動新聞連結: ${weblsget("49regionalproductlink")}</div>
                <div class="signupbutton" id="button" style="${row[6]}">${weblsget("49regionalproductsignupbutton")}</div>
                <div class="name" id="name" style="${row[2]}">電競名稱: ${weblsget("49regionalproductname")}</div>
                <div class="picture" id="picture" style="${row[1]}"><img src="${weblsget("49regionalproductfile")}" class="image"></div>
            </div>
        `
    }
}

startmacossection()
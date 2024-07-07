oldajax("GET","/backend/53regional/gettemplate?id="+weblsget("53regionalproductid")).onload=function(){
    let data=JSON.parse(this.responseText)

    if(data["success"]){
        let row=data["data"][0]
        docgetid("main").innerHTML=`
            <div class="name" style="${row[1]}">商品名稱: ${weblsget("53regionalproductname")}</div>
            <div class="cost" style="${row[2]}">費用: ${weblsget("53regionalproductcost")}</div>
            <div class="date" style="${row[3]}">發布日期: 發布後產生</div>
            <div class="link" style="${row[4]}">相關連結: ${weblsget("53regionalproductlink")}</div>
            <div class="description" style="${row[5]}">
                商品簡介:<br>
                ${weblsget("53regionalproductdescription")}
            </div>
            <div class="picture" style="${row[6]}"><img src="${weblsget("53regionalproductfile")}" class="image"></div>
        `
    }
}

docgetid("preview").classList.add("selectbut")

startmacossection()
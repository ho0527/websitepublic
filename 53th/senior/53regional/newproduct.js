let left=3
let right=3

divsort("list",".newproduct")

document.getElementById("newtemplate").onclick=function(){
    let name=""
    let cost=""
    let date=""
    let link=""
    let description=""
    let picture=""
    document.querySelectorAll(".list").forEach(function(event){
        if(event.parentNode.classList[0]=="newproductleft"){
            if(event.id=="name"){
                name="grid-column: 1/50;grid-row: "+left+"/"+(left+2)
                left=left+3
            }else if(event.id=="cost"){
                cost="grid-column: 1/50;grid-row: "+left+"/"+(left+2)
                left=left+3
            }else if(event.id=="date"){
                date="grid-column: 1/50;grid-row: "+left+"/"+(left+2)
                left=left+3
            }else if(event.id=="link"){
                link="grid-column: 1/50;grid-row: "+left+"/"+(left+2)
                left=left+3
            }else if(event.id=="description"){
                description="grid-column: 1/50;grid-row: "+left+"/"+(left+6)
                left=left+7
            }else if(event.id=="picture"){
                picture="grid-column: 1/50;grid-row: "+left+"/"+(left+12)
                left=left+12
            }
        }else{
            if(event.id=="name"){
                name="grid-column: 50/100;grid-row: "+right+"/"+(right+2)
                right=right+3
            }else if(event.id=="cost"){
                cost="grid-column: 50/100;grid-row: "+right+"/"+(right+2)
                right=right+3
            }else if(event.id=="date"){
                date="grid-column: 50/100;grid-row: "+right+"/"+(right+2)
                right=right+3
            }else if(event.id=="link"){
                link="grid-column: 50/100;grid-row: "+right+"/"+(right+2)
                right=right+3
            }else if(event.id=="description"){
                description="grid-column: 50/100;grid-row: "+right+"/"+(right+6)
                right=right+7
            }else if(event.id=="picture"){
                picture="grid-column: 50/100;grid-row: "+right+"/"+(right+12)
                right=right+12
            }
        }
    })

    oldajax("POST","/backend/53regional/newtemplate",JSON.stringify({
        "submit": true,
        "name": name,
        "cost": cost,
        "date": date,
        "link": link,
        "description": description,
        "picture": picture,
    })).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert("新增成功")
            localStorage.setItem("53regionalproductid",data["data"])
            location.href="productindex.html"
        }else{
            alert("伺服器出現問題請重新填寫")
        }
    }
}

docgetid("new").classList.add("selectbut")

startmacossection()
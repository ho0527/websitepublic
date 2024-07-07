function clear(){
    docgetid("name").value=""
    docgetid("englishname").value=""
}

function submit(key,id){
    let method
    let url
    let successmessage
    if(key=="new"){
        method="POST"
        url="/backend/46nationalmoduled/mangerstation/"
        successmessage="新增成功!"
    }else{
        method="PUT"
        url="/backend/46nationalmoduled/mangerstation/"+id
        successmessage="修改成功!"
    }
    oldajax(method,url,JSON.stringify({
        "englishname": docgetid("englishname").value,
        "name": docgetid("name").value,
    }),[
        ["Content-Type","application/json"]
    ]).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert(successmessage)
            location.reload()
        }else{
            alert(data["data"])
        }
    }
}

if(!weblsget("46nationalmoduleduserid")){
    alert("請先登入")
    location.href="login.html"
}

lightbox("#new","lightbox",function(){
    return `
        路線名稱: <input type="text" class="lightboxinput" id="name"><br><br>
        路線英文名: <input type="text" class="lightboxinput" id="englishname"><br><br>
        <input type="button" class="button" id="close" value="返回">
        <input type="button" class="button" onclick="clear()" value="清除">
        <input type="button" class="button" onclick="submit('new')" value="送出">
    `
},"close")

oldajax("GET","/backend/46nationalmoduled/mangerstation/").onload=function(){
    let data=JSON.parse(this.response)
    if(data["success"]){
        let row=data["data"]
        for(let i=0;i<row.length;i=i+1){
            let tr=doccreate("tr")
            tr.innerHTML=`
                <td class="admintd">${row[i][0]}</td>
                <td class="admintd">${row[i][2]}</td>
                <td class="admintd">
                    <input type="button" class="bluebutton editbutton" data-id="${i}" value="編輯">
                    <input type="button" class="bluebutton delbutton" data-id="${row[i][0]}" value="刪除">
                </td>
            `
            docappendchild("table",tr)
        }

        lightbox(".editbutton","lightbox",function(event){
            let id=event.dataset.id
            return `
                路線id: <input type="text" class="lightboxinput" value="${row[id][0]}" readonly><br><br>
                路線名稱: <input type="text" class="lightboxinput" id="name" value="${row[id][2]}"><br><br>
                路線英文名: <input type="text" class="lightboxinput" id="englishname" value="${row[id][1]}"><br><br>
                <input type="button" class="button" id="close" value="返回">
                <input type="button" class="button" onclick="clear()" value="清除">
                <input type="button" class="button" onclick="submit('edit',${row[id][0]})" value="送出">
            `
        },"close")

        docgetall(".delbutton").forEach(function(event){
            event.onclick=function(){
                oldajax("DELETE","/backend/46nationalmoduled/mangerstation/"+event.dataset.id).onload=function(){
                    let data=JSON.parse(this.responseText)
                    if(data["success"]){
                        alert("刪除成功!")
                        location.reload()
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

startmacossection()
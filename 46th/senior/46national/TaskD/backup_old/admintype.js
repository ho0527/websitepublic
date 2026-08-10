function clear(){
    docgetid("name").value=""
    docgetid("passenger").value=""
}

function postsubmit(){
    oldajax("POST","/backend/46nationalmoduled/mangertraintype/",JSON.stringify({
        "name": docgetid("name").value,
        "passenger": docgetid("passenger").value,
    }),[
        ["Content-Type","application/json"]
    ]).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert("新增成功!")
            location.reload()
        }else{
            alert(data["data"])
        }
    }
}

function putsubmit(){
    oldajax("PUT","/backend/46nationalmoduled/mangertraintype/"+docgetid("id").dataset.id,JSON.stringify({
        "name": docgetid("name").value,
        "passenger": docgetid("passenger").value,
    }),[
        ["Content-Type","application/json"]
    ]).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            alert("修改成功!")
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
        車種名稱: <input type="text" class="lightboxinput" id="name"><br><br>
        乘客乘載量: <input type="text" class="lightboxinput" id="passenger"><br><br>
        <input type="button" class="button" id="close" value="返回">
        <input type="button" class="button" onclick="clear()" value="清除">
        <input type="button" class="button" onclick="postsubmit()" value="送出">
    `
},"close")

oldajax("GET","/backend/46nationalmoduled/mangertraintype/").onload=function(){
    let data=JSON.parse(this.response)
    if(data["success"]){
        row=data["data"]
        for(let i=0;i<row.length;i=i+1){
            let tr=doccreate("tr")
            tr.innerHTML=`
                <td class="admintd">${row[i][0]}</td>
                <td class="admintd">${row[i][1]}</td>
                <td class="admintd">${row[i][2]}</td>
                <td class="admintd">
                    <input type="button" class="bluebutton editbutton" data-id=${i} value="編輯">
                    <input type="button" class="bluebutton delbutton" data-id="${row[i][0]}" value="刪除">
                </td>
            `
            docappendchild("table",tr)
        }

        lightbox(".editbutton","lightbox",function(event){
            let id=event.dataset.id
            return `
                車種id: <input type="text" class="lightboxinput" id="id" data-id="${row[id][0]}" value="${row[id][0]}" readonly><br><br>
                車種名稱: <input type="text" class="lightboxinput" id="name" value="${row[id][1]}"><br><br>
                乘客乘載量: <input type="text" class="lightboxinput" id="passenger" value="${row[id][2]}"><br><br>
                <input type="hidden" class="lightboxinput" name="id" value="${row[id][0]}">
                <input type="button" class="button" id="close" value="返回">
                <input type="button" class="button" onclick="clear()" value="清除">
                <input type="button" class="button" onclick="putsubmit()" value="送出">
            `
        },"close")

        docgetall(".delbutton").forEach(function(event){
            event.onclick=function(){
                oldajax("DELETE","/backend/46nationalmoduled/mangertraintype/"+event.dataset.id).onload=function(){
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
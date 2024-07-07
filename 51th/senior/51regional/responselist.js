ajax("GET","api.php?getresponselist=",function(event){
    let data=JSON.parse(event.responseText)

    docgetid("responselist").innerHTML=`
        <div class="responselist grid">
            <div class="responseno">編號</div>
            <div class="responseuser">回應者id</div>
            <div class="responsedef">功能</div>
        </div>
    `

    if(data["success"]){
        let row=data["data"]
        if(row.length>0){
            for(let i=0;i<row.length;i=i+1){
                let id=row[i][1]
                if(row[i][1]==""){
                    id="訪客"
                }
                docgetid("responselist").innerHTML=`
                    ${docgetid("responselist").innerHTML}
                    <div class="responselist grid">
                        <div class="responseno">${row[i][0]}</div>
                        <div class="responseuser">${id}</div>
                        <div class="responsedef">
                            <input type="button" class="stbutton light view" data-id="${row[i][0]}" value="查看">
                            <input type="button" class="stbutton error delete" data-id="${row[i][0]}" value="刪除">
                        </div>
                    </div>
                `
            }
    
            docgetall(".view").forEach(function(event){
                event.onclick=function(){
                    weblsset("51regionalresponseid",event.dataset.id)
                    location.href="response.html"
                }
            })
    
            docgetall(".delete").forEach(function(event){
                event.onclick=function(){
                    if(confirm("確定刪除?")){
                        location.href="api.php?delresponse=&id="+event.dataset.id
                    }
                }
            })
        }else{
            docgetid("responselist").innerHTML=`
                ${docgetid("responselist").innerHTML}
                <div class="responselist grid">
                    <div class="responsewarning">暫無回應</div>
                </div>
            `
        }
    }else{
        alert(data["data"])
    }
})

docgetid("seeall").onclick=function(){
    weblsset("51regionalresponseid","all")
    location.href="response.html"
}
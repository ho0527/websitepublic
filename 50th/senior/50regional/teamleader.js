function endcheck(type,projectid){
    if(type=="allfinish"){
        if(confirm("所有人皆完成投票，是否確定結束?")){
            location.href="api.php?key=planchange&value=check&id="+projectid
        }else{
            location.reload()
        }
    }else{
        if(confirm("確定結束?")){
            location.href="api.php?key=planchange&value=check&id="+projectid
        }else{
            location.reload()
        }
    }
}

docgetall(".end").forEach(function(event){
    event.onclick=function(){
        let projectid=this.dataset.id
        let check=true
        oldajax("GET","api.php?projectdata=").onload=function(){
            let data=JSON.parse(this.responseText)
            let table=`
                <div class="lightboxtitle textcenter">尚未評分列表</div>
                <table class="lightboxtable textcenter macossectiondivy">
                    <tr>
                        <td class="maintd">專案id</td>
                        <td class="maintd">職位</td>
                        <td class="maintd">planid</td>
                        <td class="maintd">未評分使用者列表</td>
                    </tr>
            `

            for(let i=0;i<data.length;i=i+1){
                if(data[i]["projectid"]==projectid){
                    if(data[i].status=="leader"){
                        if(Array.isArray(data[i].data)){
                            for(let j=0;j<data[i].data.length;j=j+1){
                                if(data[i]["data"][j]["message"]!="true"){
                                    table=table+`
                                        <tr>
                                            <td class="maintd">${data[i]["projectid"]}</td>
                                            <td class="maintd">${data[i]["status"]}</td>
                                            <td class="maintd">${data[i]["data"][j]["planid"]}</td>
                                            <td class="maintd">${data[i]["data"][j]["userid"].join(", ")}</td>
                                        </tr>
                                    `
                                    check=false
                                }
                            }
                        }
                    }
                }
            }

            table=table+`
                <table class="table">
                <div class="lightboxbuttondiv textcenter">
                    <input type="button" class="stbutton light" id="back" value="返回">
                    <input type="button" class="stbutton negative" onclick="endcheck('notfinish',${projectid})" value="不管他們確定結束">
                </div>
            `

            if(check){
                endcheck("allfinish",projectid)
            }else{
                lightbox(null,"lightbox",function(){
                    return table
                },"back")
            }

        }
    }
})

startmacossection()
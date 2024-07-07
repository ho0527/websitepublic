oldajax("GET","api.php?projectdata=").onload=function(){
    let data=JSON.parse(this.responseText)
    for(let i=0;i<data.length;i=i+1){
        if(data[i].status=="leader"){
            if(Array.isArray(data[i].data)){
                for(let j=0;j<data[i].data.length;j=j+1){
                    let tr=doccreate("tr")
                    let td4data=""
                    if(data[i]["data"][j]["message"]=="true"){
                        td4data="已完成評分"
                    }else{
                        td4data="userid: "+data[i]["data"][j]["userid"].join(", ")+" 尚未評分"
                    }

                    tr.innerHTML=`
                        <td class="maintd">${data[i]["projectid"]}</td>
                        <td class="maintd">${data[i]["status"]}</td>
                        <td class="maintd">${data[i]["data"][j]["planid"]}</td>
                        <td class="maintd">${td4data}</td>
                        <td class="maintd"></td>
                    `
                    docappendchild("table",tr)
                }
            }else{
                let tr=doccreate("tr")
                tr.innerHTML=`
                    <td class="maintd">${data[i]["projectid"]}</td>
                    <td class="maintd">${data[i]["status"]}</td>
                    <td class="maintd sttext negative bold" colspan="3">${data[i]["data"]}</td>
                `
                docappendchild("table",tr)
            }
        }else if(data[i].status=="member"){
            if(Array.isArray(data[i].data)){
                for(let j=0;j<data[i].data.length;j=j+1){
                    let tr=doccreate("tr")
                    let td4data=""
                    let td5data=""
                    if(data[i]["data"][j]["message"]=="true"){
                        td4data="已完成評分"
                    }else{
                        td4data="尚未評分"
                        td5data=`
                            <input type="button" class="stbutton outline" onclick="location.href='score.php?key=plan&id=${data[i]["data"][j]["planid"]}&projectid=${data[i]["projectid"]}'" value="去評分">
                        `
                    }

                    tr.innerHTML=`
                        <td class="maintd">${data[i]["projectid"]}</td>
                        <td class="maintd">${data[i]["status"]}</td>
                        <td class="maintd">${data[i]["data"][j]["planid"]}</td>
                        <td class="maintd">${td4data}</td>
                        <td class="maintd">${td5data}</td>
                    `
                    docappendchild("table",tr)
                }
            }else{
                let tr=doccreate("tr")
                tr.innerHTML=`
                    <td class="maintd">${data[i]["projectid"]}</td>
                    <td class="maintd">${data[i]["status"]}</td>
                    <td class="maintd sttext negative bold" colspan="3">${data[i]["data"]}</td>
                `
                docappendchild("table",tr)
            }
        }
    }
}

startmacossection()
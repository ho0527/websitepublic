ajax("GET",ajaxurl+"/getroomorderlist",function(event,data){
    if(data["success"]){
        let row=data["data"]

        for(let i=0;i<row.length;i=i+1){
            docgetid("maintable").innerHTML=`
                ${docgetid("maintable").innerHTML}
                <tr>
                    <td class="adminttabletd">${row[i]["no"]}</td>
                    <td class="adminttabletd textcenter" colspan="2">${row[i]["startdate"]}~${row[i]["enddate"]}</td>
                    <td class="adminttabletd">${row[i]["roomno"]}</td>
                    <td class="adminttabletd">${row[i]["username"]}</td>
                    <td class="adminttabletd">${row[i]["phone"]}</td>
                    <td class="adminttabletd">${row[i]["email"]}</td>
                    <td class="adminttabletd">${row[i]["totalprice"]}</td>
                    <td class="adminttabletd">${row[i]["deposit"]}</td>
                    <td class="adminttabletd macossectiondivx">${row[i]["ps"]}</td>
                    <td class="adminttabletd textcenter">
                        <input type="button" class="button outline editbutton" data-id="${row[i]["id"]}" value="修改">
                        <input type="button" class="button error deletebutton" data-id="${row[i]["id"]}" value="刪除">
                    </td>
                </tr>
            `
        }

        onclick(".editbutton",function(element2,event2){
            weblsset("54regionaladminroomeditid",element2.dataset.id)
            location.href="bookroom.html"
        })

        onclick(".deletebutton",function(element,event){
            if(confirm("確定要刪除嗎?")){
                ajax("DELETE",ajaxurl+"/deleteroomorder/"+element.dataset.id,function(event2,data2){
                    if(data2["success"]){
                        alert("刪除成功")
                        location.reload()
                    }
                })
            }
        })
    }
})
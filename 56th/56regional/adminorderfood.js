ajax("GET",ajaxurl+"/getfoodorderlist",function(event,data){
    if(data["success"]){
        let row=data["data"]
        let maininnerhtml=``

        for(let i=0;i<row.length;i=i+1){
            let orderdata=row[i]["orderdata"].split("，")
            let orderdatadata=[]

            for(let j=0;j<orderdata.length;j=j+1){
                let thisorder=orderdata[j].split(",")
                orderdatadata.push(thisorder[0]+"*"+thisorder[2])
            }

            maininnerhtml=`
                ${maininnerhtml}
                <tr class="textcenter">
                    <td class="tabletd" rowspan="${orderdatadata.length}">${row[i]["username"]}</td>
                    <td class="tabletd">${orderdatadata[0]}</td>
                    <td class="tabletd"  rowspan="${orderdatadata.length}">${row[i]["totalprice"]}</td>
                    <td class="tabletd"  rowspan="${orderdatadata.length}">
                        <input type="button" class="button outline editbutton" data-id="${row[i]["id"]}" value="修改">
                        <input type="button" class="button error deletebutton" data-id="${row[i]["id"]}" value="刪除">
                    </td>
                </tr>
            `

            for(let j=1;j<orderdatadata.length;j=j+1){
                maininnerhtml=`
                    ${maininnerhtml}
                    <tr class="textcenter">
                        <td class="tabletd">${orderdatadata[j]}</td>
                    </tr>
                `
            }
        }

        docgetid("maintable").innerHTML=`
            <tr class="textcenter">
                <th class="adminttabletd">使用者名稱</th>
                <th class="adminttabletd">購買內容</th>
                <th class="adminttabletd">總價格</th>
                <th class="adminttabletd">功能區</th>
            </tr>
            ${maininnerhtml}
        `

        onclick(".editbutton",function(element,event){
            ajax("GET",ajaxurl+"/getfoodlist",function(event,data){
                let foodlist=data["data"]
                ajax("GET",ajaxurl+"/getfoodorder/"+element.dataset.id,function(event2,data2){
                    let foodorderlist=data2["data"]["orderdata"].split("，")

                    lightbox(null,"lightbox",function(){
                        let innerhtml=``

                        for(let i=0;i<foodlist.length;i=i+1){
                            let ordervalue=0

                            for(let j=0;j<foodorderlist.length;j=j+1){
                                let thisorder=foodorderlist[j].split(",")

                                if(thisorder[0]==foodlist[i]["name"]){
                                    ordervalue=thisorder[2]
                                }
                            }

                            innerhtml=`
                                ${innerhtml}
                                <tr class="textcenter">
                                    <td class="tabletd">${foodlist[i]["id"]}</td>
                                    <td class="tabletd">${foodlist[i]["name"]}</td>
                                    <td class="tabletd">
                                        <div class="input">
                                            <input type="number" class="ordercount textcenter" id="count_${i}" data-id="${foodlist[i]["id"]}" data-name="${foodlist[i]["name"]}" data-price="${foodlist[i]["price"]}" value="${ordervalue}" min="0" step="1">
                                        </div>
                                    </td>
                                </tr>
                            `
                        }

                        return `
                            <div class="textcenter adminfoodlightbox">
                                <div class="adminfoodtitle">編輯訂餐</div>
                                <div class="textleft inputdiv" id="namediv">
                                    <div class="textlabel light">餐點名稱:</div>
                                    <div class="input light">
                                        <input type="text" id="username" value="${data2["data"]["username"]}">
                                    </div>
                                </div>
                                <div class="adminorderfoodlightboxmain">
                                    <div class="orderfoodlist macossectiondivy">
                                        <table class="table outborder light" id="maintable">
                                            <tr class="textcenter">
                                                <th class="adminttabletd">#</th>
                                                <th class="adminttabletd">名稱</th>
                                                <th class="adminttabletd">數量</th>
                                            </tr>
                                            ${innerhtml}
                                        </table>
                                    </div>
                                </div>
                                <div class="textcenter">
                                    <input type="button" class="button light" id="close" value="取消">
                                    <input type="button" class="button light" id="submit" value="送出">
                                </div>
                            </div>
                        `
                    },"close",true,"none")

                    onclick("#submit",function(element2,event2){
                        let orderdata=[]
                        let totalprice=0

                        docgetall(".ordercount").forEach(function(event2){
                            if(event2.value!="0"){
                                orderdata.push([event2.dataset.name,event2.dataset.price,event2.value])
                                totalprice=totalprice+parseFloat(event2.dataset.price)*parseInt(event2.value)
                            }
                        })

                        ajax("PUT",ajaxurl+"/editfoodorder/"+element.dataset.id,function(event,data){
                            if(data["success"]){
                                alert("修改成功")
                                location.reload()
                            }else{
                                docgetid(data["errorkey"]+"error").innerHTML=data["data"]
                                docgetid(data["errorkey"]).parentNode.classList.add("inputerror")
                            }
                        },JSON.stringify({
                            "username": docgetid("username").value,
                            "orderdata": orderdata.join("，"),
                            "totalprice": totalprice
                        }))
                    })

                    onkeydown("input[type='text']",function(element2,event2){
                        if(event2.key=="Enter"){
                            docgetid("submit").click()
                        }
                    })

                    onkeydown("input[type='number']",function(element2,event2){
                        if(event2.key=="Enter"){
                            docgetid("submit").click()
                        }
                    })
                })
            })
        })

        onclick(".deletebutton",function(element,event){
            if(confirm("確定要刪除嗎?")){
                ajax("DELETE",ajaxurl+"/deletefoodorder/"+element.dataset.id,function(event,data){
                    if(data["success"]){
                        alert("刪除成功")
                        location.reload()
                    }else{
                        alert(data["data"])
                    }
                })
            }
        })
    }else{
        alert(data["data"])
    }
})
ajax("GET",ajaxurl+"/getfoodlist",function(event,data){
    if(data["success"]){
        let row=data["data"]
        for(let i=0;i<row.length;i=i+1){
            docgetid("maintable").innerHTML=`
                ${docgetid("maintable").innerHTML}
                <tr class="textcenter">
                    <td class="adminttabletd">${row[i]["name"]}</td>
                    <td class="adminttabletd">${row[i]["price"]}</td>
                    <td class="adminttabletd">
                        <input type="button" class="button outline editbutton" data-id="${row[i]["id"]}" value="修改">
                        <input type="button" class="button error deletebutton" data-id="${row[i]["id"]}" value="刪除">
                    </td>
                </tr>
            `
        }

        onclick(".editbutton",function(element,event){
            ajax("GET",ajaxurl+"/getfood/"+element.dataset.id,function(event,data){
                let row=data["data"]

                lightbox(null,"lightbox",function(){
                    return `
                        <div class="textcenter adminfoodlightbox">
                            <div class="adminfoodtitle">編輯餐點</div>
                            <div class="flex">
                                <div class="textleft inputdiv" id="namediv">
                                    <div class="textlabel light">餐點名稱:</div>
                                    <div class="input light">
                                        <input type="text" id="name" value="${row["name"]}">
                                    </div>
                                    <div class="text large bold error errordiv" id="nameerror"></div>
                                </div>
                                <div class="textleft inputdiv" id="pricediv">
                                    <div class="textlabel light">價格:</div>
                                    <div class="input light">
                                        <input type="number" id="price" value="${row["price"]}">
                                    </div>
                                    <div class="text large bold error errordiv" id="usernameerror"></div>
                                </div>
                            </div>
                            <input type="button" class="button light" id="close" value="取消">
                            <input type="button" class="button light" id="submit" value="完成">
                        </div>
                    `
                },"close",true,"none")

                onclick("#submit",function(element2,event2){
                    ajax("PUT",ajaxurl+"/editfood/"+element.dataset.id,function(event,data){
                        if(data["success"]){
                            alert("修改成功")
                            location.reload()
                        }else{
                            docgetid(data["errorkey"]+"error").innerHTML=data["data"]
                            docgetid(data["errorkey"]).parentNode.classList.add("inputerror")
                        }
                    },JSON.stringify({
                        "name": docgetid("name").value,
                        "price": docgetid("price").value
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

        onclick(".deletebutton",function(element,event){
            if(confirm("確定要刪除嗎?")){
                ajax("DELETE",ajaxurl+"/deletefood/"+element.dataset.id,function(event,data){
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

onclick("#newfood",function(element,event){
    lightbox(null,"lightbox",function(){
        return `
            <div class="textcenter adminfoodlightbox">
                <div class="adminfoodtitle">新增餐點</div>
                <div class="flex">
                    <div class="textleft inputdiv" id="namediv">
                        <div class="textlabel light">餐點名稱:</div>
                        <div class="input light">
                            <input type="text" id="name">
                        </div>
                        <div class="text large bold error errordiv" id="nameerror"></div>
                    </div>
                    <div class="textleft inputdiv" id="pricediv">
                        <div class="textlabel light">價格:</div>
                        <div class="input light">
                            <input type="number" id="price">
                        </div>
                        <div class="text large bold error errordiv" id="usernameerror"></div>
                    </div>
                </div>
                <input type="button" class="button light" id="close" value="取消">
                <input type="button" class="button light" id="submit" value="完成">
            </div>
        `
    },"close",true,"none")

    onclick("#submit",function(){
        ajax("POST",ajaxurl+"/newfood",function(event,data){
            if(data["success"]){
                alert("新增成功")
                location.reload()
            }else{
                docgetid(data["errorkey"]+"error").innerHTML=data["data"]
                docgetid(data["errorkey"]+"div").classList.add("inputerror")
            }
        },JSON.stringify({
            "name": docgetid("name").value,
            "price": docgetid("price").value
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
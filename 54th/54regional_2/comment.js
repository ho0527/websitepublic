ajax("GET",ajaxurl+"/getcommentlist",function(event,data){
    if(data["success"]){
        let row=data["data"]
        for(let i=0;i<row.length;i=i+1){
            let commentcontent=row[i]["content"]
            let commentinnerhtml=``

            if(!row[i]["delete"]){
                commentinnerhtml=`
                    <div class="commentimagediv"><img src="${row[i]["image"]}" class="commentimage"></div>
                    <div class="commentemailphone macossectiondivy">email: ${row[i]["email"]}，電話: ${row[i]["phone"]}</div>
                    <div class="commentfunction macossectiondivy">
                        <div class="commentfunctioncenter">
                            <div class="textleft inputdiv" id="code_${row[i]["id"]}_div">
                                <div class="textlabel">留言序號:</div>
                                <div class="input">
                                    <input type="text" class="codeinput" id="code_${row[i]["id"]}" data-id="${row[i]["id"]}">
                                </div>
                                <div class="text large bold error errordiv" id="codeerror_${row[i]["id"]}"></div>
                            </div>
                            <div class="commentbuttondiv">
                                <input type="button" class="button outline editcomment" data-id="${row[i]["id"]}" data-code="${row[i]["code"]}" value="編輯">
                                <input type="button" class="button warn deletecomment" data-id="${row[i]["id"]}" data-code="${row[i]["code"]}" value="刪除">
                            </div>
                        </div>
                    </div>
                    <div class="commentadminresponse">管理員回應: ${row[i]["adminresponse"]}</div>
                `
            }else{
                commentcontent=``
                commentinnerhtml=`
                    <div class="commentimagediv"></div>
                    <div class="commentfunctiondelete"><div class="commentfunctioncenter commentfunctiondeleted">已刪除</div></div>
                `
            }

            if(row[i]["pin"]=="true"){
                docgetid("pindiv").innerHTML=`
                    ${docgetid("pindiv").innerHTML}
                    <img src="/website/material/icon/dark/pin01-solid.png" class="pinicon" title="已訂選">
                    <div class="commentdiv grid" id="${row[i]["id"]}">
                        <div class="commentusername macossectiondivx">${row[i]["username"]}</div>
                        <div class="commentcontent macossectiondivy">${commentcontent}</div>
                        <div class="commenttimedata">${row[i]["timedata"]}</div>
                        ${commentinnerhtml}
                    </div>
                `
                docgetid("pindiv").style.height="30vh"
            }else{
                docgetid("main").innerHTML=`
                    ${docgetid("main").innerHTML}
                    <div class="commentdiv grid" id="${row[i]["id"]}">
                        <div class="commentusername macossectiondivx">${row[i]["username"]}</div>
                        <div class="commentcontent macossectiondivy">${commentcontent}</div>
                        <div class="commenttimedata">${row[i]["timedata"]}</div>
                        ${commentinnerhtml}
                    </div>
                `
            }
        }

        onkeydown(".codeinput",function(element,event){
            if(event.key=="Enter"){
                docgetall(".editcomment").forEach(function(event2){
                    if(event2.dataset.id==element.dataset.id){
                        event2.click()
                    }
                })
            }
        })

        onclick(".editcomment",function(element,event){
            if(docgetid("code_"+element.dataset.id).value==element.dataset.code){
                ajax("GET",ajaxurl+"/getcomment/"+element.dataset.id,function(event,data){
                    let row=data["data"]

                    lightbox(null,"lightbox",function(){
                        return `
                            <div class="textcenter commentlightbox">
                                <div class="commenttitle">編輯留言</div>
                                <div class="textleft inputdiv">
                                    <div class="textlabel light">姓名:</div>
                                    <div class="input light">
                                        <input type="text" id="username" value="${row["username"]}">
                                    </div>
                                    <div class="text large bold error errordiv" id="usernameerror"></div>
                                </div>
                                <div class="textleft inputdiv">
                                    <div class="textlabel light">E-mail:</div>
                                    <div class="flex">
                                        <div class="input light endlabel">
                                            <input type="text" id="email" value="${row["email"]}">
                                        </div>
                                        <label class="checkbox" style="min-width: 100px;">
                                            <input type="checkbox" id="emailshow" ${row["emailshow"]}>顯示
                                        </label>
                                    </div>
                                    <div class="text large bold error errordiv" id="emailerror"></div>
                                </div>
                                <div class="textleft inputdiv">
                                    <div class="textlabel light">電話:</div>
                                    <div class="flex">
                                        <div class="input light">
                                            <input type="text" id="phone" value="${row["phone"]}">
                                        </div>
                                        <label class="checkbox" style="min-width: 100px;">
                                            <input type="checkbox" id="phoneshow" ${row["phoneshow"]}>顯示
                                        </label>
                                    </div>
                                    <div class="text large bold error errordiv" id="phoneerror"></div>
                                </div>
                                <div class="input light inputmargin inputdiv">
                                    <textarea class="commenttextarea" id="content" placeholder="內容">${row["content"]}</textarea>
                                </div>
                                <input type="button" class="button light" onclick="docgetid('file').click()" value="上傳圖片">
                                <input type="button" class="button light" id="close" value="取消">
                                <input type="button" class="button light" id="submit" value="完成">
                                <input type="file" class="displaynone" id="file" accept="image/*">
                            </div>
                        `
                    },"close",true,"none")

                    weblsset("54regionalcommentimage",row["image"])

                    onchange("#file",function(element,event){
                        let file=event.target.files[0]
                        let reader=new FileReader()
                        reader.onload=function(){
                            weblsset("54regionalcommentimage",reader.result)
                        }
                        reader.readAsDataURL(file)
                    })

                    onclick("#submit",function(element2,event2){
                        ajax("PUT",ajaxurl+"/editcomment/"+element.dataset.id,function(event,data){
                            if(data["success"]){
                                alert("修改成功")
                                location.reload()
                            }else{
                                docgetid(data["errorkey"]+"error").innerHTML=data["data"]
                                docgetid(data["errorkey"]).parentNode.classList.add("inputerror")
                            }
                        },JSON.stringify({
                            "image": weblsget("54regionalcommentimage"),
                            "username": docgetid("username").value,
                            "email": docgetid("email").value,
                            "emailshow": docgetid("emailshow").checked,
                            "phone": docgetid("phone").value,
                            "phoneshow": docgetid("phoneshow").checked,
                            "content": docgetid("content").value
                        }))
                    })

                    onkeydown("input[type='text']",function(element2,event2){
                        if(event2.key=="Enter"){
                            docgetid("submit").click()
                        }
                    })
                })
            }else{
                docgetid("code_"+element.dataset.id+"_div").classList.add("inputerror")
                docgetid("codeerror_"+element.dataset.id).innerHTML=`留言序號錯誤`
            }
        })

        onclick(".deletecomment",function(element,event){
            if(docgetid("code_"+element.dataset.id).value==element.dataset.code){
                if(confirm("確定要刪除嗎?")){
                    ajax("DELETE",ajaxurl+"/deletecomment/"+element.dataset.id,function(event,data){
                        if(data["success"]){
                            alert("刪除成功")
                            location.reload()
                        }else{
                            alert(data["data"])
                        }
                    })
                }
            }else{
                docgetid("code_"+element.dataset.id+"_div").classList.add("inputerror")
                docgetid("codeerror_"+element.dataset.id).innerHTML=`留言序號錯誤`
            }
        })

        weblsset("54regionalcommentimage",null)
    }else{
        alert(data["data"])
    }
})

innerhtml(domgetid("titletext"),`訪客留言`,false)

onclick("#newcomment",function(element,event){
    weblsset("54regionalcommentimage","")

    lightbox(null,"lightbox",function(){
        return `
            <div class="textcenter commentlightbox">
                <div class="commenttitle">新增留言</div>
                <div class="textleft inputdiv">
                    <div class="textlabel light">訪客姓名:</div>
                    <div class="input light">
                        <input type="text" id="username">
                    </div>
                    <div class="text large bold error errordiv" id="usernameerror"></div>
                </div>
                <div class="textleft inputdiv">
                    <div class="textlabel light">Email:</div>
                    <div class="input light">
                        <input type="text" id="email">
                    </div>
                    <div class="text large bold error errordiv" id="emailerror"></div>
                </div>
                <div class="textleft inputdiv">
                    <div class="textlabel light">聯絡電話:</div>
                    <div class="input light">
                        <input type="text" id="phone">
                    </div>
                    <div class="text large bold error errordiv" id="phoneerror"></div>
                </div>
                <div class="input light inputmargin inputdiv">
                    <textarea class="commenttextarea" id="content" placeholder="留言內容"></textarea>
                </div>
                <div class="textleft inputdiv">
                    <div class="textlabel light">留言序號:</div>
                    <div class="input light">
                        <input type="text" id="code">
                    </div>
                    <div class="text large bold error errordiv" id="codeerror"></div>
                </div>
                <input type="button" class="button light" onclick="docgetid('file').click()" value="上傳圖片">
                <input type="button" class="button light" id="close" value="取消">
                <input type="button" class="button light" id="submit" value="完成">
                <input type="file" class="displaynone" id="file" accept="image/*">
            </div>
        `
    },"close",true,"none")

    onchange("#file",function(element,event){
        let file=event.target.files[0]
        let reader=new FileReader()
        reader.onload=function(){
            weblsset("54regionalcommentimage",reader.result)
            alert("圖片上傳成功")
        }
        reader.readAsDataURL(file)
    })

    onclick("#submit",function(element,event){
        docgetall(".inputdiv").forEach(function(event){
            event.classList.remove("inputerror")
        })

        docgetall(".error").forEach(function(event){
            event.innerHTML=``
        })

        ajax("POST",ajaxurl+"/newcomment",function(event,data){
            if(data["success"]){
                alert("新增成功")
                location.reload()
            }else{
                docgetid(data["errorkey"]+"error").innerHTML=data["data"]
                docgetid(data["errorkey"]).parentNode.classList.add("inputerror")
            }
        },JSON.stringify({
            "image": weblsget("54regionalcommentimage")??"",
            "username": docgetid("username").value,
            "email": docgetid("email").value,
            "phone": docgetid("phone").value,
            "content": docgetid("content").value,
            "code": docgetid("code").value
        }))

        weblsset("54regionalcommentimage",null)
    })

    onkeydown("input[type='text']",function(element2,event2){
        if(event2.key=="Enter"){
            docgetid("submit").click()
        }
    })
})
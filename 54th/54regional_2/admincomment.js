ajax("GET",ajaxurl+"/getcommentlist",function(event,data){
    if(data["success"]){
        let row=data["data"]
        for(let i=0;i<row.length;i=i+1){
            let commentcontent=row[i]["content"]
            let pinvalue="訂選"

            if(row[i]["pin"]=="true"){
                pinvalue="取消訂選"
            }

            docgetid("main").innerHTML=`
                ${docgetid("main").innerHTML}
                <div class="commentdiv grid" id="${row[i]["id"]}">
                    <div class="commentusername macossectiondivx">${row[i]["username"]}</div>
                    <div class="commentcontent macossectiondivy">${commentcontent}</div>
                    <div class="commenttimedata">${row[i]["timedata"]}</div>
                    <div class="commentimagediv"><img src="${row[i]["image"]}" class="commentimage"></div>
                    <div class="commentemailphone">email: ${row[i]["email"]}，電話: ${row[i]["phone"]}</div>
                    <div class="commentfunction">
                        <input type="button" class="button outline pincomment fill" data-id="${row[i]["id"]}" value="${pinvalue}">
                        <input type="button" class="button outline adminresponse fill" data-id="${row[i]["id"]}" value="管理員回應">
                        <input type="button" class="button light editcomment fill" data-id="${row[i]["id"]}" value="編輯">
                        <input type="button" class="button warn deletecomment fill" data-id="${row[i]["id"]}" value="刪除">
                    </div>
                    <div class="commentadminresponse">管理員回應: ${row[i]["adminresponse"]}</div>
                </div>
            `
        }

        onclick(".pincomment",function(element,event){
            ajax("POST",ajaxurl+"/adminpincomment/"+element.dataset.id,function(event,data){
                if(data["success"]){
                    location.reload()
                }
            })
        })

        onclick(".adminresponse",function(element,event){
            lightbox(null,"lightbox",function(){
                return `
                    <div class="textcenter commentlightbox">
                        <div class="input light inputmargin inputdiv">
                            <textarea class="commenttextarea" id="content" placeholder="內容"></textarea>
                        </div>
                        <input type="button" class="button light" id="close" value="取消">
                        <input type="button" class="button light" id="submit" value="完成">
                    </div>
                `
            },"close",true,"none")

            onclick("#submit",function(element2,event2){
                ajax("POST",ajaxurl+"/adminresponsecomment/"+element.dataset.id,function(event,data){
                    if(data["success"]){
                        alert("上傳成功")
                        location.reload()
                    }
                },JSON.stringify({
                    "response": docgetid("content").value
                }))
            })

            onkeydown("input[type='text']",function(element2,event2){
                if(event2.key=="Enter"){
                    docgetid("submit").click()
                }
            })
        })

        onclick(".editcomment",function(element,event){
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
        })

        onclick(".deletecomment",function(element,event){
            if(confirm("確定要刪除嗎?")){
                ajax("DELETE",ajaxurl+"/admindeletecomment/"+element.dataset.id,function(event,data){
                    if(data["success"]){
                        alert("刪除成功")
                        location.reload()
                    }else{
                        alert(data["data"])
                    }
                })
            }
        })

        weblsset("54regionalcommentimage",null)
    }else{
        alert(data["data"])
    }
})
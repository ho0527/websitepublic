// userlist START
let orderby="number"
let ordertype="ASC"
let keyword=""

function userlistmain(){
    let sortnumbervalue="升冪"
    let sortusernamevalue="升冪"
    let sortnamevalue="升冪"

    if(isset(weblsget("45regionalsortnumber"))){
        orderby="number"
        ordertype=weblsget("45regionalsortnumber")
        if(weblsget("45regionalsortnumber")=="DESC"){
            sortnumbervalue="降冪"
        }
    }else if(isset(weblsget("45regionalsortusername"))){
        orderby="username"
        ordertype=weblsget("45regionalsortusername")
        if(weblsget("45regionalsortusername")=="DESC"){
            sortusernamevalue="降冪"
        }
    }else if(isset(weblsget("45regionalsortname"))){
        orderby="name"
        ordertype=weblsget("45regionalsortname")
        if(weblsget("45regionalsortname")=="DESC"){
            sortnamevalue="降冪"
        }
    }else{
        weblsset("45regionalsortnumber","ASC")
    }

    if(isset(weblsget("45regionalkeyword"))){
        keyword=weblsget("45regionalkeyword")
    }

    docgetid("main").innerHTML=`
        <tr>
            <td class="admintd">使用者id</td>
            <td class="admintd">編號<input type="button" id="sortnumber" value="${sortnumbervalue}"></td>
            <td class="admintd">帳號<input type="button" id="sortusername" value="${sortusernamevalue}"></td>
            <td class="admintd">密碼</td>
            <td class="admintd">姓名<input type="button" id="sortname" value="${sortnamevalue}"></td>
            <td class="admintd">權限</td>
            <td class="admintd">功能</td>
        </tr>
    `

    ajax("GET","/backend/45regional/getuserlist?keyword="+keyword+"&orderby="+orderby+"&ordertype="+ordertype,function(event){
        let data=JSON.parse(event.responseText)
        if(data["success"]){
            let row=data["data"]
            for(let i=0;i<row.length;i=i+1){
                let def=`
                    <input type="button" class="stbutton light edituser" data-id="${row[i][0]}" value="修改">
                    <input type="button" class="stbutton error deluser" data-id="${row[i][0]}" value="刪除">
                `

                if(row[i][0]==1){
                    def=``
                }

                docgetid("main").innerHTML=`
                    ${docgetid("main").innerHTML}
                    <tr>
                        <td class="admintd">${row[i][0]}</td>
                        <td class="admintd">${row[i][4]}</td>
                        <td class="admintd">${row[i][1]}</td>
                        <td class="admintd">${row[i][2]}</td>
                        <td class="admintd">${row[i][3]}</td>
                        <td class="admintd">${row[i][5]}</td>
                        <td class="admintd">${def}</td>
                    </tr>
                `
            }

            docgetid("sortnumber").onclick=function(){
                weblsset("45regionalsortusername",null)
                weblsset("45regionalsortname",null)

                if(sortnumbervalue=="升冪"){
                    weblsset("45regionalsortnumber","DESC")
                }else{
                    weblsset("45regionalsortnumber","ASC")
                }

                userlistmain()
            }

            docgetid("sortusername").onclick=function(){
                weblsset("45regionalsortnumber",null)
                weblsset("45regionalsortname",null)

                if(sortusernamevalue=="升冪"){
                    weblsset("45regionalsortusername","DESC")
                }else{
                    weblsset("45regionalsortusername","ASC")
                }

                userlistmain()
            }

            docgetid("sortname").onclick=function(){
                weblsset("45regionalsortnumber",null)
                weblsset("45regionalsortusername",null)

                if(sortnamevalue=="升冪"){
                    weblsset("45regionalsortname","DESC")
                }else{
                    weblsset("45regionalsortname","ASC")
                }

                userlistmain()
            }
        }else{
            alert(data["data"])
        }

        docgetall(".edituser").forEach(function(event){
            event.onclick=function(){
                let userid=event.dataset.id

                ajax("GET","/backend/45regional/getuser/"+userid,function(event){
                    let data=JSON.parse(event.responseText)

                    if(data["success"]){
                        let permissioninnerhtml=`
                            <input type="checkbox" class="top3px" id="permission">
                        `
                        if(data["data"][5]=="管理者"){
                            permissioninnerhtml=`
                                <input type="checkbox" class="top3px" id="permission" checked>
                            `
                        }
                        lightbox(null,"lightbox",function(){
                            return `
                                <div class="inputmargin">
                                    <div class="sttext">帳號</div>
                                    <div class="stinput underline light endicon">
                                        <input type="text" id="username" value="${data["data"][1]}">
                                        <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                                    </div>
                                </div>
                                <div class="inputmargin">
                                    <div class="sttext">密碼</div>
                                    <div class="stinput underline light endicon">
                                        <input type="text" id="password" value="${data["data"][2]}">
                                        <div class="icon"><img src="/website/material/icon/lock.svg" class="iconinputicon cursor_pointer" id="passwordicon" draggable="false"></div>
                                    </div>
                                </div>
                                <div class="inputmargin">
                                    <div class="sttext">姓名</div>
                                    <div class="stinput underline light endicon">
                                        <input type="text" id="name" value="${data["data"][3]}">
                                        <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                                    </div>
                                </div>
                                <label class="stcheckbox light">
                                    管理員權限
                                    ${permissioninnerhtml}
                                </label>
                                <input type="button" class="stbutton outline light" id="close" value="返回">
                                <input type="button" class="stbutton outline light" id="submit" value="送出">
                            `
                        },"close",true,"mask")
                        setTimeout(function(){
                            docgetid("submit").onclick=function(){
                                ajax("PUT","/backend/45regional/editdeluser/"+userid,function(event){
                                    let data=JSON.parse(event.responseText)
                                    if(data["success"]){
                                        alert("修改成功")
                                        location.reload()
                                    }else{
                                        alert(data["data"])
                                    }
                                },JSON.stringify({
                                    "username": docgetid("username").value,
                                    "password": docgetid("password").value,
                                    "name": docgetid("name").value,
                                    "permission": docgetid("permission").checked
                                }))
                            }
                        },100)
                    }else{
                        alert(data["data"])
                    }
                })
            }
        })

        docgetall(".deluser").forEach(function(event){
            event.onclick=function(){
                if(event.dataset.id!="1"){
                    if(confirm("是否確定刪除?")){
                        ajax("DELETE","/backend/45regional/editdeluser/"+event.dataset.id,function(event){
                            let data=JSON.parse(event.responseText)
                            if(data["success"]){
                                alert("刪除成功")
                                location.reload()
                            }else{
                                alert(data["data"])
                            }
                        })
                    }
                }
            }
        })
    })
}

userlistmain()

docgetid("search").oninput=function(){
    weblsset("45regionalkeyword",this.value)
    userlistmain()
}
// userlist END

docgetid("newuser").onclick=function(){
    lightbox(null,"lightbox",function(){
        return `
            <div class="inputmargin">
                <div class="sttext">帳號</div>
                <div class="stinput underline light endicon">
                    <input type="text" id="username">
                    <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                </div>
            </div>
            <div class="inputmargin">
                <div class="sttext">密碼</div>
                <div class="stinput underline light endicon">
                    <input type="text" id="password">
                    <div class="icon"><img src="/website/material/icon/lock.svg" class="iconinputicon cursor_pointer" id="passwordicon" draggable="false"></div>
                </div>
            </div>
            <div class="inputmargin">
                <div class="sttext">姓名</div>
                <div class="stinput underline light endicon">
                    <input type="text" id="name">
                    <div class="icon"><img src="/website/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                </div>
            </div>
            <label class="stcheckbox light">
                管理員權限
                <input type="checkbox" class="top3px" id="permission">
            </label>
            <input type="button" class="stbutton outline light" id="close" value="返回">
            <input type="button" class="stbutton outline light" id="submit" value="送出">
        `
    },"close",true,"mask")
    setTimeout(function(){
        docgetid("submit").onclick=function(){
            oldajax("POST","/backend/45regional/signup",JSON.stringify({
                "username": docgetid("username").value,
                "password": docgetid("password").value,
                "name": docgetid("name").value,
                "permission": docgetid("permission").checked
            })).onload=function(){
                let data=JSON.parse(this.responseText)
                if(data["success"]){
                    alert("新增成功")
                    location.reload()
                }else{
                    alert(data["data"])
                }
            }
        }
    },100)
}

docgetid("user").onclick=function(){
    docgetid("log").classList.remove("navigationbarselect")
    docgetid("user").classList.add("navigationbarselect")

    userlistmain()
}

docgetid("log").onclick=function(){
    docgetid("log").classList.add("navigationbarselect")
    docgetid("user").classList.remove("navigationbarselect")

    ajax("GET","/backend/45regional/getlog",function(event){
        let data=JSON.parse(event.responseText)

        if(data["success"]){
            let row=data["data"]

            docgetid("main").innerHTML=`
                <tr>
                    <td class="admintd">使用者id</td>
                    <td class="admintd">動作</td>
                    <td class="admintd">動作時間</td>
                </tr>
            `

            for(let i=0;i<row.length;i=i+1){
                docgetid("main").innerHTML=`
                    ${docgetid("main").innerHTML}
                    <tr>
                        <td class="admintd">${row[i][1]}</td>
                        <td class="admintd">${row[i][2]}</td>
                        <td class="admintd">${row[i][3]}</td>
                    </tr>
                `
            }
        }
    })
}

onclick("#logout",function(element,event){
    ajax("POST","/backend/45regional/logout/"+weblsget("45regionaluserid"),function(event,data){
        if(data["success"]){
            alert("登出成功")
            weblsset("45regionaluserid",null)
            weblsset("45regionalpermission",null)
            weblsset("45regionalsortnumber",null)
            weblsset("45regionalsortusername",null)
            weblsset("45regionalsortname",null)
            location.href="index.html"
        }
    })
})

startmacossection()
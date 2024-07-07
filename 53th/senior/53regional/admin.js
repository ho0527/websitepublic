// userlist START
let orderby="number"
let ordertype="ASC"
let keyword=""

function userlistmain(){
    let sortnumbervalue="升冪"
    let sortusernamevalue="升冪"
    let sortnamevalue="升冪"

    if(isset(weblsget("53regionalsortnumber"))){
        orderby="number"
        ordertype=weblsget("53regionalsortnumber")
        if(weblsget("53regionalsortnumber")=="DESC"){
            sortnumbervalue="降冪"
        }
    }else if(isset(weblsget("53regionalsortusername"))){
        orderby="username"
        ordertype=weblsget("53regionalsortusername")
        if(weblsget("53regionalsortusername")=="DESC"){
            sortusernamevalue="降冪"
        }
    }else if(isset(weblsget("53regionalsortname"))){
        orderby="name"
        ordertype=weblsget("53regionalsortname")
        if(weblsget("53regionalsortname")=="DESC"){
            sortnamevalue="降冪"
        }
    }else{
        weblsset("53regionalsortnumber","ASC")
    }

    if(weblsget("53regionalkeyword")){
        keyword=weblsget("53regionalkeyword")
    }

    docgetid("usertable").innerHTML=`
        <tr>
            <td class="admintd">編號 <input type="button" id="sortnumber" value="${sortnumbervalue}"></td>
            <td class="admintd">帳號 <input type="button" id="sortusername" value="${sortusernamevalue}"></td>
            <td class="admintd">密碼</td>
            <td class="admintd">姓名 <input type="button" id="sortname" value="${sortnamevalue}"></td>
            <td class="admintd">權限</td>
            <td class="admintd">功能</td>
        </tr>
    `

    oldajax("GET","/backend/53regional/getuserlist?keyword="+keyword+"&orderby="+orderby+"&ordertype="+ordertype).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            let row=data["data"]
            for(let i=0;i<row.length;i=i+1){
                docgetid("usertable").innerHTML=`
                    ${docgetid("usertable").innerHTML}
                    <tr>
                        <td class="admintd">${row[i][4]}</td>
                        <td class="admintd">${row[i][1]}</td>
                        <td class="admintd">${row[i][2]}</td>
                        <td class="admintd">${row[i][3]}</td>
                        <td class="admintd">${row[i][5]}</td>
                        <td class="admintd">
                            <input type="button" class="bluebutton edituser" data-id="${row[i][0]}" value="修改">
                            <input type="button" class="bluebutton deluser" data-id="${row[i][0]}" value="刪除">
                        </td>
                    </tr>
                `
            }

            docgetid("sortnumber").onclick=function(){
                weblsset("53regionalsortusername",null)
                weblsset("53regionalsortname",null)

                if(sortnumbervalue=="升冪"){
                    weblsset("53regionalsortnumber","DESC")
                }else{
                    weblsset("53regionalsortnumber","ASC")
                }

                userlistmain()
            }

            docgetid("sortusername").onclick=function(){
                weblsset("53regionalsortnumber",null)
                weblsset("53regionalsortname",null)

                if(sortusernamevalue=="升冪"){
                    weblsset("53regionalsortusername","DESC")
                }else{
                    weblsset("53regionalsortusername","ASC")
                }

                userlistmain()
            }

            docgetid("sortname").onclick=function(){
                weblsset("53regionalsortnumber",null)
                weblsset("53regionalsortusername",null)

                if(sortnamevalue=="升冪"){
                    weblsset("53regionalsortname","DESC")
                }else{
                    weblsset("53regionalsortname","ASC")
                }

                userlistmain()
            }
        }else{
            alert(data["data"])
        }
    }
}

userlistmain()

docgetid("searchsubmit").onclick=function(){
    weblsset("53regionalkeyword",docgetid("search").value)
    userlistmain()
}

docgetid("search").onkeydown=function(event){
    if(event.key=="Enter"){
        docgetid("searchsubmit").click()
    }
}
// userlist END

// log START
oldajax("GET","/backend/53regional/getlog").onload=function(){
    let data=JSON.parse(this.responseText)
    let row=data["data"]
    for(let i=0;i<row.length;i=i+1){
        docgetid("logtable").innerHTML=`
            ${docgetid("logtable").innerHTML}
            <tr>
                <td class="admintd">${row[i][1]}</td>
                <td class="admintd">${row[i][2]}</td>
                <td class="admintd">${row[i][4]}</td>
                <td class="admintd">${row[i][6]}</td>
                <td class="admintd">${row[i][7]}</td>
                <td class="admintd">${row[i][8]}</td>
            </tr>
        `
    }
}
// log END


// 計時器 START
let seconds
let asktimer=5

docgetid("changetimer").value=weblsget("53regionaltime")
seconds=docgetid("changetimer").value
docgetid("timer").value=seconds
let timer=setInterval(function(){
    seconds=seconds-1
    if(seconds==0){
        clearInterval(timer)
        setTimeout(function(){
            docgetid("timer").value=0
            lightbox(null,"lightbox",function(){
                return `
                    是否繼續操作?<br>
                    <input type="button" class="button" onclick="location.reload()" value="Yes">
                    <input type="button" class="button" onclick="logout()" value="否">
                `
            })
            setTimeout(function(){
                logout()
            },5000)
        },100)
    }
    docgetid("timer").value=seconds
},1000)

docgetid("resetbutton").onclick=function(){
    location.reload()
}

docgetid("changetimersubmit").onclick=function(){
    weblsset("53regionaltime",docgetid("changetimer").value)
    location.reload()
}
// 計時器 END


// user相關 START
docgetid("newuser").onclick=function(){
    lightbox(null,"lightbox",function(){
        return `
            帳號: <input type="text" class="input" id="username"><br><br>
            密碼: <input type="password" class="input" id="password"><br><br>
            姓名: <input type="text" class="input" id="name"><br><br>
            管理員權限: <input type="checkbox" class="checkbox" id="permission">
            <input type="button" class="button" id="close" value="返回">
            <input type="button" class="button" id="submit" value="送出">
        `
    },"close",true,"none")
    setTimeout(function(){
        docgetid("submit").onclick=function(){
            oldajax("POST","/backend/53regional/newuser",JSON.stringify({
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

docgetall(".edituser").forEach(function(event){
    event.onclick=function(){
        if(event.dataset.id!="1"){
            oldajax("GET","/backend/53regional/getuser/"+event.dataset.id).onload=function(){
                let data=JSON.parse(this.responseText)

                let permissioninnerhtml=`
                    <input type="checkbox" class="checkbox" id="permission">
                `
                if(data["data"][5]=="管理者"){
                    permissioninnerhtml=`
                        <input type="checkbox" class="checkbox" id="permission" checked>
                    `
                }
                lightbox(null,"lightbox",function(){
                    return `
                        帳號: <input type="text" class="input" id="username" value="${data["data"][1]}" disabled><br><br>
                        密碼: <input type="password" class="input" id="password" value="${data["data"][2]}"><br><br>
                        姓名: <input type="text" class="input" id="name" value="${data["data"][3]}"><br><br>
                        管理員權限: ${permissioninnerhtml}
                        <input type="button" class="button" id="close" value="返回">
                        <input type="button" class="button" id="submit" value="送出">
                    `
                },"close",true,"none")
                setTimeout(function(){
                    docgetid("submit").onclick=function(){
                        oldajax("PUT","/backend/53regional/editdeluser/"+event.dataset.id,JSON.stringify({
                            "password": docgetid("password").value,
                            "name": docgetid("name").value,
                            "permission": docgetid("permission").value
                        })).onload=function(){
                            let data=JSON.parse(this.responseText)
                            if(data["success"]){
                                alert("修改成功")
                                location.reload()
                            }else{
                                alert(data["data"])
                            }
                        }
                    }
                },100)
            }
        }
    }
})

docgetall(".deluser").forEach(function(event){
    event.onclick=function(){
        if(event.dataset.id!="1"){
            if(confirm("是否確定刪除?")){
                oldajax("DELETE","/backend/53regional/editdeluser/"+event.dataset.id).onload=function(){
                    let data=JSON.parse(this.responseText)
                    if(data["success"]){
                        alert("刪除成功")
                        location.reload()
                    }else{
                        alert(data["data"])
                    }
                }
            }
        }
    }
})
// user相關 END
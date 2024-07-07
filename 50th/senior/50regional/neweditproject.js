let facingcount=1

if(key=="edit"){
    facingcount=facingrow.length

    docgetid("name").value=row[1]
    docgetid("desciption").value=row[2]
    let leader=row[3]
    let member=row[4].split("|&|")

    docgetall(".userdiv>.user").forEach(function(event){
        if(event.dataset.id==leader){
            let div=doccreate("div")
            div.classList.add("user")
            div.dataset.id=event.dataset.id
            div.innerHTML=`${event.innerHTML}`
            docgetid("leader").appendChild(div)
            event.remove()
        }
    })

    for(let i=0;i<member.length;i=i+1){
        docgetall(".userdiv>.user").forEach(function(event){
            if(event.dataset.id==member[i]){
                let div=doccreate("div")
                div.classList.add("user")
                div.dataset.id=event.dataset.id
                div.innerHTML=`${event.innerHTML}`
                docgetid("member").appendChild(div)
                event.remove()
            }
        })
    }

    for(let i=0;i<facingcount;i=i+1){
        let div=doccreate("div")
        div.classList.add("facingdiv")
        div.classList.add("macossectiondivy")
        div.innerHTML=`
            <div class="facing grid">
                <input type="text" class="input2 facingname" value="${facingrow[i][2]}" placeholder="面向名稱">
                <input type="text" class="input2 facingdesciption" value="${facingrow[i][3]}" placeholder="面向說明">
                <input type="button" class="noborderbutton facingdelect" value="X">
            </div>
        `
        docgetid("projectfacing").appendChild(div)
    }
}

docgetid("newfacing").onclick=function(){
    if(facingcount<10){
        docgetid("projectfacing").innerHTML=`
            ${docgetid("projectfacing").innerHTML}
            <div class="facingdiv">
                <div class="facing grid">
                    <input type="text" class="input2 facingname" placeholder="面向名稱">
                    <input type="text" class="input2 facingdesciption" placeholder="面向說明">
                    <input type="button" class="noborderbutton facingdelect" value="X">
                </div>
            </div>
        `
        facingcount=facingcount+1
    }else{ alert("以到面向上限") }

    docgetall(".facingdelect").forEach(function(event){
        event.onclick=function(){
            event.parentElement.parentElement.remove() // 刪除facingdiv
            facingcount=facingcount-1
        }
    })
}

docgetid("submit").onclick=function(){
    let data=[]
    let projectname=docgetid("name").value
    let projectdesciption=docgetid("desciption").value
    let leader=""
    let member=[]
    let facingname=[]
    let facingdesciption=[]
    for(let i=0;i<docgetall(".facing").length;i=i+1){
        if(docgetall(".facingname")[i].value!=""&&docgetall(".facingdesciption")[i].value!=""){
            facingname.push(docgetall(".facingname")[i].value)
            facingdesciption.push(docgetall(".facingdesciption")[i].value)
        }else{ conlog("pass") }
    }
    if(docgetall(".leader>.user").length==1){
        leader=docgetall(".leader>.user")[0].dataset.id
        docgetall(".member>.user").forEach(function(event){
            member.push(event.dataset.id)
        })
        data.push(projectname,projectdesciption,leader,member,facingname,facingdesciption)
        let url="api/newproject.php"
        if(key=="edit"){ url="api/editproject.php" }
        fetch(url,{
            method:"POST",
            body:JSON.stringify(data),
            headers:{ "Content-Type":"application/json" },
        }).then(function(response){ conlog(response.text()) })
        .catch(function(event){ console.error("[ERROR]",event) })
        .then(function(){ alert("儲存成功");location.href="project.php" })
    }else{ alert("[WARNING]組長只能一個") }
    conlog(data)
}

divsort("user",".sort")
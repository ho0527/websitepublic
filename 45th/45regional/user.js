let positiontype="top"
let positiondeviation=40
let check=false
let dragid=null
let clickid=null
let maintableinnerhtml=`
    <tr>
        <td class="todotabletime">時間</td>
        <td>工作計畫</td>
    </tr>
`

// 讀取(初始化)各工作項目
function main(){
    ajax("GET","/backend/45regional/gettodolist/"+domgetid("dealselect").value+"/"+domgetid("priorityselect").value,function(event,data){
        if(data["success"]){
            let row=data["data"]

            domgetid("maintable").innerHTML=maintableinnerhtml

            for(let i=0;i<row.length;i=i+1){
                let starthr=parseInt(row[i][2])
                let endhr=parseInt(row[i][3])

                domgetid("maintable").innerHTML=`
                    ${domgetid("maintable").innerHTML}
                    <div class="todoblockdiv macossectiondivy" id="${row[i][0]}" data-starttime="${row[i][2]}" data-endtime="${row[i][3]}" style="position: absolute;${positiontype}: ${positiondeviation+30*(starthr)}px;left: 110px;height: ${30*(endhr-starthr)}px;">
                        <div class="todoblock" style="height: ${30*(endhr-starthr)}px;">
                            <div class="dragable" data-id="${row[i][0]}"></div>
                            <img src="icon/close.svg" class="tododeleteicon" data-id="${row[i][0]}">
                            <div class="todoblocktitle">工作名稱: ${row[i][1]}</div>
                            <div class="todoblocktime">執行時間: ${String(starthr).padStart(2,"0")}:00~${String(endhr).padStart(2,"0")}:00</div>
                            <div class="todoblocktime">處理情形: ${row[i][4]}</div>
                            <div class="todoblocktime">優先情形: ${row[i][5]}</div>
                        </div>
                    </div>
                `
            }

            // 各事件執行
            onmousemove(".dragable",function(element,event){
                dragid=element.dataset.id
            })

            onmouseout(".dragable",function(element,event){
                dragid=null
            })

            onmousedown(".todoblockdiv",function(element,event){
                if(dragid==null){
                    domgetall(".todoblockdiv").forEach(function(event2){
                        event2.style.backgroundColor="var(--stgray-300)"
                        event2.style.color="white"
                    })

                    if(clickid!=element.id){
                        clickid=element.id
                        element.style.backgroundColor="var(--stprimary-400)"
                        domgetid("edit").classList.remove("disabled")
                        domgetid("view").classList.remove("disabled")
                        domgetid("edit").disabled=false
                        domgetid("view").disabled=false
                    }else{
                        clickid=null
                        domgetid("edit").classList.add("disabled")
                        domgetid("view").classList.add("disabled")
                        domgetid("edit").disabled=true
                        domgetid("view").disabled=true
                    }
                }else{
                    check=true
                }
            })

            onmousemove(".todoblockdiv",function(element,event){
                if(check&&dragid==element.id){
                    let timecross=parseInt(element.dataset.endtime)-parseInt(element.dataset.starttime)
                    let y=event.pageY-45
                    let starttime
                    let endtime

                    if(y<65){
                        starttime="00"
                        endtime=String(timecross).padStart(2,"0")
                    }else if(24<(Math.round((y-55)/30)+timecross)){
                        starttime=String(24-timecross).padStart(2,"0")
                        endtime="24"
                    }else{
                        element.style.top=y-30+"px"
                        starttime=String(Math.round((y-65)/30)).padStart(2,"0")
                        endtime=String(Math.round((y-65)/30)+timecross).padStart(2,"0")
                    }

                    element.querySelectorAll(".todoblock>.todoblocktime")[0].innerHTML=`
                        執行時間: ${starttime}:00~${endtime}:00
                    `

                    element.style.zIndex="1000"
                    element.dataset.starttime=starttime
                    element.dataset.endtime=endtime
                }
            })

            onmouseup(".todoblockdiv",function(element,event){
                check=false
                dragid=null
                element.style.zIndex="1"
                ajax("PUT","/backend/45regional/edittodo/"+element.id,function(event,data){
                },JSON.stringify({
                    "starttime": element.dataset.starttime,
                    "endtime": element.dataset.endtime,
                }))
            })

            onclick(".tododeleteicon",function(element,event){
                if(confirm("確定刪除?")){
                    ajax("DELETE","/backend/45regional/deletetodo/"+element.dataset.id,function(event,data){
                        if(data["success"]){
                            alert("刪除成功")
                            location.reload()
                        }else{
                            alert("刪除失敗: "+data["data"])
                        }
                    })
                }
            })
        }else{
            alert(data["data"])
        }
    })

    // 創建新工作
    onclick("#newtodo",function(element,event){
        lightbox(null,"lightbox",function(){
            let timeoptionlist=``

            for(let i=0;i<=24;i=i+1){
                timeoptionlist=`
                    ${timeoptionlist}
                    <option value="${String(i).padStart(2,"0")}">${String(i).padStart(2,"0")}:00</option>
                `
            }

            return `
                <div class="textcenter userlightbox">
                    <div class="newtodotitle">新增工作</div>
                    <div class="inputmargin textleft">
                        <div class="sttextlabel light">工作標題:</div>
                        <div class="stinput underline">
                            <input type="text" id="title" value="work">
                        </div>
                    </div>
                    <div class="stselect fill colorwhite inputmargin">
                        <select class="textcenter" id="starttime">
                            <option value="na" disabled selected>開始時間</option>
                            ${timeoptionlist}
                        </select>
                    </div><br>
                    <div class="stselect fill colorwhite inputmargin">
                        <select class="textcenter" id="endtime">
                            <option value="na" disabled selected>結束時間</option>
                            ${timeoptionlist}
                        </select>
                    </div><br>
                    <div class="flex inputmargin">
                        <div class="box">
                            處理情形:
                            <div class="stselect solid">
                                <select class="textcenter" id="deal">
                                    <option value="未處理">未處理</option>
                                    <option value="處理中">處理中</option>
                                    <option value="已完成">已完成</option>
                                </select>
                            </div>
                        </div>
                        <div class="box">
                            類別:
                            <div class="stselect solid">
                                <select class="textcenter" id="priority">
                                    <option value="普通">普通</option>
                                    <option value="速件">速件</option>
                                    <option value="最速件">最速件</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="stinput inputmargin">
                        <textarea class="todotextarea" id="description" placeholder="詳細敘述工作內容"></textarea>
                    </div>
                    <input type="button" class="stbutton light" id="close" value="取消">
                    <input type="button" class="stbutton light" id="submit" value="完成">
                </div>
            `
        },"close",true,"none")

        onclick("#submit",function(){
            if(domgetid("starttime").value!="na"&&domgetid("endtime").value!="na"){
                if(parseInt(domgetid("starttime").value)<parseInt(domgetid("endtime").value)){
                    ajax("POST","/backend/45regional/newtodo",function(event){
                        let data=JSON.parse(event.responseText)
                        if(data["success"]){
                            alert("新增成功")
                            location.reload()
                        }else{
                            alert(data["data"])
                        }
                    },JSON.stringify({
                        "title": domgetid("title").value,
                        "starttime": domgetid("starttime").value,
                        "endtime": domgetid("endtime").value,
                        "deal": domgetid("deal").value,
                        "priority": domgetid("priority").value,
                        "description": domgetid("description").value
                    }))
                }else{
                    alert("結束時間不可大於開始時間!")
                }
            }else{
                alert("請選擇時間!")
            }
        })
    })
}

if(!weblsget("45regionaltodosorttype")){
    weblsset("45regionaltodosorttype","ASC")
}

if(weblsget("45regionaltodosorttype")=="ASC"){
    for(let i=0;i<=22;i=i+2){
        maintableinnerhtml=`
            ${maintableinnerhtml}
            <tr>
                <td class="todotabletime">${String(i).padStart(2,"0")}~${String(i+2).padStart(2,"0")}</td>
                <td class="todotablecontent"></td>
            </tr>
        `
    }
    domgetid("updownbutton").value="升冪"
}else{
    for(let i=22;i>=0;i=i-2){
        maintableinnerhtml=`
            ${maintableinnerhtml}
            <tr>
                <td class="todotabletime">${String(i+2).padStart(2,"0")}~${String(i).padStart(2,"0")}</td>
                <td class="todotablecontent"></td>
            </tr>
        `
    }
    domgetid("updownbutton").value="降冪"
    positiontype="bottom"
    positiondeviation=0
}

main()

onclick("#updownbutton",function(element,event){
    if(weblsget("45regionaltodosorttype")=="ASC"){
        weblsset("45regionaltodosorttype","DESC")
    }else{
        weblsset("45regionaltodosorttype","ASC")
    }
    location.reload()
})

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

onclick("#edit",function(element,event){
    ajax("GET","/backend/45regional/gettodo/"+clickid,function(event,data){
        if(data["success"]){
            let row=data["data"]
            let starttime=parseInt(row[2])
            let endtime=parseInt(row[3])

            lightbox(null,"lightbox",function(){
                let starttimelist=``
                let endtimelist=``
                let deallist=``
                let prioritylist=``

                for(let i=0;i<=24;i=i+1){
                    if(starttime==i){
                        starttimelist=`
                            ${starttimelist}
                            <option value="${String(i).padStart(2,"0")}" selected>${String(i).padStart(2,"0")}:00</option>
                        `
                    }else{
                        starttimelist=`
                            ${starttimelist}
                            <option value="${String(i).padStart(2,"0")}">${String(i).padStart(2,"0")}:00</option>
                        `
                    }

                    if(endtime==i){
                        endtimelist=`
                            ${endtimelist}
                            <option value="${String(i).padStart(2,"0")}" selected>${String(i).padStart(2,"0")}:00</option>
                        `
                    }else{
                        endtimelist=`
                            ${endtimelist}
                            <option value="${String(i).padStart(2,"0")}">${String(i).padStart(2,"0")}:00</option>
                        `
                    }
                }

                // deallist START
                if(row[4]=="未處理"){
                    deallist=`
                        <option value="未處理" selected>未處理</option>
                        <option value="處理中">處理中</option>
                        <option value="已完成">已完成</option>
                    `
                }

                if(row[4]=="處理中"){
                    deallist=`
                        <option value="未處理">未處理</option>
                        <option value="處理中" selected>處理中</option>
                        <option value="已完成">已完成</option>
                    `
                }

                if(row[4]=="已完成"){
                    deallist=`
                        <option value="未處理">未處理</option>
                        <option value="處理中">處理中</option>
                        <option value="已完成" selected>已完成</option>
                    `
                }
                // deallist END

                // prioritylist START
                if(row[5]=="普通"){
                    prioritylist=`
                        <option value="普通" selected>普通</option>
                        <option value="速件">速件</option>
                        <option value="最速件">最速件</option>
                    `
                }

                if(row[5]=="速件"){
                    prioritylist=`
                        <option value="普通">普通</option>
                        <option value="速件" selected>速件</option>
                        <option value="最速件">最速件</option>
                    `
                }

                if(row[5]=="最速件"){
                    prioritylist=`
                        <option value="普通">普通</option>
                        <option value="速件">速件</option>
                        <option value="最速件" selected>最速件</option>
                    `
                }
                // prioritylist END

                return `
                    <div class="textcenter userlightbox">
                        <div class="newtodotitle">編輯工作</div>
                        <div class="inputmargin textleft">
                            <div class="sttextlabel light">工作標題:</div>
                            <div class="stinput underline">
                                <input type="text" id="title" value="${row[1]}">
                            </div>
                        </div>
                        <div class="stselect fill colorwhite inputmargin">
                            <select class="textcenter" id="starttime">
                                <option value="na" disabled>開始時間</option>
                                ${starttimelist}
                            </select>
                        </div><br>
                        <div class="stselect fill colorwhite inputmargin">
                            <select class="textcenter" id="endtime">
                                <option value="na" disabled>結束時間</option>
                                ${endtimelist}
                            </select>
                        </div><br>
                        <div class="flex inputmargin">
                            <div class="box">
                                處理情形:
                                <div class="stselect solid">
                                    <select class="textcenter" id="deal">
                                        ${deallist}
                                    </select>
                                </div>
                            </div>
                            <div class="box">
                                類別:
                                <div class="stselect solid">
                                    <select class="textcenter" id="priority">
                                        ${prioritylist}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="stinput inputmargin">
                            <textarea class="todotextarea" id="description" placeholder="詳細敘述工作內容">${row[6]}</textarea>
                        </div>
                        <input type="button" class="stbutton light" id="close" value="取消">
                        <input type="button" class="stbutton light" id="submit" value="完成">
                    </div>
                `
            },"close",true,"none")

            onclick("#submit",function(element,event){
                if(parseInt(domgetid("starttime").value)<parseInt(domgetid("endtime").value)){
                    ajax("PUT","/backend/45regional/edittodo/"+clickid,function(event){
                        let data=JSON.parse(event.responseText)
                        if(data["success"]){
                            alert("編輯成功")
                            location.reload()
                        }else{
                            alert(data["data"])
                        }
                    },JSON.stringify({
                        "title": domgetid("title").value,
                        "starttime": domgetid("starttime").value,
                        "endtime": domgetid("endtime").value,
                        "deal": domgetid("deal").value,
                        "priority": domgetid("priority").value,
                        "description": domgetid("description").value
                    }))
                }else{
                    alert("結束時間不可大於開始時間!")
                }
            })
        }
    })
})

onclick("#view",function(element,event){
    ajax("GET","/backend/45regional/gettodo/"+clickid,function(event,data){
        if(data["success"]){
            domgetid("viewdiv").innerHTML=`
                詳細內容:<br>
                ${data["data"][6]}
            `
        }
    })
})

onchange("#dealselect",function(element,event){ main() })

onchange("#priorityselect",function(element,event){ main() })

document.onmouseup=function(){
    check=false
    dragid=null
}

startmacossection()
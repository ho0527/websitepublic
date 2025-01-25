let date=new Date()
let year=date.getFullYear()
let month=date.getMonth()+1
let selectstartdate=null
let selectenddate=null
let canbookroom=[]
let leftroomdata=[]
let bookroomlist=[]

function main(){
    let firstdaythismonth=new Date(year,month-1,1).getDay()
    let totaldaythismonth=new Date(year,month,0).getDate()
    let datecheck=0
    let calendarinnerhtml=`
        <tr>
            <td class="calendartd">日</td>
            <td class="calendartd">一</td>
            <td class="calendartd">二</td>
            <td class="calendartd">三</td>
            <td class="calendartd">四</td>
            <td class="calendartd">五</td>
            <td class="calendartd">六</td>
        </tr>
    `

    canbookroom=[]

    docgetid("yearmonth").innerHTML=`
        ${String(year).padStart(4,"0")} ${String(month).padStart(2,"0")}月
    `

    ajax("GET",ajaxurl+"/getleftroom/"+year+"/"+month,function(event,data){
        leftroomdata=data["data"]

        for(let i=0;i<totaldaythismonth;i=i+1){
            let leftroom=0

            for(let j=0;j<8;j=j+1){
                if(!leftroomdata[i][j+1]){
                    leftroom=leftroom+1
                }
            }

            if(datecheck%7==0){
                calendarinnerhtml=`
                    ${calendarinnerhtml}
                    </tr>
                    <tr>
                `
            }

            if(i==0){
                for(let j=0;j<firstdaythismonth;j=j+1){
                    calendarinnerhtml=`
                        ${calendarinnerhtml}
                        <td class="calendartd"></td>
                    `
                }
                datecheck=datecheck+firstdaythismonth
            }

            calendarinnerhtml=`
                ${calendarinnerhtml}
                <td class="calendartd calendardate" id="calendar_${year}_${month}_${i+1}" data-year="${year}" data-month="${month}" data-date="${i+1}">
                    ${i+1}<br>
                    $5000<br>
                    剩下 ${leftroom} 間房
                </td>
            `
            datecheck=datecheck+1
        }

        docgetid("calendar").innerHTML=calendarinnerhtml

        if(selectstartdate){
            docgetid("startdate").value=String(year).padStart(4,"0")+"/"+String(month).padStart(2,"0")+"/"+String(selectstartdate).padStart(2,"0")
            docgetid("enddate").value=String(year).padStart(4,"0")+"/"+String(month).padStart(2,"0")+"/"+String(selectstartdate).padStart(2,"0")
            docgetid("datecount").value="1"

            docgetid("calendar_"+year+"_"+month+"_"+selectstartdate).style.backgroundColor="lightyellow"
            docgetid("calendar_"+year+"_"+month+"_"+selectstartdate).style.color="black"

            docgetid("roomcount").disabled=true
            docgetid("roomcount").classList.add("disabled")
            docgetid("prevmonth").disabled=true
            docgetid("prevmonth").classList.add("disabled")
            docgetid("nextmonth").disabled=true
            docgetid("nextmonth").classList.add("disabled")
        }

        if(selectenddate&&selectenddate!=selectstartdate){
            docgetid("calendar_"+year+"_"+month+"_"+selectenddate).style.backgroundColor="lightyellow"
            docgetid("calendar_"+year+"_"+month+"_"+selectenddate).style.color="black"

            for(let i=selectstartdate;i<selectenddate;i=i+1){
                docgetid("calendar_"+year+"_"+month+"_"+i).style.backgroundColor="lightyellow"
                docgetid("calendar_"+year+"_"+month+"_"+i).style.color="black"
            }

            docgetid("datecount").value=selectenddate-selectstartdate+1
            docgetid("enddate").value=String(year).padStart(4,"0")+"/"+String(month).padStart(2,"0")+"/"+String(selectenddate).padStart(2,"0")

            docgetid("selectroom").disabled=true
            docgetid("selectroom").classList.add("disabled")

        }

        onclick(".calendardate",function(element,event){
            bookroomlist=[]
            if(selectstartdate==null){
                let canbookroomtemp=[]

                for(let i=0;i<8;i=i+1){
                    if(!leftroomdata[parseInt(element.dataset.date)-1][i+1]){
                        canbookroom.push(i+1)
                        canbookroomtemp.push(i+1)
                    }
                }

                if(docgetid("roomcount").value<=canbookroom.length){
                    let value=[]

                    element.style.backgroundColor="lightyellow"
                    element.style.color="black"
                    selectstartdate=parseInt(element.dataset.date)

                    docgetid("startdate").value=String(year).padStart(4,"0")+"/"+String(month).padStart(2,"0")+"/"+String(selectstartdate).padStart(2,"0")
                    docgetid("enddate").value=String(year).padStart(4,"0")+"/"+String(month).padStart(2,"0")+"/"+String(selectstartdate).padStart(2,"0")
                    docgetid("datecount").value="1"

                    docgetid("roomcount").disabled=true
                    docgetid("roomcount").classList.add("disabled")
                    docgetid("prevmonth").disabled=true
                    docgetid("prevmonth").classList.add("disabled")
                    docgetid("nextmonth").disabled=true
                    docgetid("nextmonth").classList.add("disabled")

                    for(let i=0;i<parseInt(docgetid("roomcount").value);i=i+1){
                        let random=parseInt(Math.random()*canbookroomtemp.length)

                        value.push("room"+canbookroomtemp[random])
                        bookroomlist.push(canbookroomtemp[random])
                        canbookroomtemp.splice(random,1)
                    }

                    value.sort()
                    bookroomlist.sort()

                    docgetid("roomno").value=value.join("，")
                }else{
                    alert("房間數量不足")
                }
            }else if(selectenddate==null){
                if(selectstartdate<=parseInt(element.dataset.date)){
                    let canbookroomtemp=[]

                    selectenddate=parseInt(element.dataset.date)

                    for(let i=0;i<canbookroom.length;i=i+1){
                        canbookroomtemp.push(canbookroom[i])
                    }

                    for(let i=selectstartdate;i<selectenddate;i=i+1){
                        for(let j=0;j<8;j=j+1){
                            if(leftroomdata[i][j+1]){
                                for(let k=0;k<canbookroomtemp.length;k=k+1){
                                    if(canbookroomtemp[k]==j+1){
                                        canbookroomtemp.splice(k,1)
                                    }
                                }
                            }
                        }
                    }

                    if(docgetid("roomcount").value<=canbookroomtemp.length){
                        let value=[]

                        bookroomlist=[]

                        element.style.backgroundColor="lightyellow"
                        element.style.color="black"

                        for(let i=selectstartdate;i<selectenddate;i=i+1){
                            docgetid("calendar_"+year+"_"+month+"_"+i).style.backgroundColor="lightyellow"
                            docgetid("calendar_"+year+"_"+month+"_"+i).style.color="black"
                        }

                        docgetid("datecount").value=selectenddate-selectstartdate+1
                        docgetid("enddate").value=String(year).padStart(4,"0")+"/"+String(month).padStart(2,"0")+"/"+String(selectenddate).padStart(2,"0")

                        docgetid("selectroom").disabled=true
                        docgetid("selectroom").classList.add("disabled")

                        for(let i=0;i<parseInt(docgetid("roomcount").value);i=i+1){
                            let random=parseInt(Math.random()*canbookroomtemp.length)

                            value.push("room"+canbookroomtemp[random])
                            bookroomlist.push(canbookroomtemp[random])
                            canbookroomtemp.splice(random,1)
                        }

                        value.sort()
                        bookroomlist.sort()

                        docgetid("roomno").value=value.join("，")
                    }else{
                        alert("房間數量不足")
                        selectenddate=null
                    }
                }else{ /* alert("入住最後一晚要大於第一晚日期") */ }
            }else{
                alert("如要修改請先取消選擇日期")
            }
        })
    })
}

if(weblsget("54regionaladminroomeditid")){
    ajax("GET",ajaxurl+"/getroomorder/"+weblsget("54regionaladminroomeditid"),function(event,data){
        if(data["success"]){
            let row=data["data"]
            year=parseInt(row["startdate"].split("/")[0])
            month=parseInt(row["startdate"].split("/")[1])
            selectstartdate=parseInt(row["startdate"].split("/")[2])
            selectenddate=parseInt(row["enddate"].split("/")[2])
            bookroomlist=row["roomno"].split(",")
            docgetid("roomno").value="room"+bookroomlist.join("，room")
            docgetid("autoselect").disabled=true
            docgetid("autoselect").classList.add("disabled")
            docgetid("selectroom").disabled=true
            docgetid("selectroom").classList.add("disabled")
        }
        main()
    })
}else{
    main()
}


onclick("#prevmonth",function(element,event){
    if(month-1<1){
        year=year-1
        month=12
    }else{
        month=month-1
    }
    main()
})

onclick("#nextmonth",function(element,event){
    if(month+1>12){
        year=year+1
        month=1
    }else{
        month=month+1
    }
    main()
})

onclick("#autoselect",function(element,event){
    if(selectstartdate!=null){
        let value=[]
        let canbookroomtemp=[]

        bookroomlist=[]

        for(let i=0;i<canbookroom.length;i=i+1){
            canbookroomtemp.push(canbookroom[i])
        }

        for(let i=0;i<parseInt(docgetid("roomcount").value);i=i+1){
            let random=parseInt(Math.random()*canbookroomtemp.length)

            value.push("room"+canbookroomtemp[random])
            bookroomlist.push(canbookroomtemp[random])
            canbookroomtemp.splice(random,1)
        }

        value.sort()
        bookroomlist.sort()

        docgetid("roomno").value=value.join("，")
    }else{
        alert("請先選擇時間")
    }
})

onclick("#selectroom",function(element,event){
    if(selectstartdate!=null){
        let roomcount=0
        let bookdata=docgetid("roomno").value.split("，")

        lightbox(null,"lightbox",function(){
            let innerhtml=``
            for(let i=0;i<8;i=i+1){
                let value="空房"
                let classlist="outline roomselectbutton"

                if(leftroomdata[selectstartdate-1][i+1]){
                    classlist="disabled light"
                    value="已訂"
                }

                for(let j=0;j<bookdata.length;j=j+1){
                    if("room"+(i+1)==bookdata[j]){
                        classlist="light roomselectbutton"
                    }
                }

                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="button ${classlist}" id="roombutton_${i+1}" style="width: 10vw;" value="room${i+1}(${value})">
                `
            }
            return `
                <div class="textcenter">
                    ${innerhtml}
                    <div class="text error textcenter large bold" id="leftroom">還剩 0 間房可選</div>
                    <div class="textcenter">
                        <input type="button" class="button light" id="closeselectbutton" value="返回">
                        <input type="button" class="button light" id="clearselectbutton" value="清除">
                        <input type="button" class="button light" id="submitselectbutton" value="送出">
                    </div>
                </div>
            `
        },"closeselectbutton")

        onclick("#clearselectbutton",function(element2,event2){
            roomcount=docgetid("roomcount").value

            docgetall(".roomselectbutton").forEach(function(event3){
                event3.classList="stbutton outline roomselectbutton"
            })

            docgetid("leftroom").innerHTML=`
                還剩 ${roomcount} 間房可選
            `
        })

        onclick(".roomselectbutton",function(element2,event2){
            if(element2.classList.contains("light")){
                element2.classList="stbutton outline roomselectbutton"
                roomcount=roomcount+1
            }else{
                if(roomcount>0){
                    element2.classList="stbutton light roomselectbutton"
                    roomcount=roomcount-1
                }else{
                    alert("房間以選擇完畢，如果需要選擇請先取消不需要的房間")
                }
            }

            docgetid("leftroom").innerHTML=`
                還剩 ${roomcount} 間房可選
            `
        })

        onclick("#submitselectbutton",function(element2,event2){
            if(roomcount==0){
                let roomorderdata=[]

                docgetall(".roomselectbutton").forEach(function(event3){
                    if(event3.classList.contains("light")){
                        roomorderdata.push("room"+event3.id.split("_")[1])
                    }
                })

                docgetid("roomno").value=roomorderdata.join("，")

                lightboxclose()
            }else{
                alert("尚有 "+roomcount+" 個房間未選擇")
            }
        })
    }else{
        alert("請先選擇時間")
    }
})

onclick("#clear",function(element,event){
    selectstartdate=null
    selectenddate=null

    docgetid("startdate").value=""
    docgetid("enddate").value=""
    docgetid("roomno").value=""

    docgetid("autoselect").disabled=false
    docgetid("autoselect").classList.remove("disabled")
    docgetid("roomcount").disabled=false
    docgetid("roomcount").classList.remove("disabled")
    docgetid("prevmonth").disabled=false
    docgetid("prevmonth").classList.remove("disabled")
    docgetid("nextmonth").disabled=false
    docgetid("nextmonth").classList.remove("disabled")
    docgetid("selectroom").disabled=false
    docgetid("selectroom").classList.remove("disabled")

    main()
})

onclick("#submit",function(element,event){
    if(docgetid("roomno").value!=""){
        lightbox(null,"lightbox",function(){
            return `
                <div class="inputmargin">
                    <div class="text">訂房間數</div>
                    <div class="input">
                        <input type="text" value="${docgetid("roomcount").value}" readonly>
                    </div>
                </div>
                <div class="inputmargin">
                    <div class="text">入住天數</div>
                    <div class="input">
                        <input type="text" value="${docgetid("datecount").value}" readonly>
                    </div>
                </div>
                <div class="inputmargin">
                    <div class="text">入住日期</div>
                    <div class="input">
                        <input type="text" value="${docgetid("startdate").value}" readonly>
                        ~
                        <input type="text" value="${docgetid("enddate").value}" readonly>
                    </div>
                </div>
                <div class="inputmargin">
                    <div class="text">房間號碼</div>
                    <div class="input">
                        <input type="text" value="${docgetid("roomno").value}" readonly>
                    </div>
                </div>
                <div class="inputmargin">
                    <div class="text">總金額</div>
                    <div class="input">
                        <input type="text" value="${parseInt(docgetid("roomcount").value)*parseInt(docgetid("datecount").value)*5000}" readonly>
                    </div>
                </div>
                <div class="inputmargin">
                    <div class="text">需付訂金</div>
                    <div class="input">
                        <input type="text" value="${parseInt(docgetid("roomcount").value)*parseInt(docgetid("datecount").value)*5000*0.3} (訂金為30%本金)" readonly>
                    </div>
                </div>
                <div class="textcenter">
                    <input type="button" class="button light" id="close" value="取消">
                    <input type="button" class="button light" id="check" value="確定訂房">
                </div>
            `
        },"close",true,"none")

        onclick("#check",function(element2,event2){
            if(weblsget("54regionaladminroomeditid")){
                ajax("GET",ajaxurl+"/getroomorder/"+weblsget("54regionaladminroomeditid"),function(event,data){
                    if(data["success"]){
                        let row=data["data"]
                        lightbox(null,"lightbox",function(){
                            return `
                                <div style="width: 100%;">
                                    <div class="inputmargin">
                                        <div class="text">姓名</div>
                                        <div class="input light">
                                            <input type="text" id="username" value="${row["username"]}">
                                        </div>
                                    </div>
                                    <div class="inputmargin">
                                        <div class="text">email</div>
                                        <div class="input light">
                                            <input type="text" id="email" value="${row["email"]}">
                                        </div>
                                    </div>
                                    <div class="inputmargin">
                                        <div class="text">電話</div>
                                        <div class="input light">
                                            <input type="text" id="phone" value="${row["phone"]}">
                                        </div>
                                    </div>
                                    <div class="input light inputmargin inputdiv">
                                        <textarea class="commenttextarea" id="content" placeholder="備註">${row["ps"]}</textarea>
                                    </div>
                                    <div class="textcenter">
                                        <input type="button" class="button light" id="close" value="取消">
                                        <input type="button" class="button light" id="check" value="送出">
                                    </div>
                                </div>
                            `
                        },"close",true,"none")

                        onclick("#check",function(element3,event3){
                            ajax("PUT",ajaxurl+"/editroomorder/"+weblsget("54regionaladminroomeditid"),function(event,data){
                                if(data["success"]){
                                    alert("修改成功")
                                    weblsset("54regionaladminroomeditid",null)
                                    location.href="adminroom.html"
                                }
                            },JSON.stringify({
                                "startdate": docgetid("startdate").value,
                                "enddate": docgetid("enddate").value,
                                "roomno": bookroomlist.join(","),
                                "username": docgetid("username").value,
                                "email": docgetid("email").value,
                                "phone": docgetid("phone").value,
                                "totalprice": parseInt(docgetid("roomcount").value)*parseInt(docgetid("datecount").value)*5000,
                                "deposit": parseInt(docgetid("roomcount").value)*parseInt(docgetid("datecount").value)*5000*0.3,
                                "ps": docgetid("content").value
                            }))
                        })
                    }
                })
            }else{
                lightbox(null,"lightbox",function(){
                    return `
                        <div style="width: 35vw;">
                            <div class="inputmargin">
                                <div class="text">姓名</div>
                                <div class="input light">
                                    <input type="text" id="username">
                                </div>
                            </div>
                            <div class="inputmargin">
                                <div class="text">email</div>
                                <div class="input light">
                                    <input type="text" id="email">
                                </div>
                            </div>
                            <div class="inputmargin">
                                <div class="text">電話</div>
                                <div class="input light">
                                    <input type="text" id="phone">
                                </div>
                            </div>
                            <div class="input light inputmargin inputdiv">
                                <textarea class="commenttextarea" id="content" placeholder="備註"></textarea>
                            </div>
                            <div class="textcenter">
                                <input type="button" class="button light" id="close" value="取消">
                                <input type="button" class="button light" id="check" value="送出">
                            </div>
                        </div>
                    `
                },"close",true,"none")

                onclick("#check",function(element3,event3){
                    ajax("POST",ajaxurl+"/newroomorder",function(event,data){
                        if(data["success"]){
                            alert("上傳成功 訂房編號為:"+data["data"])
                            location.reload()
                        }
                    },JSON.stringify({
                        "startdate": docgetid("startdate").value,
                        "enddate": docgetid("enddate").value,
                        "roomno": bookroomlist.join(","),
                        "username": docgetid("username").value,
                        "email": docgetid("email").value,
                        "phone": docgetid("phone").value,
                        "totalprice": parseInt(docgetid("roomcount").value)*parseInt(docgetid("datecount").value)*5000,
                        "deposit": parseInt(docgetid("roomcount").value)*parseInt(docgetid("datecount").value)*5000*0.3,
                        "ps": docgetid("content").value
                    }))
                })
            }
        })

        document.onkeydown=function(event){
            if(event.key=="Enter"){
                docgetid("check").click()
            }
        }
    }else{
        alert("請先選擇日期")
    }
})
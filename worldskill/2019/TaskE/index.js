let midx=window.innerWidth/2-60
let midy=window.innerHeight/2-60
let data={}
let count=1
let candelete=false
let counter

// 初始化函式
function main(){
    if(!weblsget("worldskill2022ME")){
        weblsset("worldskill2022ME",JSON.stringify({
            "data": [
                {
                    "id": 0,
                    "position": {
                        "top": midy,
                        "left": midx
                    },
                    "content": "",
                    "1": {
                        "id": "",
                        "title": "",
                        "position": {
                            "top": 0,
                            "left": 0
                        }
                    },
                    "2": {
                        "id": "",
                        "title": "",
                        "position": {
                            "top": 0,
                            "left": 0
                        }
                    },
                    "3": {
                        "id": "",
                        "title": "",
                        "position": {
                            "top": 0,
                            "left": 0
                        }
                    },
                    "4": {
                        "id": "",
                        "title": "",
                        "position": {
                            "top": 0,
                            "left": 0
                        }
                    }
                }
            ]
        }))
    }
    data=json(weblsget("worldskill2022ME"))

    domgetid("main").innerHTML=``
    for(let i=0;i<data["data"].length;i=i+1){
        if(data["data"][i]){
            domgetid("main").innerHTML=`
                ${domgetid("main").innerHTML}
                <div class="elementdiv" id="${i}" style="position: absolute;top: ${data["data"][i]["position"]["top"]}px;left: ${data["data"][i]["position"]["left"]}px;">
                    <div class="elementposition">
                        <div class="element"></div>
                    </div>
                </div>
            `

            for(let j=1;j<=4;j=j+1){
                if(data["data"][i][j]["title"]!=""){
                    let toplength=(data["data"][i]["position"]["top"]-data["data"][i][j]["position"]["top"])
                    let leftlength=(data["data"][i]["position"]["left"]-data["data"][i][j]["position"]["left"])
                    let elementdistance=((toplength**2)+(leftlength**2))**(1/2)
                    let rotate
                    let top
                    let left
                    let tempid

                    if(leftlength==0){
                        if(j==1){
                            rotate="0"
                        }else{
                            rotate="180"
                        }
                    }else{
                        rotate=Math.asin(leftlength/elementdistance)*(180/Math.PI)
                    }

                    if(j=="1"){
                        top=-140
                        left=60
                        tempid="3"
                    }else if(j=="2"){
                        top=-20
                        left=185
                        tempid="4"
                    }else if(j=="3"){
                        top=110
                        left=60
                        tempid="1"
                    }else if(j=="4"){
                        top=-20
                        left=-65
                        tempid="2"
                    }

                    if(data["data"][i]["id"]<parseInt(data["data"][i][j]["id"])){
                        domgetid("main").innerHTML=`
                            ${domgetid("main").innerHTML}
                            <div class="line" id="line_${i}_${j}" style="
                                position: absolute;top: ${data["data"][i]["position"]["top"]+top}px;left: ${data["data"][i]["position"]["left"]+left}px;height: ${elementdistance-90}px;rotate: ${rotate}deg;
                            "></div>
                        `
                    }

                    domgetid(i).dataset["link"+j+"to"]=data["data"][i][j]["id"]+"_"+tempid
                }
            }
        }
    }

    count=data["data"].length

    domgetall(".line").forEach(function(event){
        event.onpointerdown=function(){
            let eventid=event.id.split("_")
            let tempid
            domgetall(".line").forEach(function(event){
                event.style.backgroundColor="lightgray"
            })
            event.style.backgroundColor="lightblue"
            if(eventid[2]=="1"){
                tempid="3"
            }else if(eventid[2]=="2"){
                tempid="4"
            }else if(eventid[2]=="3"){
                tempid="1"
            }else if(eventid[2]=="4"){
                tempid="2"
            }
            candelete=true

            document.onkeydown=function(event2){
                if((event2.key=="Delete"||event2.key=="Backspace")&&candelete){
                    data["data"][data["data"][eventid[1]][eventid[2]]["id"]][tempid]={
                        "id": "",
                        "title": "",
                        "position": {
                            "top": 0,
                            "left": 0
                        }
                    }
                    data["data"][eventid[1]][eventid[2]]={
                        "id": "",
                        "title": "",
                        "position": {
                            "top": 0,
                            "left": 0
                        }
                    }
                    event.remove()
                    weblsset("worldskill2022ME",JSON.stringify(data))
                    main()
                    candelete=false
                }
            }

        }
    })

    domgetall(".elementdiv").forEach(function(event){
        let time=0
        let clickelement=""

        // hover時顯示
        event.onpointerover=function(){
            event.querySelectorAll(".element")[0].innerHTML=`
                <div class="elementposition2" id="${event.id}_mainelement">
                    <div class="element1" id="${event.id}_element1"><div class="element1text">1</div></div>
                    <div class="element2" id="${event.id}_element2"><div class="element2text">2</div></div>
                    <div class="element3" id="${event.id}_element3"><div class="element3text">3</div></div>
                    <div class="element4" id="${event.id}_element4"><div class="element4text">4</div></div>
                    <input type="button" class="elementedit" id="${event.id}_edit" value="E">
                    <input type="button" class="elementdelete" id="${event.id}_delete" value="X">
                </div>
            `

            // 驗證初始化 START
            domgetid(event.id+"_mainelement").onpointerdown=function(){
                document.onkeydown=function(event){
                    if(event.shiftKey){
                    }
                }
                counter=setInterval(function(){
                    time=time+1
                },10)
                candelete=false
            }

            domgetid(event.id+"_mainelement").onpointermove=function(event2){
                if(time>15){
                    let x=event2.pageX
                    let y=event2.pageY
                    event.style.top=y-70+"px"
                    event.style.left=x-62.5+"px"
                    for(let i=1;i<=4;i=i+1){
                        if(event.dataset["link"+i+"to"]){
                            let linkid=event.dataset["link"+i+"to"].split("_")[0]
                            let keyid=event.dataset["link"+i+"to"].split("_")[1]
                            let toplength=(y-130-data["data"][linkid]["position"]["top"])
                            let leftlength=(x-62.5-data["data"][linkid]["position"]["left"])
                            let elementdistance=((toplength**2)+(leftlength**2))**(1/2)
                            let rotate
                            let top
                            let left
                            let tempid
                            let lineid

                            if(leftlength==0){
                                if(i==1){
                                    rotate="0"
                                }else{
                                    rotate="180"
                                }
                            }else{
                                rotate=Math.asin(leftlength/elementdistance)*(180/Math.PI)
                            }

                            if(i=="1"){
                                top=-140
                                left=60
                                tempid="3"
                            }else if(i=="2"){
                                top=-20
                                left=185
                                tempid="4"
                            }else if(i=="3"){
                                top=110
                                left=60
                                tempid="1"
                            }else if(i=="4"){
                                top=-20
                                left=-65
                                tempid="2"
                            }

                            // line update
                            if(linkid<event.id){
                                lineid=linkid+"_"+keyid
                            }else{
                                lineid=event+"_"+i
                            }

                            domgetid("line_"+lineid).style.height=elementdistance-90+"px"
                            domgetid("line_"+lineid).style.rotate=rotate+"deg"
                        }
                    }
                }
                candelete=false
            }

            domgetid(event.id+"_mainelement").onpointerup=function(){
                if(time<=15){
                    newelement(event.id,clickelement)
                    clearInterval(counter)
                    clickelement=""
                    time=0
                }else{
                    main()
                    clearInterval(counter)
                    clickelement=""
                    time=0
                }
                candelete=false
            }

            domgetid(event.id+"_mainelement").onpointerout=function(){
                clearInterval(counter)
                clickelement=""
                time=0
                candelete=false
            }
            // 驗證初始化 END

            // 各元素創建 START
            domgetid(event.id+"_element1").onpointerdown=function(){
                clickelement="1"
                candelete=false
            }

            domgetid(event.id+"_element2").onpointerdown=function(){
                clickelement="2"
                candelete=false
            }

            domgetid(event.id+"_element3").onpointerdown=function(){
                clickelement="3"
                candelete=false
            }

            domgetid(event.id+"_element4").onpointerdown=function(){
                clickelement="4"
                candelete=false
            }

            domgetid(event.id+"_edit").onpointerdown=function(){
                lightbox(null,"lightbox",function(){
                    let disabled1=""
                    let disabled2=""
                    let disabled3=""
                    let disabled4=""

                    if(data["data"][event.id]["1"]["id"]==""){
                        disabled1="disabled"
                    }
                    if(data["data"][event.id]["2"]["id"]==""){
                        disabled2="disabled"
                    }
                    if(data["data"][event.id]["3"]["id"]==""){
                        disabled3="disabled"
                    }
                    if(data["data"][event.id]["4"]["id"]==""){
                        disabled4="disabled"
                    }

                    return `
                        <div class="close" id="close"></div>
                        <div class="stinput light">
                            <textarea class="edittextarea" id="description" placeholder="pls type word that need to show">${data["data"][event.id]["content"]}</textarea>
                        </div>
                        <div class="stinput inputmargin light">
                            <input class="editinput ${disabled1}" id="relationtitle1" placeholder="relation 1" value="${data["data"][event.id]["1"]["title"]}" ${disabled1}>
                        </div>
                        <div class="stinput inputmargin light">
                            <input class="editinput ${disabled2}" id="relationtitle2" placeholder="relation 2" value="${data["data"][event.id]["2"]["title"]}" ${disabled2}>
                        </div>
                        <div class="stinput inputmargin light">
                            <input class="editinput ${disabled3}" id="relationtitle3" placeholder="relation 3" value="${data["data"][event.id]["3"]["title"]}" ${disabled3}>
                        </div>
                        <div class="stinput inputmargin light">
                            <input class="editinput ${disabled4}" id="relationtitle4" placeholder="relation 4" value="${data["data"][event.id]["4"]["title"]}" ${disabled4}>
                        </div>
                    `
                },"close",true,"mask")
                domgetid("description").onchange=function(){
                    data["data"][event.id]["content"]=this.value
                }

                domgetid("relationtitle1").onchange=function(){
                    data["data"][event.id]["1"]["title"]=this.value
                }

                domgetid("relationtitle2").onchange=function(){
                    data["data"][event.id]["2"]["title"]=this.value
                }

                domgetid("relationtitle3").onchange=function(){
                    data["data"][event.id]["3"]["title"]=this.value
                }

                domgetid("relationtitle4").onchange=function(){
                    data["data"][event.id]["4"]["title"]=this.value
                }

                event.querySelectorAll(".element")[0].innerHTML=``
                weblsset("worldskill2022ME",JSON.stringify(data))
                main()
                candelete=false
            }

            domgetid(event.id+"_delete").onpointerdown=function(){
                if(event.id!=0){
                    for(let i=0;i<data["data"].length;i=i+1){
                        if(data["data"][i]){
                            for(let j=1;j<=4;j=j+1){
                                if(data["data"][i][j]["id"]==event.id){
                                    data["data"][i][j]={
                                        "id": "",
                                        "title": "",
                                        "position": {
                                            "top": 0,
                                            "left": 0
                                        }
                                    }
                                }
                            }
                        }
                    }
                    delete data["data"][event.id]
                    domgetid(event.id).remove()
                    weblsset("worldskill2022ME",JSON.stringify(data))
                    main()
                }else{
                    alert("不得刪除根元素")
                }
                candelete=false
            }
            // 各元素創建 END
        }

        // 離開時清空
        event.onpointerleave=function(){
            event.querySelectorAll(".element")[0].innerHTML=``
        }
    })
}

// 新元素函式
function newelement(id,key){
    let topchange=0
    let leftchange=0
    let thisdata=data["data"][id]

    if(thisdata["1"]["title"]==""&&key=="1"){
        topchange=-250
    }else if(thisdata["2"]["title"]==""&&key=="2"){
        leftchange=250
    }else if(thisdata["3"]["title"]==""&&key=="3"){
        topchange=250
    }else if(thisdata["4"]["title"]==""&&key=="4"){
        leftchange=-250
    }

    if(topchange!=0||leftchange!=0){
        domgetid("main").innerHTML=`
            ${domgetid("main").innerHTML}
            <div class="elementdiv" id="${count}" style="position: absolute;top: ${thisdata["position"]["top"]+topchange}px;left: ${thisdata["position"]["left"]+leftchange}px;">
                <div class="elementposition">
                    <div class="element"></div>
                </div>
            </div>

            <div class="line"></div>
        `

        data["data"].push({
            "id": count,
            "position": {
                "top": thisdata["position"]["top"]+topchange,
                "left": thisdata["position"]["left"]+leftchange
            },
            "content": "",
            "1": {
                "id": "",
                "title": "",
                "position": {
                    "top": 0,
                    "left": 0
                }
            },
            "2": {
                "id": "",
                "title": "",
                "position": {
                    "top": 0,
                    "left": 0
                }
            },
            "3": {
                "id": "",
                "title": "",
                "position": {
                    "top": 0,
                    "left": 0
                }
            },
            "4": {
                "id": "",
                "title": "",
                "position": {
                    "top": 0,
                    "left": 0
                }
            }
        })

        if(key=="1"){
            data["data"][data["data"].length-1]["3"]["id"]=id
            data["data"][data["data"].length-1]["3"]["title"]="default relation 3"
            data["data"][data["data"].length-1]["3"]["position"]["top"]=thisdata["position"]["top"]
            data["data"][data["data"].length-1]["3"]["position"]["left"]=thisdata["position"]["left"]
        }else if(key=="2"){
            data["data"][data["data"].length-1]["4"]["id"]=id
            data["data"][data["data"].length-1]["4"]["title"]="default relation 4"
            data["data"][data["data"].length-1]["4"]["position"]["top"]=thisdata["position"]["top"]
            data["data"][data["data"].length-1]["4"]["position"]["left"]=thisdata["position"]["left"]
        }else if(key=="3"){
            data["data"][data["data"].length-1]["1"]["id"]=id
            data["data"][data["data"].length-1]["1"]["title"]="default relation 1"
            data["data"][data["data"].length-1]["1"]["position"]["top"]=thisdata["position"]["top"]
            data["data"][data["data"].length-1]["1"]["position"]["left"]=thisdata["position"]["left"]
        }else if(key=="4"){
            data["data"][data["data"].length-1]["2"]["id"]=id
            data["data"][data["data"].length-1]["2"]["title"]="default relation 2"
            data["data"][data["data"].length-1]["2"]["position"]["top"]=thisdata["position"]["top"]
            data["data"][data["data"].length-1]["2"]["position"]["left"]=thisdata["position"]["left"]
        }

        thisdata[key]["title"]="default relation "+key
        thisdata[key]["id"]=count
        thisdata[key]["position"]["top"]=thisdata["position"]["top"]+topchange
        thisdata[key]["position"]["left"]=thisdata["position"]["left"]+leftchange

        weblsset("worldskill2022ME",JSON.stringify(data))
        main()
    }
    candelete=false
}

function presentationmain(id){
    let thisdata=data["data"][id]
    let buttoninnerhtml=``

    for(let i=1;i<=4;i=i+1){
        if(thisdata[i]["id"]!=""){
            buttoninnerhtml=`
                ${buttoninnerhtml}
                <input type="button" class="stbutton light fill" id="${i}" onclick="presentationmain(${thisdata[i]["id"]})" value="${i}-${thisdata[i]["title"]}">
            `
        }
    }

    docgetid("main").innerHTML=`
        <div class="stinput presentationcontent disabled">
            <textarea disabled>${thisdata["content"]}</textarea>
        </div>
        <div class="presentationbuttondiv" id="buttondiv">
            ${buttoninnerhtml}
        </div>
    `

}

main()

domgetid("mode").onclick=function(){
    if(!document.fullscreenElement){
        document.documentElement.requestFullscreen()
        domgetid("main").classList.remove("indexmain")
        domgetid("main").classList.remove("macossectiondivy")
        domgetid("main").classList.add("presentationmain")
        domgetid("main").classList.add("center")
        presentationmain(0)
        this.value="design mode"
    }else{
        document.exitFullscreen()
        domgetid("main").classList.add("indexmain")
        domgetid("main").classList.add("macossectiondivy")
        domgetid("main").classList.remove("presentationmain")
        domgetid("main").classList.remove("center")
        main()
        this.value="presentation mode"
    }
}

// 函式庫初始化
startmacossection()
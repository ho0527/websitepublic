let traintypedata
let stationdata
let traindata
let stationcount=0

function stationdelete(){
    for(let i=0;i<docgetall(".stationdelect").length;i=i+1){
        docgetall(".stationdelect")[i].onclick=function(){
            docgetall(".station")[i].remove()
            stationcount=stationcount-1
            stationdelete()
        }
    }
}

function newstation(){
    if(stationcount<15){
        let station=""

        for(let i=0;i<stationdata.length;i=i+1){
            station=station+`<option value="${stationdata[i][0]}">${stationdata[i][2]}</option>`
        }

        let div=doccreate("div")
        div.classList.add("station")
        div.innerHTML=`
            <div class="stationgrid grid">
                <select class="stationselect">
                    <option value="na">請選擇車站</option>
                    ${station}
                </select>
                <input type="text" class="input2 arrivetime" placeholder="抵達時間(HH:MM)">
                <input type="text" class="input2 stoptime" placeholder="停留時間(MM)">
                <input type="text" class="input2 price" placeholder="價格">
                <input type="button" class="noborderbutton stationdelect" value="X">
            </div>
        `
        docappendchild("stationdiv",div)

        stationcount=stationcount+1

        stationdelete()
        divsort("station",".stationdiv")
    }else{ alert("車站已達上限") }
}

function formsubmit(key,id=null){
    let url=""
    if(key=="new"){
        url="api/newtrain.php"
    }else{
        url="api/edittrain.php"
    }
    let code=docgetid("code").value
    let traintype=docgetid("traintype").value
    let week=docgetid("week").value
    let starttime=docgetid("starttime").value
    let station=[]
    let price=[]
    let arrivetime=[]
    let stoptime=[]
    let data=[]
    let success=true

    if(code==""){
        alert("請輸入列車代碼")
        success=false
    }

    for(let i=0;i<traindata.length;i=i+1){
        if(traindata[i][0]==code){
            alert("列車代碼不可重複")
            success=false
            break
        }
    }

    if(traintype=="na"){
        alert("請選擇車種")
        success=false
    }

    if(week=="na"){
        alert("請選擇星期")
        success=false
    }

    if(!regexpmatch(starttime,"^[0-9]{2}\:[0-9]{2}$")){
        alert("開始時間格式不正確(格式: HH:MM)")
        success=false
    }

    if(2<=stationcount&&stationcount<=15){
        for(let i=0;i<stationcount;i=i+1){
            let stationvalue=docgetall(".stationselect")[i].value
            let pricevalue=docgetall(".price")[i].value
            let arrivetimevalue=docgetall(".arrivetime")[i].value
            let stoptimevalue=docgetall(".stoptime")[i].value
            if(station.includes(stationvalue)){
                alert("車站不可重複")
                success=false
            }
            if(!regexpmatch(arrivetimevalue,"^[0-9]{2}\:[0-9]{2}$")){
                alert("抵達時間格式不正確(格式: HH:MM)")
                success=false
            }
            if(!regexpmatch(stoptimevalue,"^[0-9]{2}$")){
                alert("停留時間格式不正確(格式: MM)")
                success=false
            }
            if(!regexpmatch(pricevalue,"^[0-9]+(\.[0-9]+)?$")){
                alert("價格必須為數字")
                success=false
            }
            station.push(stationvalue)
            price.push(pricevalue)
            arrivetime.push(arrivetimevalue)
            stoptime.push(stoptimevalue)
        }
    }else{
        alert("至少要有兩個車站,至多15個車站")
        success=false
    }

    if(success){
        data=[id,code,traintype,week,starttime,stationcount,station,price,arrivetime,stoptime]

        fetch(url,{
            method:"POST",
            body:JSON.stringify(data),
            headers:{ "Content-Type":"application/json" },
        }).then(function(response){ console.log(response.text()) })
        .then(function(){ alert("儲存成功");location.reload() })
        .catch(function(event){ console.error("Error:",event) })
    }else{}
}

if(!weblsget("46nationalmoduleduserid")){
    alert("請先登入")
    location.href="login.html"
}

oldajax("GET","/backend/46nationalmoduled/mangertraintype/").onload=function(){ traintypedata=JSON.parse(this.responseText) }
oldajax("GET","/backend/46nationalmoduled/mangerstation/").onload=function(){ stationdata=JSON.parse(this.responseText) }
oldajax("GET","/backend/46nationalmoduled/mangertrain/").onload=function(){ traindata=JSON.parse(this.responseText) }

lightbox("#new","lightbox",function(){
    let traintype=""

    for(let i=0;i<traintypedata["data"].length;i=i+1){
        traintype=traintype+`<option value="${traintypedata["data"][i][0]}">${traintypedata["data"][i][1]}</option>`
    }

    return `
        <form method="POST">
            <div class="grid trainlightboxgrid">
                <div class="trainleft">
                    列車代碼<br>
                    <input type="text" class="lightboxinput" id="code"><br><br><br>
                    車種<br>
                    <select class="select" id="traintype">
                        <option value="na">請選擇車種</option>
                        ${traintype}
                    </select><br><br><br>
                    行車星期<br>
                    <select class="select" id="week">
                        <option value="na">請選擇行車星期</option>
                        <option value="7">星期日</option>
                        <option value="1">星期一</option>
                        <option value="2">星期二</option>
                        <option value="3">星期三</option>
                        <option value="4">星期四</option>
                        <option value="5">星期五</option>
                        <option value="6">星期六</option>
                    </select><br><br><br>
                    發車時間<br>
                    <input type="text" class="lightboxinput" id="starttime" placeholder="HH:MM">
                </div>
                <div class="trainright">
                    行經車站: <input type="button" class="button" onclick="newstation()" value="新增">
                    <div class="stationdiv macossectiondivy" id="stationdiv"></div>
                </div>
                <div class="trainbutton">
                    <input type="button" class="button" id="close" value="返回">
                    <input type="reset" class="button" value="清除">
                    <input type="button" class="button" onclick="formsubmit('new')" value="送出">
                </div>
            </div>
        </form>
    `
},"close")

setTimeout(function(){
    oldajax("GET","/backend/46nationalmoduled/mangertrain/").onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            let trainlist=data["data"][0]
            let stoplist=data["data"][1]
            let stoplistfilterdata=[]
            for(let i=0;i<trainlist.length;i=i+1){
                let id=trainlist[i][0]
                let stoplistfilter=stoplist.filter(function(event){ return event[1]==id }) // 拿到sotplist的trainid與id相同的數量
                let traintypename=""
                let stationname=""
                let week="";

                for(let j=0;j<traintypedata["data"].length;j=j+1){
                    if(traintypedata["data"][j][0]==trainlist[i][1]){
                        traintypename=traintypedata["data"][j][1]
                    }
                }

                for(let j=0;j<stationdata["data"].length;j=j+1){
                    if(stationdata["data"][j][0]==stoplistfilter[0][2]){
                        stationname=stationdata["data"][j][2]
                    }
                }

                stoplistfilterdata.push(stoplistfilter)

                if(trainlist[i][3]=="1"){
                    week="一"
                }else if(trainlist[i][3]=="2"){
                    week="二"
                }else if(trainlist[i][3]=="3"){
                    week="三"
                }else if(trainlist[i][3]=="4"){
                    week="四"
                }else if(trainlist[i][3]=="5"){
                    week="五"
                }else if(trainlist[i][3]=="6"){
                    week="六"
                }else{
                    week="日"
                }

                let tr=doccreate("tr")
                tr.innerHTML=`
                    <td class="admintd" rowspan="${stoplistfilter.length}">${trainlist[i][0]}</td>
                    <td class="admintd" rowspan="${stoplistfilter.length}">${trainlist[i][2]}</td>
                    <td class="admintd" rowspan="${stoplistfilter.length}">${traintypename}</td>
                    <td class="admintd" rowspan="${stoplistfilter.length}">${week}</td>
                    <td class="admintd" rowspan="${stoplistfilter.length}">${trainlist[i][4]}</td>
                    <td class="admintraintd">${stationname}</td>
                    <td class="admintraintd">${stoplistfilter[0][4]}</td>
                    <td class="admintraintd">${stoplistfilter[0][5]}</td>
                    <td class="admintraintd">${stoplistfilter[0][3]}</td>
                    <td class="admintd" rowspan="${stoplistfilter.length}">
                        <input type="button" class="bluebutton editbutton" data-id=${i} value="編輯">
                        <input type="button" class="bluebutton" onclick="location.href='api.php?key=deltrain&id=${trainlist[i][0]}'" value="刪除">
                    </td>
                `
                docappendchild("table",tr)

                for(let j=1;j<stoplistfilter.length;j=j+1){
                    let stationname=""
                    let tdclass="admintraintd"

                    for(let k=0;k<stationdata["data"].length;k=k+1){
                        if(stationdata["data"][k][0]==stoplistfilter[j][2]){
                            stationname=stationdata["data"][k][2]
                        }
                    }
                    if(j+1==stoplistfilter.length){
                        tdclass="admintd"
                    }
                    let tr=doccreate("tr")
                    tr.innerHTML=`
                        <td class="${tdclass}">${stationname}</td>
                        <td class="${tdclass}">${stoplistfilter[j][4]}</td>
                        <td class="${tdclass}">${stoplistfilter[j][5]}</td>
                        <td class="${tdclass}">${stoplistfilter[j][3]}</td>
                    `
                    docappendchild("table",tr)
                }
            }

            lightbox(".editbutton","lightbox",function(event){
                let id=event.dataset.id
                let traintype=""
                let week=""

                for(let i=0;i<traintypedata["data"].length;i=i+1){
                    if(traintypedata["data"][i][0]==trainlist[id][1]){
                        traintype=traintype+`<option value="${traintypedata["data"][i][0]}" selected>${traintypedata["data"][i][1]}</option>`
                    }else{
                        traintype=traintype+`<option value="${traintypedata["data"][i][0]}">${traintypedata["data"][i][1]}</option>`
                    }
                }

                for(let i=1;i<=7;i=i+1){
                    let weekdata
                    if(trainlist[id][3]=="1"){
                        weekdata="一"
                    }else if(trainlist[id][3]=="2"){
                        weekdata="二"
                    }else if(trainlist[id][3]=="3"){
                        weekdata="三"
                    }else if(trainlist[id][3]=="4"){
                        weekdata="四"
                    }else if(trainlist[id][3]=="5"){
                        weekdata="五"
                    }else if(trainlist[id][3]=="6"){
                        weekdata="六"
                    }else{
                        weekdata="日"
                    }
                    if(trainlist[id][3]==i){
                        week=week+`<option value="${i}" selected>星期${weekdata}</option>`
                    }else{
                        week=week+`<option value="${i}">星期${weekdata}</option>`
                    }
                }

                setTimeout(function(){
                    for(let j=0;j<stoplistfilterdata[id].length;j=j+1){
                        let station=""
                        let stationid=stoplistfilterdata[id][j][2]

                        for(let k=0;k<stationdata["data"].length;k=k+1){
                            if(stationdata["data"][k][0]==stationid){
                                station=station+`<option value="${stationdata["data"][k][0]}" selected>${stationdata["data"][k][2]}</option>`
                            }else{
                                station=station+`<option value="${stationdata["data"][k][0]}">${stationdata["data"][k][2]}</option>`
                            }
                        }

                        let div=doccreate("div")
                        div.classList.add("station")
                        div.innerHTML=`
                            <div class="stationgrid grid">
                                <select class="stationselect">
                                    <option value="na">請選擇車站</option>
                                    ${station}
                                </select>
                                <input type="text" class="input2 arrivetime" value="${stoplistfilterdata[id][j][4]}" placeholder="抵達時間(HH:MM)">
                                <input type="text" class="input2 stoptime" value="${stoplistfilterdata[id][j][5]}" placeholder="停留時間(MM)">
                                <input type="text" class="input2 price" value="${stoplistfilterdata[id][j][3]}" placeholder="價格">
                                <input type="button" class="noborderbutton stationdelect" value="X">
                            </div>
                        `
                        docappendchild("stationdiv",div)

                        stationcount=stationcount+1
                    }
                    stationdelete()
                    divsort("station",".stationdiv")
                },200)

                return `
                    <form method="POST">
                        <div class="grid trainlightboxgrid">
                            <div class="trainleft">
                                列車id<br>
                                <input type="text" class="lightboxinput" value="${trainlist[id][0]}" readonly><br><br>
                                列車代碼<br>
                                <input type="text" class="lightboxinput" id="code" value="${trainlist[id][2]}"><br><br>
                                車種<br>
                                <select class="select" id="traintype">
                                    <option value="na">請選擇車種</option>
                                    ${traintype}
                                </select><br><br><br>
                                行車星期<br>
                                <select class="select" id="week">
                                    ${week}
                                </select><br><br><br>
                                發車時間<br>
                                <input type="text" class="lightboxinput" id="starttime" value="${trainlist[id][4]}" placeholder="HH:MM">
                            </div>
                            <div class="trainright">
                                行經車站: <input type="button" class="button" onclick="newstation()" value="新增">
                                <div class="stationdiv macossectiondivy" id="stationdiv"></div>
                            </div>
                            <div class="trainbutton">
                                <input type="button" class="button" id="close" value="返回">
                                <input type="reset" class="button" value="清除">
                                <input type="button" class="button" onclick="formsubmit('edit','${trainlist[id][0]}')" value="送出">
                            </div>
                        </div>
                    </form>
                `
            },"close")
        }else{
            alert(data["data"])
        }
    }
},100)

startmacossection()
let traintypedata
let stationdata
let stationcount=0

function main(url){
    oldajax("GET",url).onload=function(){
        let data=JSON.parse(this.responseText)
        if(data["success"]){
            let trainlist=data["data"][0]
            let stoplist=data["data"][1]
            let stoplistfilterdata=[]
            docgetid("table").innerHTML=`
                <tr>
                    <td class="admintd">id</td>
                    <td class="admintd">列車代碼</td>
                    <td class="admintd">車種</td>
                    <td class="admintd">行駛星期</td>
                    <td class="admintd">本週日期</td>
                    <td class="admintd">行經車站</td>
                    <td class="admintd">抵達時間</td>
                    <td class="admintd">發車時間</td>
                    <td class="admintd">訂票</td>
                </tr>
            `

            if(trainlist.length>0){
                for(let i=0;i<trainlist.length;i=i+1){
                    let id=trainlist[i][0]
                    let stoplistfilter=stoplist.filter(function(event){ return event[1]==id }) // 拿到sotplist的trainid與id相同的數量
                    let traintypename=""
                    let stationname=""
                    let week=""
                    let date=new Date()
                    let weekfirst=0-date.getDay()
                    let weeklast=6-date.getDay()
                    let thisweekdate

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

                    if(weekfirst<0){
                        weekfirst=weekfirst+7;
                    }

                    if(weeklast<0){
                        weeklast=weeklast+7;
                    }

                    let tr=doccreate("tr")
                    tr.innerHTML=`
                        <td class="admintd" rowspan="${stoplistfilter.length}">${trainlist[i][0]}</td>
                        <td class="admintd" rowspan="${stoplistfilter.length}">${trainlist[i][2]}</td>
                        <td class="admintd" rowspan="${stoplistfilter.length}">${traintypename}</td>
                        <td class="admintd" rowspan="${stoplistfilter.length}">${week}</td>
                        <td class="admintd" rowspan="${stoplistfilter.length}">${(new Date(date.getTime()+weekfirst*86400000).getMonth()+1).toString().padStart(2,"0")+"/"+new Date(date.getTime()+weekfirst*86400000).getDate().toString().padStart(2,"0")+"~"+(new Date(date.getTime()+weeklast*86400000).getMonth()+1).toString().padStart(2,"0")+"/"+new Date(date.getTime()+weeklast*86400000).getDate().toString().padStart(2,"0")}</td>
                        <td class="admintraintd">${stationname}</td>
                        <td class="admintraintd">-</td>
                        <td class="admintraintd">${stoplistfilter[0][4]}</td>
                        <td class="admintd" rowspan="${stoplistfilter.length}">
                            <input type="button" class="bluebutton buyticket" data-id=${trainlist[i][2]} value="訂票">
                        </td>
                    `
                    docappendchild("table",tr)

                    for(let j=1;j<stoplistfilter.length;j=j+1){
                        let stationname=""
                        let tdclass="admintraintd"
                        let starttime=stoplistfilter[j][4]
                        let laststarttime=stoplistfilter[j-1][4].split(":")
                        let arrivetime

                        for(let k=0;k<stationdata["data"].length;k=k+1){
                            if(stationdata["data"][k][0]==stoplistfilter[j][2]){
                                stationname=stationdata["data"][k][2]
                            }
                        }
                        if(j+1==stoplistfilter.length){
                            tdclass="admintd"
                        }

                        if(j+1==stoplistfilter.length){
                            starttime="-"
                        }

                        arrivetime=[parseInt(laststarttime[0]),parseInt(laststarttime[1])+parseInt(stoplistfilter[j][5])]

                        if(arrivetime[1]>=60){
                            arrivetime[0]=arrivetime[0]+1
                            arrivetime[1]=arrivetime[1]-60
                        }

                        let tr=doccreate("tr")
                        tr.innerHTML=`
                            <td class="${tdclass}">${stationname}</td>
                            <td class="${tdclass}">${arrivetime[0].toString().padStart(2,"0")+":"+arrivetime[1].toString().padStart(2,"0")}</td>
                            <td class="${tdclass}">${starttime}</td>
                        `
                        docappendchild("table",tr)
                    }
                }

                docgetall(".buyticket").forEach(function(event){
                    event.onclick=function(){
                        weblsset("46nationalmoduledtraincode",event.dataset.id)
                        location.href="ticket.html"
                    }
                })
            }else{
                docgetid("table").innerHTML=`
                    <div class="warning">查無相關車輛，請重新搜尋</div>
                `
            }
        }else{
            alert(data["data"])
        }
    }
}

oldajax("GET","/backend/46nationalmoduled/mangertraintype/").onload=function(){ traintypedata=JSON.parse(this.responseText) }
oldajax("GET","/backend/46nationalmoduled/mangerstation/").onload=function(){ stationdata=JSON.parse(this.responseText) }

setTimeout(function(){
    main("/backend/46nationalmoduled/mangertrain/")
},100)

docgetid("submit").onclick=function(){
    if(blank(docgetid("traincode").value)){
        main("/backend/46nationalmoduled/searchtrain/"+docgetid("traincode").value)
    }else{
        main("/backend/46nationalmoduled/mangertrain/")
    }
}

docgetid("traincode").onkeydown=function(event){
    if(event.key=="Enter"){
        docgetid("submit").click()
    }
}

startmacossection()
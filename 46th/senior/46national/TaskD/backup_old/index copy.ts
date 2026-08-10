let traintypedata
let stationdata
let traindata
let station="<option value='all'>所有</option>"
let traintype="<option value='all'>所有</option>"

oldajax("GET","api.php?traintypelist=").onload=function(){ traintypedata=JSON.parse(this.responseText) }
oldajax("GET","api.php?stationlist=").onload=function(){ stationdata=JSON.parse(this.responseText) }
oldajax("GET","api.php?trainlist=").onload=function(){ traindata=JSON.parse(this.responseText) }

setTimeout(function(){
    for(let i=0;i<stationdata.length;i=i+1){
        station=station+`<option value="${stationdata[i][1]}">${stationdata[i][2]}</option>`
    }

    for(let i=0;i<traintypedata.length;i=i+1){
        traintype=traintype+`<option value="${traintypedata[i][0]}">${traintypedata[i][1]}</option>`
    }

    docgetid("start").innerHTML=station
    docgetid("end").innerHTML=station
    docgetid("traintype").innerHTML=traintype
    docgetid("date")

    docgetid("submit").onclick=function(){
        docgetid("start")
        docgetid("end")
        docgetid("traintype")
        docgetid("date")
    }

    startmacossection()
},100)

setTimeout(function(){
    oldajax("GET","api.php?trainlist=").onload=function(){
        let data=JSON.parse(this.responseText)
        let trainlist=data[0]
        let stoplist=data[1]
        let stoplistfilterdata=[]
        for(let i=0;i<trainlist.length;i=i+1){
            let id=trainlist[i][0]
            let stoplistfilter=stoplist.filter(function(event){ return event[1]==id }) // 拿到sotplist的trainid與id相同的數量
            let traintypename=""
            let stationname=""
            let week="";

            for(let j=0;j<traintypedata.length;j=j+1){
                if(traintypedata[j][0]==trainlist[i][1]){
                    traintypename=traintypedata[j][1]
                }
            }

            for(let j=0;j<stationdata.length;j=j+1){
                if(stationdata[j][0]==stoplistfilter[0][2]){
                    stationname=stationdata[j][2]
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
                <td class="td">${traintypename}</td>
                <td class="td">${trainlist[i][2]}</td>
                <td class="td">發車站</td>
                <td class="td">終點站</td>
                <td class="td">預計發車時間</td>
                <td class="td">預計到達時間</td>
                <td class="td">行駛時間</td>
                <td class="td">票價</td>
                <td class="td">
                    <input type="button" class="bluebutton ticketbutton" value="訂票">
                </td>
            `
            docappendchild("table",tr)
        }

        docgetall(".ticketbutton").forEach(function(event){
            event.onclick=function(){  }
        })
    }
},100)

startmacossection()
//<div class="warning">查無相關車輛，請重新搜尋</div>
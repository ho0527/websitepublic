let traincodelist
let trainlist
let traindata
let trainid
let traincode

if(weblsget("46nationalmoduledtraincode")){
    traincode=weblsget("46nationalmoduledtraincode")
}

oldajax("GET","api.php?traincodelist=").onload=function(){ traincodelist=JSON.parse(this.responseText) }
oldajax("GET","/backend/46nationalmoduled/mangertrain/").onload=function(){ trainlist=JSON.parse(this.responseText) }

setTimeout(function(){
    let traincodeinnerhtml="<option value=\"na\">車次代碼</option>"
    for(let i=0;i<traincodelist.length;i=i+1){
        if(traincode&&traincodelist[i]==traincode){
            traincode=i
            traincodeinnerhtml=traincodeinnerhtml+`<option value="${i}" selected>${traincodelist[i]}</option>`
        }else{
            traincodeinnerhtml=traincodeinnerhtml+`<option value="${i}">${traincodelist[i]}</option>`
        }
    }

    docgetid("traincode").innerHTML=`
        ${traincodeinnerhtml}
    `

    if(isset(traincode)){
        let stopinnerhtml=""
        let count=1

        console.log(trainlist["data"])
        trainid=trainlist["data"][0][traincode][0]

        for(let i=0;i<trainlist["data"][1].length;i=i+1){
            if(trainlist["data"][1][i][1]==trainid){
                for(let j=0;j<trainlist["data"][2].length;j=j+1){
                    if(trainlist["data"][2][j][0]==trainlist["data"][1][i][2]){
                        stopinnerhtml=stopinnerhtml+"<option value=\""+trainlist["data"][2][j][0]+"\" data-id='"+j+"'>"+count+". "+trainlist["data"][2][j][2]+"</option>"
                        count=count+1
                    }
                }
            }
        }

        docgetid("start").innerHTML="<option value=\"na\">起程站</option>"+stopinnerhtml
        docgetid("end").innerHTML="<option value=\"na\">終點站</option>"+stopinnerhtml
    }

    docgetid("traincode").onchange=function(){
        if(this.value!="na"){
            let stopinnerhtml=""
            let count=1
            traincode=this.value

            trainid=trainlist["data"][0][traincode][0]

            for(let i=0;i<trainlist["data"][1].length;i=i+1){
                if(trainlist["data"][1][i][1]==trainid){
                    for(let j=0;j<trainlist["data"][2].length;j=j+1){
                        if(trainlist["data"][2][j][0]==trainlist["data"][1][i][2]){
                            stopinnerhtml=stopinnerhtml+"<option value=\""+trainlist["data"][2][j][0]+"\" data-id='"+j+"'>"+count+". "+trainlist["data"][2][j][2]+"</option>"
                            count=count+1
                        }
                    }
                }
            }

            docgetid("start").innerHTML="<option value=\"na\">起程站</option>"+stopinnerhtml
            docgetid("end").innerHTML="<option value=\"na\">終點站</option>"+stopinnerhtml
        }
    }
},100)

docgetid("submit").onclick=function(){
    let phone=docgetid("phone").value
    let date=docgetid("date").value
    let traincode=docgetid("traincode").value
    let start=docgetid("start").value
    let end=docgetid("end").value
    let count=docgetid("count").value
    let day=new Date(date).getDay()
    let success=true
    let error=[]
    let notwrite=""

    if(phone==""){
        notwrite=notwrite+"手機號碼 "
    }

    if(date==""){
        notwrite=notwrite+"乘車日期 "
    }

    if(traincode=="na"){
        notwrite=notwrite+"車次代碼 "
    }

    if(start=="na"){
        notwrite=notwrite+"起程站 "
    }

    if(end=="na"){
        notwrite=notwrite+"終點站 "
    }

    if(count==""){
        notwrite=notwrite+"車票張數 "
    }

    if(notwrite!=""){
        error.push(notwrite+"項目未填寫")
        success=false
    }

    if(isset(traincode)){
        if(day!=trainlist["data"][0][traincode][3]){
            console.log(trainlist["data"][0][traincode])
            error.push("列車日期錯誤 無此班列車")
            success=false
        }
    }

    if(!regexpmatch(count,"^[1-9]([0-9]{0,3})$")){
        error.push("訂票數量錯誤(1~1000)")
        success=false
    }

    if(false){
        error.push("該區間以無空位")
        success=false
    }

    if(date<=new Date()){
        error.push("發車時間已過")
        success=false
    }

    if(docgetid("start").dataset.id>=docgetid("end").dataset.id){
        error.push("起訖站相同或不正確")
        success=false
    }

    if(docgetid("check").style.backgroundColor!="green"){
        error.push("尚未通過驗證碼")
        success=false
    }

    if(success){
        docgetid("error").innerHTML=`` // 清空error區塊

        // 傳送資料
        oldajax("POST","/backend/46nationalmoduled/newticket/",JSON.stringify({
            "trainid": trainid,
            "typeid": trainlist["data"][0][traincode][1],
            "startstationid": start,
            "endstationid": end,
            "phone": phone,
            "count": count,
            "getgodate": date
        }),[
            ["Content-Type","application/json"]
        ]).onload=function(){
            let data=JSON.parse(this.responseText)

            if(data["success"]){
                oldajax("POST","api/ticketsms.php",formdata([
                    ["phone",phone],
                    ["traincode",data["data"]["traincode"]],
                    ["getgodate",date],
                    ["count",count],
                    ["startstation",data["data"]["startstation"]],
                    ["endstation",data["data"]["endstation"]],
                    ["traincode",data["data"]["traincode"]],
                    ["startstop",data["data"]["startstop"]],
                    ["total",data["data"]["total"]]
                ])).onload=function(){
                    let data2=JSON.parse(this.responseText)
                    if(data2["success"]){
                        lightbox(null,"lightbox",function(){
                            return `
                                <h1>訂票成功</h1>
                                <hr>
                                <div class='ticketlist'>
                                    詳細資料如下:<br>
                                    訂票編號: ${data["data"]["traincode"]}<br>
                                    手機號碼: ${phone}<br>
                                    發車時間: ${data["data"]["startstop"]}<br>
                                    車次代碼: ${data["data"]["traincode"]}<br>
                                    起程站: ${data["data"]["startstation"]}<br>
                                    終點站: ${data["data"]["endstation"]}<br>
                                    張數: ${count}<br>
                                    票價: ${data["data"]["price"]}<br>
                                    總價: ${data["data"]["total"]}<br>
                                </div>
                                <input type='button' class='button' onclick='location.reload()' value='返回'>
                            `
                        },clickcolse="none")
                    }
                }
            }else{
                alert(data["data"])
            }
        }
    }else{
        docgetid("error").innerHTML=`
            錯誤內容如下: <br>
            ${error.join(", ")}
        `
    }
}

docgetid("check").onclick=function(){
    docgetid("check").disabled="true"
    docgetid("check").style.backgroundColor="green"
    docgetid("check").style.color="black"
}

startmacossection()
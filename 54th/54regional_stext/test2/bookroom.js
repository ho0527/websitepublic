let year=null
let month=null
let firstday=null
let endday=null
let leftroom=[]

document.querySelectorAll(".date").forEach(function(event){
    event.onclick=function(){
        if(!firstday){
            leftroom=event.dataset.leftroom.split(",")
            if(leftroom.length>parseInt(document.getElementById("roomcount").value)){
                firstday=parseInt(event.dataset.day)
                year=parseInt(event.dataset.year)
                month=parseInt(event.dataset.month)
                event.classList.add("select")
                document.getElementById("roomcount").disabled
                document.getElementById("daycount").value="1"
                document.getElementById("startday").value=event.dataset.year+"/"+event.dataset.month+"/"+event.dataset.day
                document.getElementById("endday").value=event.dataset.year+"/"+event.dataset.month+"/"+event.dataset.day
            }else{
                leftroom=[]
                alert("無空房! 請重新選擇")
            }
        }else if(!endday&&firstday<parseInt(event.dataset.day)){
            let leftroomtemp=[]
            let leftroomtemp2=[]
            console.log(leftroomtemp)
            for(let i=0;i<leftroom.length;i=i+1){
                leftroomtemp.push(leftroom[i])
                leftroomtemp2.push(leftroom[i])
            }
            console.log(leftroomtemp)
            endday=parseInt(event.dataset.day)
            for(let i=firstday;i<=endday;i=i+1){
                let notleftroom=document.getElementById("date_"+i).dataset.notleftroom.split(",")
                for(let j=0;j<leftroomtemp.length;j=j+1){
                    for(let k=0;k<notleftroom.length;k=k+1){
                        if(notleftroom[k]==leftroomtemp[j]){
                            leftroomtemp[j]=""
                        }
                    }
                }
            }
            console.log(leftroomtemp)
            leftroom=[]
            for(let i=0;i<leftroomtemp.length;i=i+1){
                if(leftroomtemp[i]!=""){
                    leftroom.push(leftroomtemp[i])
                }
            }
            if(leftroom.length>parseInt(document.getElementById("roomcount").value)){
                for(let i=firstday;i<=endday;i=i+1){
                    document.getElementById("date_"+i).classList.add("select")
                }
                document.getElementById("roomcount").disabled
                document.getElementById("daycount").value=endday-firstday+1
                document.getElementById("endday").value=event.dataset.year+"/"+event.dataset.month+"/"+event.dataset.day
            }else{
                leftroom=leftroomtemp2
                endday=null
                alert("無空房! 請重新選擇")
            }
        }else{
            alert("請先取消選擇")
        }
    }
})

document.getElementById("autoselect").onclick=function(){
    if(firstday){
        document.getElementById("roomno").value="Room"+leftroom[(Math.floor(Math.random()*leftroom.length))]
    }else{
        alert("請先選擇房間")
    }
}

document.getElementById("select").onclick=function(){
    if(firstday){
        document.getElementById("selecttitle").innerHTML=`${year}-${month}-${firstday}`
        document.getElementById("title").innerHTML=`選擇房間`
        document.getElementById("bookroomright").style.display="none"
        document.getElementById("bookroomleft").style.display="none"
        document.getElementById("selectdiv").style.display="block"
        document.querySelectorAll(".selectbutton").forEach(function(event){
            if(event.id==document.getElementById("roomno").value){
                event.classList.add("selectdivselect")
            }
            if(event.id==document.getElementById("roomno").value){
                event.classList.add("selectdivselect")
            }
        })

        document.getElementById("finish").onclick=function(){
            document.getElementById("roomno").value="Room"
        }

        document.getElementById("clear").onclick=function(){
            document.querySelectorAll(".selectbutton").forEach(function(event){
                if(event.id==document.getElementById("roomno").value){
                    event.classList.remove("selectdivselect")
                }
            })
        }

        document.getElementById("back").onclick=function(){
            document.getElementById("title").innerHTML=`選擇訂房資訊`
            document.getElementById("bookroomright").style.display="block"
            document.getElementById("bookroomleft").style.display="block"
            document.getElementById("selectdiv").style.display="none"
        }
    }else{
        alert("請先選擇房間")
    }
}

document.getElementById("check1").onclick=function(){
    document.getElementById("title").innerHTML=`已選擇的訂房資訊再確認`
    document.getElementById("bookroomright").style.display="none"
    document.getElementById("bookroomleft").style.display="none"
    document.getElementById("checkdiv").style.display="block"
    document.getElementById("checkdiv").innerHTML=`
        訂房間數: <input type="text" class="noinput" value="1" disabled><br>
        入住天數: <input type="text" class="noinput" value="${document.getElementById("daycount").value}" disabled><br>
        入住日期: <input type="text" class="noinput" value="${document.getElementById("startday").value}" disabled> ~ <input type="text" class="noinput" value="${document.getElementById("endday").value}" disabled><br>
        房號: <input type="text" class="noinput" value="${document.getElementById("roomno").value}" disabled><br>
        總金額: <input type="text" class="noinput" value="${(endday-firstday+1)*5000}" disabled><br>
        需付討金: <input type="text" class="noinput" value="${((endday-firstday+1)*5000)*0.3}" disabled><br>
        <div class="tc">
            <input type="button" id="checkback" value="取消">
            <input type="button" id="check2" value="確定訂房">
        </div>
    `
    document.getElementById("checkback").onclick=function(){
        document.getElementById("title").innerHTML=`選擇訂房資訊`
        document.getElementById("bookroomright").style.display="block"
        document.getElementById("bookroomleft").style.display="block"
        document.getElementById("checkdiv").style.display="none"
    }
}
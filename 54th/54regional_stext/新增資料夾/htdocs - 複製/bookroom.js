let roomcount=1
let datecount=0
let startdate=null
let enddate=null
let canbookroom=[]
let bookroomlist=[]

function domgetid(id){
    if(document.getElementById(id)){
        return document.getElementById(id)
    }else{
        console.warn("no this element")
    }
}

function randomroom(){
    if(startdate!=null){
        let canbookroomtemp=[...canbookroom]

        bookroomlist=[]
        for(let i=0;i<parseInt(roomcount);i=i+1){
            let random=parseInt(Math.random()*canbookroomtemp.length)
    
            bookroomlist.push(canbookroomtemp[random])
            canbookroomtemp.splice(random,1)
        }
        bookroomlist.sort()
    
        domgetid("roomno").value="Room"+bookroomlist.join(",Room")
    }else{
        alert("請選擇日期")
    }
}

document.querySelectorAll(".calendardate").forEach(function(event){
    event.onclick=async function(){
        let day=event.dataset.day
        let date=parseInt(event.dataset.date)

        roomcount=domgetid("roomcount").value
        if(startdate==null){
            canbookroom=event.dataset.leftroom.split(",")

            if(roomcount<=canbookroom.length){
                startdate=date
                localStorage.setItem("startdate",date)
                domgetid("startdate").value=day
                randomroom()
            }else{
                canbookroom=[]
                alert("房間數量不足")
                return
            }
        }else if(enddate==startdate&&startdate<date){
            let res=await fetch("api.php?leftroom=&startdate="+domgetid("startdate").value+"&enddate="+day)
            canbookroom=await res.json()
            if(roomcount<=canbookroom.length){
                domgetid("selectroom").disabled="true"
                randomroom()
            }else{
                alert("房間數量不足")
                return
            }
        }else{
            alert("請先取消選擇日期在選擇")
            return
        }

        domgetid("enddate").value=day
        domgetid("datecount").value=enddate-startdate+1
        domgetid("roomcount").disabled=true

        enddate=date
        datecount=enddate-startdate+1
        localStorage.setItem("enddate",date)

        for(let i=startdate;i<=enddate;i=i+1){
            domgetid("date_"+i).style.backgroundColor="orange"
            domgetid("date_"+i).style.color="white"
        }
    }
})

domgetid("selectroom").onclick=function(){
    if(startdate!=null){
        let bookroomcount=0
        let innerhtml=``

        domgetid("titletext").innerHTML=`選擇房間`
        domgetid("main").style.display="none"
        domgetid("selectroomdiv").style.display="block"
        domgetid("check").style.display="none"
        // $("#selectroomdiv").css({
        //     display:"block"
        // }).html("")

        for(let i=1;i<=8;i=i+1){ 
            if(bookroomlist.includes((String(i)))){
                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="roomselectbutton roomselectbuttonlight" id="roombutton_${i}" data-id="${i}" value="Room${i}(空房)">
                `
            }else if(canbookroom.includes(String(i))){
                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="roomselectbutton" id="roombutton_${i}" data-id="${i}" value="Room${i}(空房)">
                `
            }else{
                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="disabled light" id="roombutton_${i}" data-id="${i}" value="Room${i}(已訂)">
                `
            }
        }

        domgetid("selectroomdiv").innerHTML=`
            ${innerhtml}
            <div class="leftroomtext">還剩 <span id="leftroom">0</span> 間房可選</div>
            <div class="textcenter">
                <input type="button" class="button" id="closeselectbutton" value="返回">
                <input type="button" class="button" id="clearselectbutton" value="清除">
                <input type="button" class="button" id="submitselectbutton" value="確定">
            </div>
        `

        document.querySelectorAll(".roomselectbutton").forEach(function(event2){
            event2.onclick=function(){
                if(event2.classList.contains("roomselectbuttonlight")){
                    event2.classList="roomselectbutton"
                    bookroomcount=bookroomcount+1
                }else{
                    if(bookroomcount>0){
                        event2.classList.add("roomselectbuttonlight")
                        bookroomcount=bookroomcount-1
                    }else{
                        alert("沒有房間可選了")
                    }
                }
                domgetid("leftroom").innerHTML=bookroomcount
            }
        })

        domgetid("closeselectbutton").onclick=function(){
            domgetid("titletext").innerHTML=`選擇訂房資訊`
            domgetid("main").style.display="flex"
            domgetid("selectroomdiv").style.display="none"
            domgetid("check").style.display="none"
        }

        domgetid("clearselectbutton").onclick=function(){
            bookroomcount=roomcount
            document.querySelectorAll(".roomselectbutton").forEach(function(event2){
                event2.classList="roomselectbutton"
            })
            domgetid("leftroom").innerHTML=roomcount
        }

        domgetid("submitselectbutton").onclick=function(){
            if(bookroomcount==0){
                bookroomlist=[]

                document.querySelectorAll(".roomselectbuttonlight").forEach(function(event3){
                    bookroomlist.push(event3.dataset.id)
                })

                domgetid("titletext").innerHTML=`選擇訂房資訊`
                domgetid("main").style.display="flex"
                domgetid("selectroomdiv").style.display="none"
                domgetid("check").style.display="none"
                domgetid("roomno").value="Room"+bookroomlist.join(",Room")
            }else{
                alert("還需要選 "+bookroomcount+" 間房")
            }
        }
    }else{
        alert("請先取消選擇日期在選擇")
    }
}

domgetid("submit").onclick=function(){
    if(startdate){
        domgetid("titletext").innerHTML=`已選擇的訂房資訊在確認`
        domgetid("main").style.display="none"
        domgetid("selectroomdiv").style.display="none"
        domgetid("check").style.display="block"
    
        domgetid("check").innerHTML=`
            訂房間數: <input type="text" class="notext" value="${roomcount}" disabled><br><br>
            入住天數: <input type="text" class="notext" value="${datecount}" disabled><br><br>
            入住日期: <input type="text" class="notext" value="${domgetid("startdate").value}~${domgetid("enddate").value}" disabled><br><br>
            房間號碼: <input type="text" class="notext" value="${"Room"+bookroomlist.join(",")}" disabled><br><br>
            總 價 格: <input type="text" class="notext" value="${parseInt(roomcount)*parseInt(datecount)*5000}" disabled><br><br>
            需付訂金: <input type="text" class="notext" value="${parseInt(roomcount)*parseInt(datecount)*5000*0.3} (總價格30%)" disabled><br><br>
            <div class="textcenter">
                <input type="button" class="button" id="close" value="取消">
                <input type="button" class="button" id="checksubmit" value="送出">
            </div>
        `

        domgetid("close").onclick=function(){
            domgetid("titletext").innerHTML=`選擇訂房資訊`
            domgetid("main").style.display="flex"
            domgetid("selectroomdiv").style.display="none"
            domgetid("check").style.display="none"
        }

        domgetid("checksubmit").onclick=function(){
            localStorage.setItem("roomcount",roomcount)
            localStorage.setItem("datecount",datecount)
            localStorage.setItem("bookroomlist",bookroomlist.join(","))
            localStorage.setItem("price",roomcount*datecount*5000)
            localStorage.setItem("deposit",roomcount*datecount*5000*0.3)
            location.href="bookroomsubmit.php"
        }
    }else{
        alert("請先選擇日期")
    }
}
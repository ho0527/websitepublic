function load(){
    if(localStorage.getItem("book")){
        if(JSON.parse(localStorage.getItem("book"))["enddate"]!=null){
            book=JSON.parse(localStorage.getItem("book"))
            book["totaldate"]=book["enddate"]-book["startdate"]+1
            book["totalprice"]=book["totaldate"]*book["roomcount"]*5000
            document.getElementById("totaldate").innerHTML=book["totaldate"]
            document.getElementById("startday").innerHTML=book["startday"]
            document.getElementById("endday").innerHTML=book["endday"]
            document.getElementById("room").innerHTML="Room"+book["bookroom"].join("，Room")

            document.getElementById("roomcount").disabled="true"
            for(let i=book["startdate"];i<=book["enddate"];i=i+1){
                if(document.getElementById(i)){
                    document.getElementById(i).classList.add("bookselect")
                }
            }
        }
    }
}

function auto(){
    if(book["enddate"]!=null){
        let canbookroom=[...book["canbookroom"]]

        book["bookroom"]=[]
        for(let i=0;i<book["roomcount"];i=i+1){
            let rand=Math.floor(Math.random()*canbookroom.length)

            book["bookroom"].push(canbookroom[rand])
            canbookroom.splice(rand,1)
        }
        book["bookroom"].sort()

        localStorage.setItem("book",JSON.stringify(book))
        localStorage.setItem("id",id)
        load()
    }else{
        alert("請選擇日期")
    }
}

if(!localStorage.getItem("reload")){
    book["totaldate"]=book["enddate"]-book["startdate"]+1
    book["totalprice"]=book["totaldate"]*book["roomcount"]*5000
    document.getElementById("totaldate").innerHTML=book["totaldate"]
    document.getElementById("startday").innerHTML=book["startday"]
    document.getElementById("endday").innerHTML=book["endday"]
    document.getElementById("room").innerHTML="Room"+book["bookroom"].join("，Room")

    document.getElementById("roomcount").disabled="true"
    for(let i=book["startdate"];i<=book["enddate"];i=i+1){
        if(document.getElementById(i)){
            document.getElementById(i).classList.add("bookselect")
        }
    }
}else{
    book={
        "roomcount": 1,
        "totaldate": 0,
        "totalprice": 0,
        "startdate": null,
        "enddate": null,
        "startday": null,
        "endday": null,
        "canbookroom": [],
        "bookroom": []
    }
    load()
}

for(let i=0;i<book["bookroom"].length;i=i+1){
    book["bookroom"][i]=parseInt(book["bookroom"][i])
}

for(let i=0;i<book["canbookroom"].length;i=i+1){
    book["canbookroom"][i]=parseInt(book["canbookroom"][i])
}

document.getElementById("roomcount").onchange=function(){
    book["roomcount"]=document.getElementById("roomcount").value
    localStorage.setItem("book",JSON.stringify(book))
    localStorage.setItem("id",id)
}

document.querySelectorAll(".bookroomdate").forEach(function(event){
    event.onclick=async function(){
        let date=parseInt(event.id)
        let day=event.dataset.day

        if(book["enddate"]==null||(book["totaldate"]<2&&book["startdate"]<date)){
            if(!book["startdate"]){
                book["startday"]=day
                book["startdate"]=parseInt(event.id)
            }

            let request=await fetch("api.php?getleftroom=&startday="+book["startday"]+"&endday="+day)
            let canbookroom=await request.json()

            if(canbookroom.length>=book["roomcount"]){
                book["canbookroom"]=canbookroom
                book["endday"]=day
                book["enddate"]=parseInt(event.id)

                localStorage.setItem("book",JSON.stringify(book))
                localStorage.setItem("id",id)
                load()
                auto()
            }else{
                alert("無空房")
            }
        }else{
            alert("請先取消選擇在選擇")
        }
    }
})

document.getElementById("submit").onclick=function(){
    if(book["enddate"]!=null){
        document.getElementById("title2").innerHTML=`以選擇的訂房資訊在確認` 
        document.getElementById("main").style.display="none"
        document.getElementById("main2").style.display="block"
        
        document.getElementById("main2").innerHTML=`
            訂房間數: ${book["roomcount"]}<br><br>
            <div class="margin-5px0px">
                入住天數
                <div>${book["totaldate"]}</div>
            </div><br>
            <div class="margin-5px0px">
                入住日期
                <div>${book["startday"]}</div>
            </div>
            <div class="margin-5px0px">
                ~
                <div>${book["endday"]}</div>
            </div><br>
            <div class="margin-5px0px">
                房號<div>Room${book["bookroom"].join(",Room")}</div>
            </div><br>
            <div class="margin-5px0px">
                總金額
                <div>${book["totalprice"]}</div>
            </div><br>
            <div class="margin-5px0px">
                需付討金
                <div>${book["totalprice"]*0.3}</div>
            </div><br>
            <div class="text-center">
                <input type="button" class="btn btn-light" id="cancel" value="取消">
                <input type="button" class="btn btn-warning" onclick="location.href='editbookroomsubmit.php'" value="確定">
            </div>
        `
    
        document.getElementById("cancel").onclick=function(){
            document.getElementById("title2").innerHTML=`選擇訂房資訊`
            document.getElementById("main").style.display="flex"
            document.getElementById("main2").style.display="none"
        }
    }else{
        alert("請選擇日期")
    }
}


document.getElementById("select").onclick=function(){
    if(book["enddate"]!=null){
        let innerhtml=``
        let count=book["roomcount"]

        document.getElementById("title2").innerHTML=`選擇房間`
        document.getElementById("main").style.display="none"
        document.getElementById("main2").style.display="block"

        for(let i=1;i<=8;i=i+1){
            if(book["bookroom"].includes(i)){
                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="btn btn-warning roombtn" data-id="${i}" value="Room${i}空房">
                `
            }else if(book["canbookroom"].includes(i)){
                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="btn btn-light roombtn" data-id="${i}" value="Room${i}空房">
                `
            }else{
                innerhtml=`
                    ${innerhtml}
                    <input type="button" class="btn btn-danger" data-id="${i}" value="Room${i}已訂" disabled>
                `
            }
        }
        
        document.getElementById("main2").innerHTML=`
            ${innerhtml}<br>
            <div class="text-center">
                <input type="button" class="btn btn-light" id="back" value="放棄離開">
                <input type="button" class="btn btn-light" id="cancel" value="取消">
                <input type="button" class="btn btn-warning" id="yes" value="確定">
            </div>
        `

        document.querySelectorAll(".roombtn").forEach(function(event){
            event.onclick=function(){
                if(event.classList.contains("btn-warning")){
                    event.classList="btn btn-light roombtn"
                    count=count-1
                }else{
                    if(count<book["roomcount"]){
                        event.classList="btn btn-warning roombtn"
                        count=count+1
                    }else{
                        alert("沒有房間可以選了")
                    }
                }
            }
        })
        document.getElementById("back").onclick=function(){
            document.getElementById("title2").innerHTML=`選擇訂房資訊`
            document.getElementById("main").style.display="flex"
            document.getElementById("main2").style.display="none"
        }
    
        document.getElementById("cancel").onclick=function(){
            document.querySelectorAll(".btn-warning.roombtn").forEach(function(event){
                event.classList="btn btn-light roombtn"
                count=0
            })
        }

        document.getElementById("yes").onclick=function(){
            if(count==book["roomcount"]){
                book["bookroom"]=[]
                document.querySelectorAll(".btn-warning.roombtn").forEach(function(event){
                    book["bookroom"].push(parseInt(event.dataset.id))
                })
                book["bookroom"].sort()

                document.getElementById("title2").innerHTML=`選擇訂房資訊`
                document.getElementById("main").style.display="flex"
                document.getElementById("main2").style.display="none"

                localStorage.setItem("book",JSON.stringify(book))
                localStorage.setItem("id",id)
                load()
            }else{
                alert("還要選 "+(book["roomcount"]-count)+" 間房要選")
            }
        }
    }else{
        alert("請選擇日期")
    }
} 
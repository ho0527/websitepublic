let book={
    "roomcount": 1,
    "totaldate": 0,
    "totalprice": 0,
    "startdate": null,
    "enddate": null,
    "startday": null,
    "endday": null,
    "canbookroom": [],
    "bookroomlist": []
}

function load(){
    if(localStorage.getItem("book")){
        if(JSON.parse(localStorage.getItem("book"))["enddate"]!=null){
            book=JSON.parse(localStorage.getItem("book"))
            book["totaldate"]=book["enddate"]-book["startdate"]+1
            book["totalprice"]=(book["enddate"]-book["startdate"]+1)*5000
            document.getElementById("totaldate").innerHTML=book["totaldate"]
            document.getElementById("startday").innerHTML=book["startday"]
            document.getElementById("endday").innerHTML=book["endday"]
            document.getElementById("room").innerHTML="Room"+book["bookroomlist"].join("，Room")
            for(let i=book["startdate"];i<=book["enddate"];i=i+1){
                document.getElementById(i).classList.add("bookselect")
            }
        }
    }
}

function auto(){
    if(book["startdate"]!=null){
        let canbookroom=[...book["canbookroom"]]

        book["bookroomlist"]=[]
        let rand=Math.floor(Math.random()*canbookroom.length)
        book["bookroomlist"].push(canbookroom[rand])
        localStorage.setItem("book",JSON.stringify(book))
        load()
    }else{
        alert("請選擇房間")
    }
}

load()

document.querySelectorAll(".bookroomleftdate").forEach(function(event){
    event.onclick=async function(){
        let date=parseInt(event.id)
        let day=event.dataset.day

        if(book["startdate"]==null||(2>book["totaldate"]&&date>book["startdate"])){
            if(!book["startdate"]){
                book["startdate"]=date
                book["startday"]=day
            }

            let request=await fetch("api.php?getleftroom=&startday="+book["startday"]+"&endday="+day)
            let canbookroom=await request.json()

            if(canbookroom.length>=book["roomcount"]){
                book["canbookroom"]=canbookroom
                book["enddate"]=date
                book["endday"]=day
                localStorage.setItem("book",JSON.stringify(book))
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
    document.getElementById("title2").innerHTML=`以選擇的訂房資訊在確認`
    document.getElementById("main").style.display="none"
    document.getElementById("main2").style.display="block"
    
    document.getElementById("main2").innerHTML=`
        訂房間數: 1<br><br>
        <div class="margin-5px0px">
            入住天數
            <div>${document.getElementById("totaldate").innerHTML}</div>
        </div><br>
        <div class="margin-5px0px">
            入住日期
            <div>${document.getElementById("startday").innerHTML}</div>
        </div>
        <div class="margin-5px0px">
            ~
            <div>${document.getElementById("endday").innerHTML}</div>
        </div><br>
        <div class="margin-5px0px">
            房號
            <div>${document.getElementById("room").innerHTML}</div>
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
            <input type="button" class="btn btn-warning" onclick="location.href='bookroomsubmit.php'" value="確定">
        </div>
    `

    document.getElementById("cancel").onclick=function(){
        document.getElementById("title2").innerHTML=`選擇訂房資訊`
        document.getElementById("main").style.display="flex"
        document.getElementById("main2").style.display="none"
    }
}